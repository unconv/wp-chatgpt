<?php
namespace Unconv\WPChatGPT;

class ImageAPI
{
    function __construct(
        protected string $api_key
    ) {}

    function create_image( string $desciption ): string {    
        error_log( "Sending ImageAPI request" );
        $ch = curl_init( "https://api.openai.com/v1/images/generations" );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->api_key
        ] );
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( [
            "prompt" => $desciption,
            "n" => 1,
            "size" => "1024x1024",
        ] ) );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    
        $response = curl_exec( $ch );
        error_log( "ImageAPI request sent" );
        
        $json = json_decode( $response );
        
        if( isset( $json->data[0]->url ) ) {
            return $json->data[0]->url;
        }
    
        error_log( sprintf( "Error in OpenAI request: %s", $response ) );
        throw new \Exception( "Error in OpenAI request" );
    }
}
