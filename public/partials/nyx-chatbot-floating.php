<?php
/**
 * Provide a public-facing view for the floating chatbot button
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/public/partials
 */
?>

<div class="nyx-floating-button">
    <?php if (get_option('nyx_chatbot_floating_button_image')): ?>
        <img src="<?php echo esc_url(get_option('nyx_chatbot_floating_button_image')); ?>" alt="Chat">
    <?php else: ?>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H6L4 18V4H20V16Z" fill="white"/>
            <path d="M7 9H17V11H7V9Z" fill="white"/>
            <path d="M7 12H14V14H7V12Z" fill="white"/>
            <path d="M7 6H17V8H7V6Z" fill="white"/>
        </svg>
    <?php endif; ?>
</div>

<div class="nyx-floating-chatbox">
    <div class="nyx-close-button">&times;</div>
    <div id="chatbox" class="nyx-chatbot-container">
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
</div>
