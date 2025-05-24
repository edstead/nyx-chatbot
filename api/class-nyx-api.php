<?php
/**
 * The API functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/api
 */

/**
 * The API functionality of the plugin.
 *
 * Defines the plugin name, version, and API endpoints.
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/api
 * @author     Your Name <email@example.com>
 */
class Nyx_API {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        
        // Load API classes
        require_once plugin_dir_path(dirname(__FILE__)) . 'api/class-nyx-openai.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'api/class-nyx-pinecone.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'api/class-nyx-conversation.php';
    }

    /**
     * Register the REST API routes.
     *
     * @since    1.0.0
     */
    public function register_routes() {
        register_rest_route('nyx-chatbot/v1', '/chat', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_chat_request'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route('nyx-chatbot/v1', '/upload', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_file_upload'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route('nyx-chatbot/v1', '/process-file', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_file_processing'),
            'permission_callback' => '__return_true',
        ));
        
        register_rest_route('nyx-chatbot/v1', '/save', array(
            'methods' => 'POST',
            'callback' => array($this, 'save_conversation'),
            'permission_callback' => array($this, 'check_user_logged_in'),
        ));
        
        register_rest_route('nyx-chatbot/v1', '/history', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_conversation_history'),
            'permission_callback' => array($this, 'check_user_logged_in'),
        ));
        
        register_rest_route('nyx-chatbot/v1', '/conversations', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_conversations'),
            'permission_callback' => array($this, 'check_user_logged_in'),
        ));
        
        register_rest_route('nyx-chatbot/v1', '/conversation/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_conversation'),
            'permission_callback' => array($this, 'check_user_logged_in'),
        ));
    }

    /**
     * Check if user is logged in.
     *
     * @since    1.0.0
     * @return   bool       Whether the user is logged in.
     */
    public function check_user_logged_in() {
        return is_user_logged_in();
    }

    /**
     * Handle chat request.
     *
     * @since    1.0.0
     * @param    WP_REST_Request $request    Full data about the request.
     * @return   WP_REST_Response            Response object.
     */
    public function handle_chat_request($request) {
        // Get parameters
        $message = sanitize_text_field($request->get_param('message'));
        $session_id = sanitize_text_field($request->get_param('session_id'));
        $conversation_history = json_decode(stripslashes($request->get_param('conversation_history')), true);
        
        // Check rate limiting if enabled
        if (get_option('nyx_chatbot_enable_rate_limit', 0)) {
            $rate_limit_result = $this->check_rate_limit($session_id);
            if (!$rate_limit_result['success']) {
                return new WP_REST_Response(array(
                    'success' => false,
                    'reply' => $rate_limit_result['message']
                ), 429);
            }
        }
        
        // Initialize OpenAI and Pinecone
        $openai = new Nyx_OpenAI();
        $pinecone = new Nyx_Pinecone();
        
        // Get relevant context from Pinecone
        $context = $pinecone->query_pinecone($message);
        
        // Get response from OpenAI
        $response = $openai->get_chat_completion($message, $conversation_history, $context);
        
        // Save conversation if user is logged in
        if (is_user_logged_in()) {
            $conversation = new Nyx_Conversation();
            $conversation->save_message(get_current_user_id(), $session_id, 'user', $message);
            $conversation->save_message(get_current_user_id(), $session_id, 'ai', $response);
        }
        
        return new WP_REST_Response(array(
            'success' => true,
            'reply' => $response
        ), 200);
    }

    /**
     * Handle file upload.
     *
     * @since    1.0.0
     * @param    WP_REST_Request $request    Full data about the request.
     * @return   WP_REST_Response            Response object.
     */
    public function handle_file_upload($request) {
        // Check if file upload is enabled
        if (!get_option('nyx_chatbot_enable_file_upload', 1)) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'File upload is disabled.'
            ), 403);
        }
        
        // Get file
        $files = $request->get_file_params();
        
        if (empty($files['file'])) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'No file uploaded.'
            ), 400);
        }
        
        $file = $files['file'];
        
        // Check file type
        $file_type = wp_check_filetype($file['name']);
        $allowed_types = array();
        
        if (get_option('nyx_chatbot_file_upload_pdf', 1)) {
            $allowed_types[] = 'pdf';
        }
        
        if (get_option('nyx_chatbot_file_upload_doc', 1)) {
            $allowed_types[] = 'doc';
            $allowed_types[] = 'docx';
        }
        
        if (get_option('nyx_chatbot_file_upload_txt', 1)) {
            $allowed_types[] = 'txt';
        }
        
        if (!in_array($file_type['ext'], $allowed_types)) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'File type not allowed.'
            ), 400);
        }
        
        // Create uploads directory if it doesn't exist
        $upload_dir = wp_upload_dir();
        $nyx_upload_dir = $upload_dir['basedir'] . '/nyx-chatbot';
        
        if (!file_exists($nyx_upload_dir)) {
            wp_mkdir_p($nyx_upload_dir);
        }
        
        // Generate unique filename
        $filename = wp_unique_filename($nyx_upload_dir, $file['name']);
        $file_path = $nyx_upload_dir . '/' . $filename;
        
        // Move file
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Failed to upload file.'
            ), 500);
        }
        
        // Generate file ID
        $file_id = md5($file_path . time());
        
        // Store file info in transient
        set_transient('nyx_file_' . $file_id, array(
            'path' => $file_path,
            'name' => $file['name'],
            'type' => $file_type['type']
        ), 60 * 60); // Expire after 1 hour
        
        return new WP_REST_Response(array(
            'success' => true,
            'file_id' => $file_id
        ), 200);
    }

    /**
     * Handle file processing.
     *
     * @since    1.0.0
     * @param    WP_REST_Request $request    Full data about the request.
     * @return   WP_REST_Response            Response object.
     */
    public function handle_file_processing($request) {
        // Get parameters
        $file_id = sanitize_text_field($request->get_param('file_id'));
        $session_id = sanitize_text_field($request->get_param('session_id'));
        
        // Get file info
        $file_info = get_transient('nyx_file_' . $file_id);
        
        if (!$file_info) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'File not found or expired.'
            ), 404);
        }
        
        // Extract text from file
        $text = $this->extract_text_from_file($file_info['path'], $file_info['type']);
        
        if (!$text) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Failed to extract text from file.'
            ), 500);
        }
        
        // Initialize OpenAI
        $openai = new Nyx_OpenAI();
        
        // Get response from OpenAI
        $response = $openai->analyze_document($text, $file_info['name']);
        
        // Save conversation if user is logged in
        if (is_user_logged_in()) {
            $conversation = new Nyx_Conversation();
            $conversation->save_message(get_current_user_id(), $session_id, 'user', 'I\'ve uploaded a file: ' . $file_info['name']);
            $conversation->save_message(get_current_user_id(), $session_id, 'ai', $response);
        }
        
        return new WP_REST_Response(array(
            'success' => true,
            'reply' => $response
        ), 200);
    }

    /**
     * Extract text from file.
     *
     * @since    1.0.0
     * @param    string    $file_path    Path to the file.
     * @param    string    $file_type    MIME type of the file.
     * @return   string                  Extracted text.
     */
    private function extract_text_from_file($file_path, $file_type) {
        // Extract text based on file type
        if (strpos($file_type, 'pdf') !== false) {
            return $this->extract_text_from_pdf($file_path);
        } elseif (strpos($file_type, 'msword') !== false || strpos($file_type, 'officedocument.wordprocessingml') !== false) {
            return $this->extract_text_from_doc($file_path);
        } elseif (strpos($file_type, 'text/plain') !== false) {
            return file_get_contents($file_path);
        }
        
        return false;
    }

    /**
     * Extract text from PDF.
     *
     * @since    1.0.0
     * @param    string    $file_path    Path to the PDF file.
     * @return   string                  Extracted text.
     */
    private function extract_text_from_pdf($file_path) {
        // Check if pdftotext is available
        exec('which pdftotext', $output, $return_var);
        
        if ($return_var === 0) {
            // Use pdftotext
            $output_file = $file_path . '.txt';
            exec('pdftotext ' . escapeshellarg($file_path) . ' ' . escapeshellarg($output_file));
            
            if (file_exists($output_file)) {
                $text = file_get_contents($output_file);
                unlink($output_file);
                return $text;
            }
        }
        
        // Fallback to PHP library
        if (class_exists('Smalot\PdfParser\Parser')) {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file_path);
            return $pdf->getText();
        }
        
        return false;
    }

    /**
     * Extract text from DOC/DOCX.
     *
     * @since    1.0.0
     * @param    string    $file_path    Path to the DOC/DOCX file.
     * @return   string                  Extracted text.
     */
    private function extract_text_from_doc($file_path) {
        // Check if antiword is available for DOC files
        if (strpos($file_path, '.doc') !== false && strpos($file_path, '.docx') === false) {
            exec('which antiword', $output, $return_var);
            
            if ($return_var === 0) {
                // Use antiword
                exec('antiword ' . escapeshellarg($file_path), $output);
                return implode("\n", $output);
            }
        }
        
        // For DOCX files or if antiword is not available
        if (class_exists('ZipArchive')) {
            // Try to extract as DOCX (Office Open XML)
            $zip = new ZipArchive();
            
            if ($zip->open($file_path) === true) {
                if (($index = $zip->locateName('word/document.xml')) !== false) {
                    $content = $zip->getFromIndex($index);
                    $zip->close();
                    
                    // Simple XML parsing
                    $content = str_replace('</w:p>', "\n", $content);
                    $content = strip_tags($content);
                    
                    return $content;
                }
                
                $zip->close();
            }
        }
        
        return false;
    }

    /**
     * Save conversation.
     *
     * @since    1.0.0
     * @param    WP_REST_Request $request    Full data about the request.
     * @return   WP_REST_Response            Response object.
     */
    public function save_conversation($request) {
        // Get parameters
        $session_id = sanitize_text_field($request->get_param('session_id'));
        $conversation_history = json_decode(stripslashes($request->get_param('conversation_history')), true);
        
        // Save conversation
        $conversation = new Nyx_Conversation();
        $result = $conversation->save_conversation(get_current_user_id(), $session_id, $conversation_history);
        
        if (!$result) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Failed to save conversation.'
            ), 500);
        }
        
        return new WP_REST_Response(array(
            'success' => true
        ), 200);
    }

    /**
     * Get conversation history.
     *
     * @since    1.0.0
     * @param    WP_REST_Request $request    Full data about the request.
     * @return   WP_REST_Response            Response object.
     */
    public function get_conversation_history($request) {
        // Get parameters
        $session_id = sanitize_text_field($request->get_param('session_id'));
        
        // Get conversation
        $conversation = new Nyx_Conversation();
        $messages = $conversation->get_conversation_messages(get_current_user_id(), $session_id);
        
        return new WP_REST_Response(array(
            'success' => true,
            'messages' => $messages
        ), 200);
    }

    /**
     * Get conversations.
     *
     * @since    1.0.0
     * @param    WP_REST_Request $request    Full data about the request.
     * @return   WP_REST_Response            Response object.
     */
    public function get_conversations($request) {
        // Get conversations
        $conversation = new Nyx_Conversation();
        $conversations = $conversation->get_user_conversations(get_current_user_id());
        
        return new WP_REST_Response(array(
            'success' => true,
            'conversations' => $conversations
        ), 200);
    }

    /**
     * Get conversation.
     *
     * @since    1.0.0
     * @param    WP_REST_Request $request    Full data about the request.
     * @return   WP_REST_Response            Response object.
     */
    public function get_conversation($request) {
        // Get parameters
        $conversation_id = intval($request->get_param('id'));
        
        // Get conversation
        $conversation = new Nyx_Conversation();
        $messages = $conversation->get_conversation_by_id(get_current_user_id(), $conversation_id);
        
        if (!$messages) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Conversation not found.'
            ), 404);
        }
        
        return new WP_REST_Response(array(
            'success' => true,
            'messages' => $messages
        ), 200);
    }

    /**
     * Check rate limit.
     *
     * @since    1.0.0
     * @param    string    $session_id    Session ID.
     * @return   array                    Result array.
     */
    private function check_rate_limit($session_id) {
        // Get rate limit settings
        $rate_limit_count = get_option('nyx_chatbot_rate_limit_count', 10);
        $rate_limit_period = get_option('nyx_chatbot_rate_limit_period', 'minute');
        
        // Convert period to seconds
        $period_seconds = 60; // Default to minute
        
        if ($rate_limit_period === 'hour') {
            $period_seconds = 60 * 60;
        } elseif ($rate_limit_period === 'day') {
            $period_seconds = 60 * 60 * 24;
        }
        
        // Get user ID or use session ID for non-logged-in users
        $user_id = is_user_logged_in() ? get_current_user_id() : 0;
        $key = 'nyx_rate_limit_' . ($user_id ? $user_id : $session_id);
        
        // Get current count and time
        $rate_limit = get_transient($key);
        
        if (!$rate_limit) {
            // First request
            set_transient($key, array(
                'count' => 1,
                'time' => time()
            ), $period_seconds);
            
            return array('success' => true);
        }
        
        // Check if period has passed
        if (time() - $rate_limit['time'] > $period_seconds) {
            // Reset count
            set_transient($key, array(
                'count' => 1,
                'time' => time()
            ), $period_seconds);
            
            return array('success' => true);
        }
        
        // Check if count exceeds limit
        if ($rate_limit['count'] >= $rate_limit_count) {
            // Rate limit exceeded
            return array(
                'success' => false,
                'message' => 'Rate limit exceeded. Please try again later.'
            );
        }
        
        // Increment count
        set_transient($key, array(
            'count' => $rate_limit['count'] + 1,
            'time' => $rate_limit['time']
        ), $period_seconds);
        
        return array('success' => true);
    }
}
