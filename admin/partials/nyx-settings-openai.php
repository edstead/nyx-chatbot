<?php
/**
 * Provide a admin area view for the OpenAI settings
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
        <a href="?page=nyx-chatbot-openai" class="nav-tab nav-tab-active"><?php _e('OpenAI', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-pinecone" class="nav-tab"><?php _e('Pinecone', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-appearance" class="nav-tab"><?php _e('Appearance', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-features" class="nav-tab"><?php _e('Features', 'nyx-chatbot'); ?></a>
    </h2>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('nyx_chatbot_openai_settings');
        do_settings_sections('nyx_chatbot_openai_settings');
        ?>
        
        <div class="nyx-admin-section">
            <h2><?php _e('OpenAI API Settings', 'nyx-chatbot'); ?></h2>
            
            <table class="nyx-form-table">
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_openai_api_key"><?php _e('API Key', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <div class="nyx-api-key-field">
                            <input type="password" id="nyx_chatbot_openai_api_key" name="nyx_chatbot_openai_api_key" value="<?php echo esc_attr(get_option('nyx_chatbot_openai_api_key')); ?>" class="regular-text">
                            <span class="nyx-api-key-toggle">Show</span>
                        </div>
                        <p class="description"><?php _e('Enter your OpenAI API key. You can get this from your OpenAI dashboard.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_openai_model"><?php _e('Model', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <select id="nyx_chatbot_openai_model" name="nyx_chatbot_openai_model">
                            <option value="gpt-3.5-turbo" <?php selected(get_option('nyx_chatbot_openai_model', 'gpt-3.5-turbo'), 'gpt-3.5-turbo'); ?>><?php _e('GPT-3.5 Turbo', 'nyx-chatbot'); ?></option>
                            <option value="gpt-3.5-turbo-16k" <?php selected(get_option('nyx_chatbot_openai_model'), 'gpt-3.5-turbo-16k'); ?>><?php _e('GPT-3.5 Turbo 16K', 'nyx-chatbot'); ?></option>
                            <option value="gpt-4" <?php selected(get_option('nyx_chatbot_openai_model'), 'gpt-4'); ?>><?php _e('GPT-4', 'nyx-chatbot'); ?></option>
                            <option value="gpt-4-turbo" <?php selected(get_option('nyx_chatbot_openai_model'), 'gpt-4-turbo'); ?>><?php _e('GPT-4 Turbo', 'nyx-chatbot'); ?></option>
                        </select>
                        <p class="description"><?php _e('Select which OpenAI model to use. More powerful models may incur higher costs.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_max_tokens"><?php _e('Max Tokens', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="nyx_chatbot_max_tokens" name="nyx_chatbot_max_tokens" value="<?php echo esc_attr(get_option('nyx_chatbot_max_tokens', 500)); ?>" min="50" max="4000">
                        <p class="description"><?php _e('Maximum number of tokens to generate in the response. Higher values may result in more detailed responses but higher costs.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_system_message"><?php _e('System Message (Personality)', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <textarea id="nyx_chatbot_system_message" name="nyx_chatbot_system_message" rows="5" cols="50"><?php echo esc_textarea(get_option('nyx_chatbot_system_message', 'You are Nyx, a helpful AI assistant.')); ?></textarea>
                        <p class="description"><?php _e('Define the personality and behavior of your chatbot. This sets the tone and style of responses.', 'nyx-chatbot'); ?></p>
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
