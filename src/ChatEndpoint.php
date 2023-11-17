<?php
namespace Unconv\WPChatGPT;

use WP_REST_Request;
use WP_REST_Response;

class ChatEndpoint
{
    private static ChatEndpoint $instance;

    private function __construct(
        private ChatGPT $chat_gpt,
        private PageLookup $page_lookup,
        private InformationLookup $information_lookup,
    ) {
        add_action( 'rest_api_init', function() {
            register_rest_route( 'wpgpt/v1', '/send-message/', array(
                'methods' => 'POST',
                'callback' => [$this, "parse_request"],
            ) );
        } );
    }

    public static function init(
        ChatGPT $chat_gpt,
        PageLookup $page_lookup,
        InformationLookup $information_lookup,
    ) {
        if( ! isset( self::$instance ) ) {
            self::$instance = new self( ...func_get_args() );
        }

        return self::$instance;
    }

    public function parse_request( WP_REST_Request $request ) {
        $message = $request->get_param( 'message' );
        $message_history = $request->get_param( 'message_history' );

        $this->chat_gpt->add_message_history( $message_history );

        $response = "NOT_FOUND";
        $exclude = [];
        $limit = 0;

        while( strpos( $response, "NOT_FOUND" ) !== false ) {
            $find_page = $this->page_lookup->find_page( $message, $exclude );

            error_log( $find_page );
            $page_id = json_decode( $find_page, true )["id"] ?? null;

            if( $page_id === null ) {
                $response = $find_page;
            } else {
                $response = $this->information_lookup->find_info( $message, $page_id );
                $exclude[] = $page_id;
            }

            if( $limit++ > 4 ) {
                $response = "I'm sorry, but I can't find that information.";
                break;
            }
        }

        $response_data = [
            'message' => $response,
        ];

        return new WP_REST_Response( $response_data, 200 );
    }
}
