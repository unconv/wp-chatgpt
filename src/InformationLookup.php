<?php
namespace Unconv\WPChatGPT;

class InformationLookup
{
    public function __construct(
        private ChatGPT $chat_gpt,
    ) {}

    public function find_info( string $question, int $page_id ) {
        $content = strip_tags( get_the_content( post: $page_id ) );

        $system_message = "Your job is to find an answer to the question the user has provided. You can only answer with the code NOT_FOUND or the actual found information. Use the following information to answer the question:

## CONTENT START ##
".$content."
## CONTENT END ##

If the above information does not have an answer to the question, answer only with 'NOT_FOUND' and nothing else.";

error_log( $system_message );

        $this->chat_gpt->set_system_message( $system_message );

        $question .= " (if information is not found, answer with NOT_FOUND)";

        return $this->chat_gpt->send_message( $question );
    }
}
