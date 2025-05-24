<?php
/**
 * Fired during plugin activation
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/includes
 * @author     Your Name <email@example.com>
 */
class Nyx_Activator {

    /**
     * Create necessary database tables on activation.
     *
     * @since    1.0.0
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Create conversations table
        $table_name = $wpdb->prefix . 'nyx_conversations';
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            session_id varchar(255) NOT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY session_id (session_id)
        ) $charset_collate;";

        // Create messages table
        $table_name_messages = $wpdb->prefix . 'nyx_messages';
        $sql .= "CREATE TABLE $table_name_messages (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            conversation_id bigint(20) NOT NULL,
            role varchar(50) NOT NULL,
            content text NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY (id),
            KEY conversation_id (conversation_id)
        ) $charset_collate;";

        // Create user settings table
        $table_name_settings = $wpdb->prefix . 'nyx_user_settings';
        $sql .= "CREATE TABLE $table_name_settings (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            session_id varchar(255) DEFAULT NULL,
            setting_key varchar(255) NOT NULL,
            setting_value text NOT NULL,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY user_setting (user_id, session_id, setting_key)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Add default options
        add_option('nyx_chatbot_openai_api_key', '');
        add_option('nyx_chatbot_pinecone_api_key', '');
        add_option('nyx_chatbot_pinecone_index', 'nyx');
        add_option('nyx_chatbot_pinecone_dimension', '1536');
        add_option('nyx_chatbot_openai_model', 'gpt-3.5-turbo');
        add_option('nyx_chatbot_max_tokens', '500');
        add_option('nyx_chatbot_system_message', 'You are Nyx, a helpful AI assistant.');
        add_option('nyx_chatbot_guest_message_limit', '3');
        add_option('nyx_chatbot_display_mode', 'shortcode'); // shortcode, floating, or both
        add_option('nyx_chatbot_enable_file_upload', '1');
        add_option('nyx_chatbot_enable_voice', '1');
        add_option('nyx_chatbot_enable_history', '1');
        add_option('nyx_chatbot_enable_rate_limit', '0');
    }
}
