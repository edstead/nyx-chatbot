<?php
/**
 * Provide a public-facing view for the chatbot shortcode
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/public/partials
 */
?>

<div id="chatbox" class="nyx-chatbot-container" style="--nyx-chat-height: <?php echo esc_attr($atts['height']); ?>">
    <div id="chat-log"></div>
    <div class="nyx-input-container">
        <input type="text" id="user-input" placeholder="<?php echo esc_attr__('Ask a question...', 'nyx-chatbot'); ?>">
        <button class="nyx-send-button" onclick="sendMessage()"><?php echo esc_html__('Type Message', 'nyx-chatbot'); ?></button>
    </div>
    
    <div class="nyx-controls">
        <?php if (get_option('nyx_chatbot_enable_history', 1) && is_user_logged_in()): ?>
        <button class="nyx-control-button nyx-history-button"><?php echo esc_html__('View History', 'nyx-chatbot'); ?></button>
        <?php endif; ?>
        
        <button class="nyx-control-button nyx-clear-button"><?php echo esc_html__('Clear Chat', 'nyx-chatbot'); ?></button>
    </div>
</div>
