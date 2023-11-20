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

        if( isset( $_POST['settings'] ) ) {
            update_option( "wp_gpt_settings", $_POST['settings'] );
        }
    }

    public function render() {
        $settings = get_option( "wp_gpt_settings" );
        $current_model = $settings["model"] ?? "";
        $api_key = $settings["api_key"] ?? "";
        $models = [
            "gpt-3.5-turbo",
            "gpt-3.5-turbo-16k",
            "gpt-4",
        ]
        ?>
        <div class="wrap">
            <h2>WP-GPT Settings</h2>
            <form method="post" action="">
                <table>
                    <tr>
                        <td>Model:</td>
                        <td>
                            <select name="settings[model]">
                                <?php
                                foreach( $models as $model ) {
                                    $selected = ( $model === $current_model ? " selected" : "" );
                                    echo '<option'.$selected.'>'.$model.'</option>';
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>API-key:</td>
                        <td>
                            <input type="password" name="settings[api_key]" value="<?php echo esc_attr( $api_key ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <button class="button button-primary">Save settings</button>
                        </td>
                    </tr>
                </div>
            </form>
        </div>
        <?php
    }
}
