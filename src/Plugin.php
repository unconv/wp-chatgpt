<?php
namespace Unconv\WPChatGPT;

class Plugin
{
    private static Plugin $instance;

    private function __construct( string $api_key ) {
        $this->add_actions();

        ChatEndpoint::init(
            new ChatGPT( $api_key )
        );

        $this->enqueue_scripts();
        $this->enqueue_styles();

    }

    public static function init( string $api_key ) {
        if( ! isset( self::$instance ) ) {
            self::$instance = new self( $api_key );
        }

        return self::$instance;
    }

    private function enqueue_scripts(): void {
        wp_enqueue_script( "wpgpt_script", plugins_url( "assets/js/script.js", __DIR__ . "/../plugin.php" ), [
            "jquery"
        ] );
    }

    private function enqueue_styles(): void {
        wp_enqueue_style( "wpgpt_style", plugins_url( "assets/css/style.css", __DIR__ . "/../plugin.php" ) );
    }

    private function add_actions(): void {
        add_action( "wp_footer", [$this, "render_chatbox"]);
    }

    public function render_chatbox(): void {
        ?>
        <button class="wpgpt-toggle">
            <span class="dashicons dashicons-email"></span>
        </button>
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
    }
}
