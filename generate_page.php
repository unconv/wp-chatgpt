<?php
/**
 * This script is used to generate the HTML for a new page
 * based on the given site description and page name.
 *
 * It will generate both texts and images.
 */

use Unconv\WPChatGPT\ChatGPT;
use Unconv\WPChatGPT\Generator\PageGenerator;
use Unconv\WPChatGPT\ImageAPI;
use Unconv\WPChatGPT\Template\HomePage;

$site_description = "A car repair service";
$page_name = "Home";
$template_name = HomePage::class;

require( __DIR__ . "/autoload.php" );

$api_key = require( __DIR__ . "/openai_key.php" );

// set template to be used
$template = new $template_name(
    new ImageAPI( $api_key )
);

$generator = new PageGenerator(
    $site_description,
    new ChatGPT( $api_key )
);

$html = $generator->generate( $page_name, $template );

echo $html;
