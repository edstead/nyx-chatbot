<?php
/**
 * Provide a admin area view for the Appearance settings
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
        <a href="?page=nyx-chatbot" class="nav-tab"><?php _e('General', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-openai" class="nav-tab"><?php _e('OpenAI', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-pinecone" class="nav-tab"><?php _e('Pinecone', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-appearance" class="nav-tab nav-tab-active"><?php _e('Appearance', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-features" class="nav-tab"><?php _e('Features', 'nyx-chatbot'); ?></a>
    </h2>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('nyx_chatbot_appearance_settings');
        do_settings_sections('nyx_chatbot_appearance_settings');
        ?>
        
        <div class="nyx-admin-section">
            <h2><?php _e('Chatbot Appearance', 'nyx-chatbot'); ?></h2>
            
            <table class="nyx-form-table">
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_primary_color"><?php _e('Primary Color', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <div class="nyx-color-picker">
                            <input type="text" id="nyx_chatbot_primary_color" name="nyx_chatbot_primary_color" value="<?php echo esc_attr(get_option('nyx_chatbot_primary_color', '#325F6E')); ?>" class="nyx-color-field">
                            <div class="nyx-color-preview" style="background-color: <?php echo esc_attr(get_option('nyx_chatbot_primary_color', '#325F6E')); ?>"></div>
                        </div>
                        <p class="description"><?php _e('The main color for buttons and accents.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_background_color"><?php _e('Background Color', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <div class="nyx-color-picker">
                            <input type="text" id="nyx_chatbot_background_color" name="nyx_chatbot_background_color" value="<?php echo esc_attr(get_option('nyx_chatbot_background_color', '#F6F5EF')); ?>" class="nyx-color-field">
                            <div class="nyx-color-preview" style="background-color: <?php echo esc_attr(get_option('nyx_chatbot_background_color', '#F6F5EF')); ?>"></div>
                        </div>
                        <p class="description"><?php _e('The background color of the chatbot container.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_text_color"><?php _e('Text Color', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <div class="nyx-color-picker">
                            <input type="text" id="nyx_chatbot_text_color" name="nyx_chatbot_text_color" value="<?php echo esc_attr(get_option('nyx_chatbot_text_color', '#325F6E')); ?>" class="nyx-color-field">
                            <div class="nyx-color-preview" style="background-color: <?php echo esc_attr(get_option('nyx_chatbot_text_color', '#325F6E')); ?>"></div>
                        </div>
                        <p class="description"><?php _e('The color of the text in the chatbot.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_font_size"><?php _e('Font Size', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="nyx_chatbot_font_size" name="nyx_chatbot_font_size" value="<?php echo esc_attr(get_option('nyx_chatbot_font_size', 16)); ?>" min="10" max="32">
                        <p class="description"><?php _e('The base font size in pixels.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="nyx-admin-section" id="nyx-floating-settings">
            <h2><?php _e('Floating Button Settings', 'nyx-chatbot'); ?></h2>
            
            <table class="nyx-form-table">
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_floating_button_image"><?php _e('Button Image', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="nyx_chatbot_floating_button_image" name="nyx_chatbot_floating_button_image" value="<?php echo esc_attr(get_option('nyx_chatbot_floating_button_image')); ?>" class="regular-text">
                        <button id="nyx-upload-button" class="button"><?php _e('Upload Image', 'nyx-chatbot'); ?></button>
                        <div style="margin-top: 10px;">
                            <?php if (get_option('nyx_chatbot_floating_button_image')) : ?>
                                <img src="<?php echo esc_url(get_option('nyx_chatbot_floating_button_image')); ?>" id="nyx-floating-button-preview" style="max-width: 100px; max-height: 100px;">
                            <?php else : ?>
                                <img src="" id="nyx-floating-button-preview" style="max-width: 100px; max-height: 100px; display: none;">
                            <?php endif; ?>
                        </div>
                        <p class="description"><?php _e('Upload an image to use as the floating chat button. If not set, a default chat icon will be used.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="nyx-admin-section">
            <h2><?php _e('Custom CSS', 'nyx-chatbot'); ?></h2>
            
            <table class="nyx-form-table">
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_custom_css"><?php _e('Custom CSS', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <textarea id="nyx_chatbot_custom_css" name="nyx_chatbot_custom_css" rows="10" cols="50"><?php echo esc_textarea(get_option('nyx_chatbot_custom_css', '')); ?></textarea>
                        <p class="description"><?php _e('Add custom CSS to further customize the appearance of the chatbot.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php submit_button(); ?>
    </form>
    
    <div class="nyx-admin-footer">
        <p><?php _e('Nyx Chatbot - Powered by OpenAI and Pinecone', 'nyx-chatbot'); ?></p>
    </div>
</div>
