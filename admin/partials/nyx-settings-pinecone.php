<?php
/**
 * Provide a admin area view for the Pinecone settings
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
        <a href="?page=nyx-chatbot-pinecone" class="nav-tab nav-tab-active"><?php _e('Pinecone', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-appearance" class="nav-tab"><?php _e('Appearance', 'nyx-chatbot'); ?></a>
        <a href="?page=nyx-chatbot-features" class="nav-tab"><?php _e('Features', 'nyx-chatbot'); ?></a>
    </h2>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('nyx_chatbot_pinecone_settings');
        do_settings_sections('nyx_chatbot_pinecone_settings');
        ?>
        
        <div class="nyx-admin-section">
            <h2><?php _e('Pinecone API Settings', 'nyx-chatbot'); ?></h2>
            
            <table class="nyx-form-table">
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_pinecone_api_key"><?php _e('API Key', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <div class="nyx-api-key-field">
                            <input type="password" id="nyx_chatbot_pinecone_api_key" name="nyx_chatbot_pinecone_api_key" value="<?php echo esc_attr(get_option('nyx_chatbot_pinecone_api_key')); ?>" class="regular-text">
                            <span class="nyx-api-key-toggle">Show</span>
                        </div>
                        <p class="description"><?php _e('Enter your Pinecone API key. You can get this from your Pinecone dashboard.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_pinecone_index"><?php _e('Index Name', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="nyx_chatbot_pinecone_index" name="nyx_chatbot_pinecone_index" value="<?php echo esc_attr(get_option('nyx_chatbot_pinecone_index', 'nyx')); ?>" class="regular-text">
                        <p class="description"><?php _e('The name of your Pinecone index where your vector data is stored.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="nyx_chatbot_pinecone_dimension"><?php _e('Vector Dimension', 'nyx-chatbot'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="nyx_chatbot_pinecone_dimension" name="nyx_chatbot_pinecone_dimension" value="<?php echo esc_attr(get_option('nyx_chatbot_pinecone_dimension', 1536)); ?>" class="regular-text">
                        <p class="description"><?php _e('The dimension size of your vectors. For OpenAI embeddings, this is typically 1536.', 'nyx-chatbot'); ?></p>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="nyx-admin-section">
            <h2><?php _e('Knowledge Base Information', 'nyx-chatbot'); ?></h2>
            <p><?php _e('Your Pinecone index contains the following types of data:', 'nyx-chatbot'); ?></p>
            <ul>
                <li><?php _e('PDF eBooks', 'nyx-chatbot'); ?></li>
                <li><?php _e('Coaching questions', 'nyx-chatbot'); ?></li>
                <li><?php _e('Documents', 'nyx-chatbot'); ?></li>
            </ul>
            <p><?php _e('This data will be used to provide context-aware responses to user queries.', 'nyx-chatbot'); ?></p>
        </div>
        
        <?php submit_button(); ?>
    </form>
    
    <div class="nyx-admin-footer">
        <p><?php _e('Nyx Chatbot - Powered by OpenAI and Pinecone', 'nyx-chatbot'); ?></p>
    </div>
</div>
