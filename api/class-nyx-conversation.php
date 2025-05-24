<?php
/**
 * Conversation handling for Nyx Chatbot
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/api
 */

/**
 * Conversation handling for Nyx Chatbot
 *
 * Handles all conversation storage and retrieval.
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/api
 * @author     Your Name <email@example.com>
 */
class Nyx_Conversation {

    /**
     * Save message to database.
     *
     * @since    1.0.0
     * @param    int       $user_id       User ID.
     * @param    string    $session_id    Session ID.
     * @param    string    $role          Message role (user or ai).
     * @param    string    $content       Message content.
     * @return   bool                     Whether the message was saved.
     */
    public function save_message($user_id, $session_id, $role, $content) {
        global $wpdb;
        
        // Get or create conversation
        $conversation_id = $this->get_or_create_conversation($user_id, $session_id);
        
        if (!$conversation_id) {
            return false;
        }
        
        // Insert message
        $result = $wpdb->insert(
            $wpdb->prefix . 'nyx_messages',
            array(
                'conversation_id' => $conversation_id,
                'role' => $role,
                'content' => $content,
                'created_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%s')
        );
        
        // Update conversation timestamp
        $wpdb->update(
            $wpdb->prefix . 'nyx_conversations',
            array('updated_at' => current_time('mysql')),
            array('id' => $conversation_id),
            array('%s'),
            array('%d')
        );
        
        return $result !== false;
    }

    /**
     * Get or create conversation.
     *
     * @since    1.0.0
     * @param    int       $user_id       User ID.
     * @param    string    $session_id    Session ID.
     * @return   int                      Conversation ID.
     */
    private function get_or_create_conversation($user_id, $session_id) {
        global $wpdb;
        
        // Try to get existing conversation
        $conversation_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}nyx_conversations WHERE user_id = %d AND session_id = %s",
            $user_id,
            $session_id
        ));
        
        if ($conversation_id) {
            return $conversation_id;
        }
        
        // Create new conversation
        $result = $wpdb->insert(
            $wpdb->prefix . 'nyx_conversations',
            array(
                'user_id' => $user_id,
                'session_id' => $session_id,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            return 0;
        }
        
        return $wpdb->insert_id;
    }

    /**
     * Save conversation.
     *
     * @since    1.0.0
     * @param    int       $user_id               User ID.
     * @param    string    $session_id            Session ID.
     * @param    array     $conversation_history   Conversation history.
     * @return   bool                             Whether the conversation was saved.
     */
    public function save_conversation($user_id, $session_id, $conversation_history) {
        global $wpdb;
        
        // Get or create conversation
        $conversation_id = $this->get_or_create_conversation($user_id, $session_id);
        
        if (!$conversation_id) {
            return false;
        }
        
        // Delete existing messages
        $wpdb->delete(
            $wpdb->prefix . 'nyx_messages',
            array('conversation_id' => $conversation_id),
            array('%d')
        );
        
        // Insert new messages
        foreach ($conversation_history as $message) {
            $role = $message['role'] === 'assistant' ? 'ai' : 'user';
            
            $wpdb->insert(
                $wpdb->prefix . 'nyx_messages',
                array(
                    'conversation_id' => $conversation_id,
                    'role' => $role,
                    'content' => $message['content'],
                    'created_at' => current_time('mysql')
                ),
                array('%d', '%s', '%s', '%s')
            );
        }
        
        // Update conversation timestamp
        $wpdb->update(
            $wpdb->prefix . 'nyx_conversations',
            array('updated_at' => current_time('mysql')),
            array('id' => $conversation_id),
            array('%s'),
            array('%d')
        );
        
        return true;
    }

    /**
     * Get conversation messages.
     *
     * @since    1.0.0
     * @param    int       $user_id       User ID.
     * @param    string    $session_id    Session ID.
     * @return   array                    Conversation messages.
     */
    public function get_conversation_messages($user_id, $session_id) {
        global $wpdb;
        
        // Get conversation ID
        $conversation_id = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}nyx_conversations WHERE user_id = %d AND session_id = %s",
            $user_id,
            $session_id
        ));
        
        if (!$conversation_id) {
            return array();
        }
        
        // Get messages
        $messages = $wpdb->get_results($wpdb->prepare(
            "SELECT role, content FROM {$wpdb->prefix}nyx_messages WHERE conversation_id = %d ORDER BY id ASC",
            $conversation_id
        ), ARRAY_A);
        
        return $messages;
    }

    /**
     * Get user conversations.
     *
     * @since    1.0.0
     * @param    int       $user_id    User ID.
     * @return   array                 User conversations.
     */
    public function get_user_conversations($user_id) {
        global $wpdb;
        
        // Get conversations
        $conversations = $wpdb->get_results($wpdb->prepare(
            "SELECT c.id, c.updated_at, m.content 
            FROM {$wpdb->prefix}nyx_conversations c
            LEFT JOIN {$wpdb->prefix}nyx_messages m ON m.conversation_id = c.id
            WHERE c.user_id = %d
            AND m.id = (SELECT MIN(id) FROM {$wpdb->prefix}nyx_messages WHERE conversation_id = c.id AND role = 'user')
            ORDER BY c.updated_at DESC",
            $user_id
        ), ARRAY_A);
        
        $formatted_conversations = array();
        
        foreach ($conversations as $conversation) {
            $formatted_conversations[] = array(
                'id' => $conversation['id'],
                'date' => date('F j, Y, g:i a', strtotime($conversation['updated_at'])),
                'preview' => wp_trim_words($conversation['content'], 10, '...')
            );
        }
        
        return $formatted_conversations;
    }

    /**
     * Get conversation by ID.
     *
     * @since    1.0.0
     * @param    int       $user_id           User ID.
     * @param    int       $conversation_id   Conversation ID.
     * @return   array                        Conversation messages.
     */
    public function get_conversation_by_id($user_id, $conversation_id) {
        global $wpdb;
        
        // Check if conversation belongs to user
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}nyx_conversations WHERE id = %d AND user_id = %d",
            $conversation_id,
            $user_id
        ));
        
        if (!$count) {
            return false;
        }
        
        // Get messages
        $messages = $wpdb->get_results($wpdb->prepare(
            "SELECT role, content FROM {$wpdb->prefix}nyx_messages WHERE conversation_id = %d ORDER BY id ASC",
            $conversation_id
        ), ARRAY_A);
        
        return $messages;
    }
}
