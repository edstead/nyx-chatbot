<?php
/**
 * Provide a admin area view for the Features settings
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
        <a href="?page=nyx-chatbot-appearance" class="nav-tab"><?php _e('Appearance', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-features" class="nav-tab nav-tab-active"><?php _e('Features', 'nyx-chatbot'); ?></a>
    </h2>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('nyx_chatbot_features_settings');
        do_settings_sections('nyx_chatbot_features_settings');
        ?>
        
        <div class="nyx-admin-section">
            <h2><?php _e('Feature Settings', 'nyx-chatbot'); ?></h2>
            
            <table class="nyx-form-table">
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_enable_file_upload"><?php _e('File Upload', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="nyx_chatbot_enable_file_upload" name="nyx_chatbot_enable_file_upload" value="1" <?php checked(get_option('nyx_chatbot_enable_file_upload', 1), 1); ?>>
                            <?php _e('Enable file upload functionality', 'nyx-chatbot'); ?>
                        </label>
                        <p class="description"><?php _e('Allow users to upload documents for analysis.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
                
                <tr id="nyx-file-upload-settings">
                    <th scope="row">
                        <?php _e('File Upload Settings', 'nyx-chatbot'); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="nyx_chatbot_file_upload_pdf" value="1" <?php checked(get_option('nyx_chatbot_file_upload_pdf', 1), 1); ?>>
                                <?php _e('Allow PDF files', 'nyx-chatbot'); ?>
                            </label><br>
                            
                            <label>
                                <input type="checkbox" name="nyx_chatbot_file_upload_doc" value="1" <?php checked(get_option('nyx_chatbot_file_upload_doc', 1), 1); ?>>
                                <?php _e('Allow DOC/DOCX files', 'nyx-chatbot'); ?>
                            </label><br>
                            
                            <label>
                                <input type="checkbox" name="nyx_chatbot_file_upload_txt" value="1" <?php checked(get_option('nyx_chatbot_file_upload_txt', 1), 1); ?>>
                                <?php _e('Allow TXT files', 'nyx-chatbot'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_enable_voice"><?php _e('Voice Input/Output', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="nyx_chatbot_enable_voice" name="nyx_chatbot_enable_voice" value="1" <?php checked(get_option('nyx_chatbot_enable_voice', 1), 1); ?>>
                            <?php _e('Enable voice input and output', 'nyx-chatbot'); ?>
                        </label>
                        <p class="description"><?php _e('Allow users to speak to the chatbot and hear responses.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
                
                <tr id="nyx-voice-settings">
                    <th scope="row">
                        <?php _e('Voice Settings', 'nyx-chatbot'); ?>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="nyx_chatbot_voice_input" value="1" <?php checked(get_option('nyx_chatbot_voice_input', 1), 1); ?>>
                                <?php _e('Enable voice input (microphone)', 'nyx-chatbot'); ?>
                            </label><br>
                            
                            <label>
                                <input type="checkbox" name="nyx_chatbot_voice_output" value="1" <?php checked(get_option('nyx_chatbot_voice_output', 1), 1); ?>>
                                <?php _e('Enable voice output (text-to-speech)', 'nyx-chatbot'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_enable_history"><?php _e('Chat History', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="nyx_chatbot_enable_history" name="nyx_chatbot_enable_history" value="1" <?php checked(get_option('nyx_chatbot_enable_history', 1), 1); ?>>
                            <?php _e('Enable chat history viewing', 'nyx-chatbot'); ?>
                        </label>
                        <p class="description"><?php _e('Allow users to view their past conversations.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_enable_rate_limit"><?php _e('Rate Limiting', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="nyx_chatbot_enable_rate_limit" name="nyx_chatbot_enable_rate_limit" value="1" <?php checked(get_option('nyx_chatbot_enable_rate_limit', 0), 1); ?>>
                            <?php _e('Enable rate limiting', 'nyx-chatbot'); ?>
                        </label>
                        <p class="description"><?php _e('Limit the number of requests a user can make in a given time period.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
                
                <tr id="nyx-rate-limit-settings">
                    <th scope="row">
                        <?php _e('Rate Limit Settings', 'nyx-chatbot'); ?>
                    </th>
                    <td>
                        <label for="nyx_chatbot_rate_limit_count"><?php _e('Max Requests:', 'nyx-chatbot'); ?></label>
                        <input type="number" id="nyx_chatbot_rate_limit_count" name="nyx_chatbot_rate_limit_count" value="<?php echo esc_attr(get_option('nyx_chatbot_rate_limit_count', 10)); ?>" min="1" max="100" style="width: 70px;">
                        
                        <label for="nyx_chatbot_rate_limit_period"><?php _e('Per:', 'nyx-chatbot'); ?></label>
                        <select id="nyx_chatbot_rate_limit_period" name="nyx_chatbot_rate_limit_period">
                            <option value="minute" <?php selected(get_option('nyx_chatbot_rate_limit_period', 'minute'), 'minute'); ?>><?php _e('Minute', 'nyx-chatbot'); ?></option>
                            <option value="hour" <?php selected(get_option('nyx_chatbot_rate_limit_period'), 'hour'); ?>><?php _e('Hour', 'nyx-chatbot'); ?></option>
                            <option value="day" <?php selected(get_option('nyx_chatbot_rate_limit_period'), 'day'); ?>><?php _e('Day', 'nyx-chatbot'); ?></option>
                        </select>
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
