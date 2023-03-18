<?php
/**
 * Plugin Name: WordPress ChatGPT
 * Version: 1.0.0
 * Author: Unconventional Coding
 * Description: Adds ChatGPT to the website
 */

require( __DIR__ . "/autoload.php" );

$api_key = require( __DIR__ . "/openai_key.php" );

Unconv\WPChatGPT\Plugin::init( $api_key );
