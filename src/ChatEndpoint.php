<?php
namespace Unconv\WPChatGPT;

use WP_REST_Request;
use WP_REST_Response;

class ChatEndpoint
{
    private static ChatEndpoint $instance;

    private function __construct(
        private ChatGPT $chat_gpt
    ) {
        add_action( 'rest_api_init', function() {
            register_rest_route( 'wpgpt/v1', '/send-message/', array(
                'methods' => 'POST',
                'callback' => [$this, "parse_request"],
            ) );
        } );
    }

    public static function init( ChatGPT $chat_gpt ) {
        if( ! isset( self::$instance ) ) {
            self::$instance = new self( ...func_get_args() );
        }

        return self::$instance;
    }

    public function parse_request( WP_REST_Request $request ) {
        $message = $request->get_param( 'message' );
        $message_history = $_SESSION['message_history'] ?? [];

        $this->chat_gpt->add_message_history( $message_history );

        $site_data = $this->get_site_data();

        $system_message = "You are an assistant on a website. Here's the website content. Answer questions about it as best as you can. When the user refers to 'you', they mean the company who owns the website. Answer questions as a company employee.\n\n##\n" . json_encode( $site_data, JSON_PRETTY_PRINT ) . "\n##";

        $this->chat_gpt->set_system_message( $system_message );
        $response = $this->chat_gpt->send_message( $message, true );

        $response_data = [
            'message' => $response,
        ];

        return new WP_REST_Response( $response_data, 200 );
    }

    private function get_site_data(): array {
        $pages = get_pages();
        $site_data = [];

        foreach( $pages as $page ) {
            $content = strip_tags( get_the_content( post: $page->ID ) );

            $site_data[] = [
                "id" => $page->ID,
                "name" => $page->post_title,
                "content" => $content,
            ];
        }

        return $site_data;
    }
}
