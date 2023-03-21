<?php
namespace Unconv\WPChatGPT\Template;
use Unconv\WPChatGPT\ImageAPI;

class HomePage implements TemplateInterface
{
    public function __construct(
        private ImageAPI $image_api,
    ) {}

    public function get_json(): string {
        return str_replace( [' ', "\n"], '', '{
    "hero": {
        "heading": "",
        "link_text1": "",
        "link_text2": "",
        "paragraph": "",
        "background_image_description": ""
    },
    "columns": {
        "heading1": "",
        "paragraph1": "",
        "heading2": "",
        "paragraph2": "",
        "heading3": "",
        "paragraph3": ""
    },
    "block1": {
        "heading": "",
        "link_text": "",
        "paragraph": "",
        "image_description": ""
    },
    "block2": {
        "heading": "",
        "link_text": "",
        "paragraph": "",
        "image_description": ""
    },
    "block3": {
        "heading": "",
        "link_text": "",
        "paragraph": "",
        "image_description": ""
    },
    "featured": {
        "heading": "",
        "image_description1": "",
        "image_description2": ""
    },
    "reviews": {
        "reviewer_face_description1": "",
        "review_quote1": "",
        "reviewer_name1": "",
        "reviewer_face_description2": "",
        "review_quote2": "",
        "reviewer_name2": "",
        "reviewer_face_description3": "",
        "review_quote3": "",
        "reviewer_name3": ""
    },
    "footer": {
        "heading": "",
        "link_text": "",
        "paragraph": ""
    }
}' );
    }

    public function create( string $json_data ): string {
        $html = "";

        $data = json_decode( $json_data );

        $hero = file_get_contents( __DIR__ . "/../../templates/hero.html" );
        $hero = str_replace( [
            "{heading}",
            "{link_text1}",
            "{link_text2}",
            "{paragraph}",
            "{background_image_description}",
        ], [
            $data->hero->heading,
            $data->hero->link_text1,
            $data->hero->link_text2,
            $data->hero->paragraph,
            $this->image_api->create_image( $data->hero->background_image_description ),
        ], $hero );

        $html .= $hero;

        $columns = file_get_contents( __DIR__ . "/../../templates/columns.html" );
        $columns = str_replace( [
            "{heading1}",
            "{paragraph1}",
            "{heading2}",
            "{paragraph2}",
            "{heading3}",
            "{paragraph3}",
        ], [
            $data->columns->heading1,
            $data->columns->paragraph1,
            $data->columns->heading2,
            $data->columns->paragraph2,
            $data->columns->heading3,
            $data->columns->paragraph3,
        ], $columns );

        $html .= $columns;

        $block1 = file_get_contents( __DIR__ . "/../../templates/block-image-left.html" );
        $block1 = str_replace( [
            "{heading}",
            "{link_text}",
            "{paragraph}",
            "{image_description}",
        ], [
            $data->block1->heading,
            $data->block1->link_text,
            $data->block1->paragraph,
            $this->image_api->create_image( $data->block1->image_description ),
        ], $block1 );

        $html .= $block1;

        $block2 = file_get_contents( __DIR__ . "/../../templates/block-image-right.html" );
        $block2 = str_replace( [
            "{heading}",
            "{link_text}",
            "{paragraph}",
            "{image_description}",
        ], [
            $data->block2->heading,
            $data->block2->link_text,
            $data->block2->paragraph,
            $this->image_api->create_image( $data->block2->image_description ),
        ], $block2 );

        $html .= $block2;

        $block3 = file_get_contents( __DIR__ . "/../../templates/black-block-image-left.html" );
        $block3 = str_replace( [
            "{heading}",
            "{link_text}",
            "{paragraph}",
            "{image_description}",
        ], [
            $data->block3->heading,
            $data->block3->link_text,
            $data->block3->paragraph,
            $this->image_api->create_image( $data->block3->image_description ),
        ], $block3 );

        $html .= $block3;

        $featured = file_get_contents( __DIR__ . "/../../templates/featured-work.html" );
        $featured = str_replace( [
            "{heading}",
            "{image_description1}",
            "{image_description2}",
        ], [
            $data->featured->heading,
            $this->image_api->create_image( $data->featured->image_description1 ),
            $this->image_api->create_image( $data->featured->image_description2 ),
        ], $featured );

        $html .= $featured;

        $reviews = file_get_contents( __DIR__ . "/../../templates/reviews.html" );
        $reviews = str_replace( [
            "{reviewer_face_description1}",
            "{reviewer_face_description2}",
            "{reviewer_face_description3}",
            "{review_quote1}",
            "{review_quote2}",
            "{review_quote3}",
            "{reviewer_name1}",
            "{reviewer_name2}",
            "{reviewer_name3}",
        ], [
            $this->image_api->create_image( $data->reviews->reviewer_face_description1 ),
            $this->image_api->create_image( $data->reviews->reviewer_face_description2 ),
            $this->image_api->create_image( $data->reviews->reviewer_face_description3 ),
            $data->reviews->review_quote1,
            $data->reviews->review_quote2,
            $data->reviews->review_quote3,
            $data->reviews->reviewer_name1,
            $data->reviews->reviewer_name2,
            $data->reviews->reviewer_name3,
        ], $reviews );

        $html .= $reviews;

        $footer = file_get_contents( __DIR__ . "/../../templates/footer.html" );
        $footer = str_replace( [
            "{heading}",
            "{paragraph}",
            "{link_text}",
        ], [
            $data->footer->heading,
            $data->footer->paragraph,
            $data->footer->link_text,
        ], $footer );

        $html .= $footer;

        return $html;
    }
}
