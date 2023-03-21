<?php
namespace Unconv\WPChatGPT\Generator;
use Unconv\WPChatGPT\ChatGPT;
use Unconv\WPChatGPT\Template\TemplateInterface;

class PageGenerator {
    public function __construct(
        private string $website_description,
        private ChatGPT $chat_gpt,
    ) {}

    public function generate(
        string $page_name,
        TemplateInterface $template,
    ): string {
        $prompt = 'Here\'s a JSON representation of a website\'s '.$page_name.' page. Please fill in the details to the JSON based on the given prompt. The JSON properties that refer to images should be filled in with detailed descriptions of images.

```
'.$template->get_json().'
```

Prompt: Create a '.$page_name.' page for a website of the following description:
    
'.$this->website_description.'.

Please respond with only the JSON. The JSON values should be HTML encoded.
';

        $result = $this->chat_gpt->send_message( $prompt );

        $parts = explode( "```", $result, 3 );

        if( count( $parts ) === 3 ) {
            $json = $parts[1];
        } else {
            $json = $result;
        }

        return $template->create( $json );
    }
}
