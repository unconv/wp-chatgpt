<?php
namespace Unconv\WPChatGPT;

use WP_REST_Request;
use WP_REST_Response;

class ChatEndpoint
{
    private static ChatEndpoint $instance;

    private function __construct(
        private ChatGPT $chat_gpt,
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
            self::$instance = new self( $chat_gpt );
        }

        return self::$instance;
    }

    public function parse_request( WP_REST_Request $request ) {
        $message = $request->get_param( 'message' );
        $message_history = $request->get_param( 'message_history' );
    
        $this->chat_gpt->add_message_history( $message_history );
        $response = $this->chat_gpt->send_message( $message );
    
        $response_data = [
            'message' => $response,
        ];
    
        return new WP_REST_Response( $response_data, 200 );
    }
}
