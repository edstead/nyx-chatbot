<?php
/**
 * OpenAI integration for Nyx Chatbot
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/api
 */

/**
 * OpenAI integration for Nyx Chatbot
 *
 * Handles all OpenAI API interactions.
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/api
 * @author     Your Name <email@example.com>
 */
class Nyx_OpenAI {

    /**
     * OpenAI API key
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $api_key    OpenAI API key.
     */
    private $api_key;

    /**
     * OpenAI model
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $model    OpenAI model to use.
     */
    private $model;

    /**
     * Max tokens
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $max_tokens    Maximum number of tokens to generate.
     */
    private $max_tokens;

    /**
     * System message
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $system_message    System message for chat completion.
     */
    private $system_message;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->api_key = get_option('nyx_chatbot_openai_api_key', '');
        $this->model = get_option('nyx_chatbot_openai_model', 'gpt-3.5-turbo');
        $this->max_tokens = intval(get_option('nyx_chatbot_max_tokens', 500));
        $this->system_message = get_option('nyx_chatbot_system_message', 'You are Nyx, a helpful AI assistant.');
    }

    /**
     * Get chat completion from OpenAI.
     *
     * @since    1.0.0
     * @param    string    $message               User message.
     * @param    array     $conversation_history   Previous conversation history.
     * @param    string    $context               Context from Pinecone.
     * @return   string                           OpenAI response.
     */
    public function get_chat_completion($message, $conversation_history = array(), $context = '') {
        // Check if API key is set
        if (empty($this->api_key)) {
            return 'Error: OpenAI API key is not set. Please configure it in the plugin settings.';
        }

        // Prepare messages array
        $messages = array();
        
        // Add system message
        $system_content = $this->system_message;
        
        // Add context if available
        if (!empty($context)) {
            $system_content .= "\n\nYou have access to the following information that may be relevant to the user's query:\n" . $context;
        }
        
        $messages[] = array(
            'role' => 'system',
            'content' => $system_content
        );
        
        // Add conversation history
        if (!empty($conversation_history)) {
            foreach ($conversation_history as $msg) {
                $messages[] = array(
                    'role' => $msg['role'],
                    'content' => $msg['content']
                );
            }
        } else {
            // Add current message
            $messages[] = array(
                'role' => 'user',
                'content' => $message
            );
        }
        
        // Prepare request
        $request_body = array(
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $this->max_tokens,
            'temperature' => 0.7,
        );
        
        // Make API request
        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($request_body),
            'timeout' => 60,
        ));
        
        // Check for errors
        if (is_wp_error($response)) {
            return 'Error: ' . $response->get_error_message();
        }
        
        // Parse response
        $response_body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($response_body['error'])) {
            return 'Error: ' . $response_body['error']['message'];
        }
        
        if (!isset($response_body['choices'][0]['message']['content'])) {
            return 'Error: Unexpected response from OpenAI API.';
        }
        
        return $response_body['choices'][0]['message']['content'];
    }

    /**
     * Generate embeddings for text.
     *
     * @since    1.0.0
     * @param    string    $text    Text to generate embeddings for.
     * @return   array              Embeddings array or error message.
     */
    public function generate_embeddings($text) {
        // Check if API key is set
        if (empty($this->api_key)) {
            return array('error' => 'OpenAI API key is not set.');
        }
        
        // Prepare request
        $request_body = array(
            'model' => 'text-embedding-ada-002',
            'input' => $text
        );
        
        // Make API request
        $response = wp_remote_post('https://api.openai.com/v1/embeddings', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($request_body),
            'timeout' => 30,
        ));
        
        // Check for errors
        if (is_wp_error($response)) {
            return array('error' => $response->get_error_message());
        }
        
        // Parse response
        $response_body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (isset($response_body['error'])) {
            return array('error' => $response_body['error']['message']);
        }
        
        if (!isset($response_body['data'][0]['embedding'])) {
            return array('error' => 'Unexpected response from OpenAI API.');
        }
        
        return $response_body['data'][0]['embedding'];
    }

    /**
     * Analyze document using OpenAI.
     *
     * @since    1.0.0
     * @param    string    $text         Document text.
     * @param    string    $file_name    File name.
     * @return   string                  Analysis result.
     */
    public function analyze_document($text, $file_name) {
        // Truncate text if too long
        $max_length = 8000; // Approximate token limit for context
        if (strlen($text) > $max_length) {
            $text = substr($text, 0, $max_length) . '... [content truncated due to length]';
        }
        
        // Prepare prompt
        $prompt = "I've uploaded a document named \"$file_name\". Here's the content:\n\n$text\n\nPlease analyze this document and provide a summary of its key points.";
        
        // Get completion
        return $this->get_chat_completion($prompt);
    }
}
