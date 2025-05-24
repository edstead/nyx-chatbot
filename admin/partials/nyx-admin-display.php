<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/admin/partials
 */
?>

<div class="wrap">
    <div class="nyx-admin-header">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    </div>
    
    <h2 class="nyx-admin-tabs nav-tab-wrapper">
        <a href="?page=nyx-chatbot" class="nav-tab nav-tab-active"><?php _e('General', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-openai" class="nav-tab"><?php _e('OpenAI', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-pinecone" class="nav-tab"><?php _e('Pinecone', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-appearance" class="nav-tab"><?php _e('Appearance', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-features" class="nav-tab"><?php _e('Features', 'nyx-chatbot'); ?></a>
    </h2>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('nyx_chatbot_general_settings');
        do_settings_sections('nyx_chatbot_general_settings');
        ?>
        
        <div class="nyx-admin-section">
            <h2><?php _e('General Settings', 'nyx-chatbot'); ?></h2>
            
            <table class="nyx-form-table">
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_display_mode"><?php _e('Display Mode', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="radio" name="nyx_chatbot_display_mode" value="shortcode" <?php checked(get_option('nyx_chatbot_display_mode', 'shortcode'), 'shortcode'); ?>>
                                <?php _e('Shortcode only', 'nyx-chatbot'); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="nyx_chatbot_display_mode" value="floating" <?php checked(get_option('nyx_chatbot_display_mode'), 'floating'); ?>>
                                <?php _e('Floating button only', 'nyx-chatbot'); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="nyx_chatbot_display_mode" value="both" <?php checked(get_option('nyx_chatbot_display_mode'), 'both'); ?>>
                                <?php _e('Both shortcode and floating button', 'nyx-chatbot'); ?>
                            </label>
                            <p class="description"><?php _e('Choose how you want to display the chatbot on your site.', 'nyx-chatbot'); ?></p>
                        </fieldset>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_guest_message_limit"><?php _e('Guest Message Limit', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="nyx_chatbot_guest_message_limit" name="nyx_chatbot_guest_message_limit" value="<?php echo esc_attr(get_option('nyx_chatbot_guest_message_limit', 3)); ?>" min="0">
                        <p class="description"><?php _e('Number of messages non-logged-in users can send before being prompted to log in. Set to 0 for unlimited.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="nyx-admin-section" id="nyx-shortcode-section">
            <h2><?php _e('Shortcode Usage', 'nyx-chatbot'); ?></h2>
            <p><?php _e('Use this shortcode to embed the chatbot on any page or post:', 'nyx-chatbot'); ?></p>
            <div class="nyx-shortcode-example">[nyx_chatbot]</div>
            <p><?php _e('You can also customize the height:', 'nyx-chatbot'); ?></p>
            <div class="nyx-shortcode-example">[nyx_chatbot height="400px"]</div>
        </div>
        
        <?php submit_button(); ?>
    </form>
    
    <div class="nyx-admin-footer">
        <p><?php _e('Nyx Chatbot - Powered by OpenAI and Pinecone', 'nyx-chatbot'); ?></p>
    </div>
</div>
