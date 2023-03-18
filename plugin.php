<?php
/**
 * Plugin Name: WordPress ChatGPT
 * Version: 1.0.0
 * Author: Unconventional Coding
 * Description: Adds ChatGPT to the website
 */

wp_enqueue_style( "wpgpt_style", plugins_url( "assets/css/style.css", __FILE__ ) );

wp_enqueue_script( "wpgpt_script", plugins_url( "assets/js/script.js", __FILE__ ), [
    "jquery"
] );

// add chatbox on page
add_action( "wp_footer", function() {
    ?>
    <button class="wpgpt-toggle">Toggle</button>
    <div class="wpgpt-chatbox">
        <div class="wpgpt-chat-messages">
            <div class="wpgpt-chat-message assistant">
                Hello! I am your assistant.
            </div>
        </div>
        <div class="wpgpt-chat-input-wrapper">
            <textarea class="wpgpt-chat-input"></textarea>
            <button class="wpgpt-send">Send</button>
        </div>
    </div>
    <?php
} );

// register ChatGPT API endpoint
add_action( 'rest_api_init', 'register_custom_api_endpoint' );
function register_custom_api_endpoint() {
    register_rest_route( 'wpgpt/v1', '/send-message/', array(
        'methods' => 'POST',
        'callback' => 'wpgpt_chatgpt_endpoint',
    ) );
}

// send message to ChatGPT and get response
function wpgpt_chatgpt_endpoint( WP_REST_Request $request ) {
    $message = $request->get_param( 'message' );
    $message_history = $request->get_param( 'message_history' );

    $response = wpgpt_send_message_to_chatgpt( $message, $message_history );

    $response_data = [
        'message' => $response,
    ];

    return new WP_REST_Response( $response_data, 200 );
}

// ChatGPT API
function wpgpt_send_message_to_chatgpt( string $message, array $message_history = [] ): string {
    $openai_key = require __DIR__ . "/openai_key.php";

    $messages = [];

    // add an optional system message
    //$messages[] = [
    //    "role" => "system",
    //    "content" => ""
    //];

    $messages = array_merge( $messages, $message_history );

    $messages[] = [
        "role" => "user",
        "content" => $message,
    ];

    $ch = curl_init( "https://api.openai.com/v1/chat/completions" );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $openai_key
    ] );
    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, '{
        "model": "gpt-3.5-turbo",
        "messages": '.json_encode( $messages ).'
    }' );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    $response = curl_exec( $ch );

    $json = json_decode( $response );
    
    if( isset( $json->choices[0]->message->content ) ) {
        return $json->choices[0]->message->content;
    }

    error_log( sprintf( "Error in OpenAI request: %s", $response ) );
    throw new \Exception( "Error in OpenAI request" );
}
