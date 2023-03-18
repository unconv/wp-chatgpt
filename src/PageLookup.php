<?php
namespace Unconv\WPChatGPT;

class PageLookup
{
    public function __construct(
        private ChatGPT $chat_gpt,
    ) {}

    public function find_page( string $question, array $exclude ) {
        $sitemap = $this->create_sitemap( $exclude );

        $system_message = "Your job is to find a page on our website that would answer the question the user asks. Here's the sitemap of our website:
".json_encode( $sitemap )."

Please respond in JSON format with the ID and name of the page which you think that would answer the question.

For example: {\"id\": 123, \"name\": \"Home Page\"}

Answer in JSON format every time, even if it is not 100% certain that the information could be found on a given page. Answer with the most likely page that could contain that information.

Don't add any other data to the response, than JSON.";

        $this->chat_gpt->set_system_message( $system_message );

        $question .= " (answer only in JSON)";

        return $this->chat_gpt->send_message( $question );
    }

    private function create_sitemap( array $exclude ): array {
        $pages = get_pages();
        $page_list = [];
      
        foreach( $pages as $page ) {
            if( in_array( $page->ID, $exclude ) ) {
                continue;
            }
            $page_list[] = [
                "id" => $page->ID,
                "name" => $page->post_title,
            ];
        }
      
        return $page_list;
    }
}
