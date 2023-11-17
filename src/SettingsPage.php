<?php
namespace Unconv\WPChatGPT;

class SettingsPage {
    public function __construct() {
        add_action( 'admin_menu', function() {
            add_menu_page(
                'WP-GPT Settings',
                'WP-GPT Settings',
                'manage_options',
                'wp_gpt_settings',
                [$this, "render"]
            );
        } );

        add_action( 'init', [$this, "handle_saving"] );
    }

    public function handle_saving() {
        if( ! is_admin() || ! current_user_can( "manage_options" ) ) {
            return false;
        }

        if( isset( $_POST['wp_gpt_model'] ) ) {
            update_option( "wp_gpt_settings", [
                "model" => $_POST['wp_gpt_model'],
            ] );
        }
    }

    public function render() {
        $settings = get_option( "wp_gpt_settings" );
        $current_model = $settings["model"];
        $models = [
            "gpt-3.5-turbo",
            "gpt-3.5-turbo-16k",
            "gpt-4",
        ]
        ?>
        <div class="wrap">
            <h2>WP-GPT Settings</h2>
            <form method="post" action="">
                Model: <select name="wp_gpt_model">
                    <?php
                    foreach( $models as $model ) {
                        $selected = ( $model === $current_model ? " selected" : "" );
                        echo '<option'.$selected.'>'.$model.'</option>';
                    }
                    ?>
                </select>
                <?php
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
