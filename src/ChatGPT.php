<?php
namespace Unconv\WPChatGPT;

class ChatGPT
{
    protected array $message_history = [];
    protected string $system_message;

    function __construct(
        protected string $api_key
    ) {}

    function add_message_history( array $message_history ) {
        $this->message_history = $message_history;
    }

    public function set_system_message( string $system_message ) {
        $this->system_message = $system_message;
    }

    function send_message( string $message ): string {    
        $messages = [];
    
        if( isset( $this->system_message ) ) {
            $messages[] = [
                "role" => "system",
                "content" => $this->system_message,
            ];
        }
    
        $messages = array_merge( $messages, $this->message_history );
    
        $messages[] = [
            "role" => "user",
            "content" => $message,
        ];
    
        return $this->make_api_request( $messages );
    }

    protected function make_api_request( array $messages ): string {
        error_log( "Sending ChatGPT API request" );
        $ch = curl_init( "https://api.openai.com/v1/chat/completions" );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->api_key
        ] );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, '{
            "model": "gpt-3.5-turbo",
            "messages": '.json_encode( $messages ).'
        }' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    
        $response = curl_exec( $ch );
        error_log( "ChatGPT API request sent" );
        
        $json = json_decode( $response );
        
        if( isset( $json->choices[0]->message->content ) ) {
            return $json->choices[0]->message->content;
        }
    
        error_log( sprintf( "Error in OpenAI request: %s", $response ) );
        throw new \Exception( "Error in OpenAI request" );
    }
}
