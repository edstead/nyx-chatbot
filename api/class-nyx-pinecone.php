<?php
/**
 * Pinecone integration for Nyx Chatbot
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/api
 */

/**
 * Pinecone integration for Nyx Chatbot
 *
 * Handles all Pinecone API interactions.
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/api
 * @author     Your Name <email@example.com>
 */
class Nyx_Pinecone {

    /**
     * Pinecone API key
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $api_key    Pinecone API key.
     */
    private $api_key;

    /**
     * Pinecone index name
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $index_name    Pinecone index name.
     */
    private $index_name;

    /**
     * Vector dimension
     *
     * @since    1.0.0
     * @access   private
     * @var      int    $dimension    Vector dimension.
     */
    private $dimension;

    /**
     * OpenAI instance
     *
     * @since    1.0.0
     * @access   private
     * @var      Nyx_OpenAI    $openai    OpenAI instance.
     */
    private $openai;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->api_key = get_option('nyx_chatbot_pinecone_api_key', '');
        $this->index_name = get_option('nyx_chatbot_pinecone_index', 'nyx');
        $this->dimension = intval(get_option('nyx_chatbot_pinecone_dimension', 1536));
        $this->openai = new Nyx_OpenAI();
    }

    /**
     * Query Pinecone for relevant context.
     *
     * @since    1.0.0
     * @param    string    $query    Query text.
     * @return   string              Relevant context.
     */
    public function query_pinecone($query) {
        // Check if API key is set
        if (empty($this->api_key)) {
            return '';
        }

        // Generate embeddings for query
        $embeddings = $this->openai->generate_embeddings($query);
        
        if (isset($embeddings['error'])) {
            return '';
        }
        
        // Query Pinecone
        $response = $this->query_index($embeddings);
        
        if (empty($response) || isset($response['error'])) {
            return '';
        }
        
        // Extract and format context
        return $this->format_context($response);
    }

    /**
     * Query Pinecone index.
     *
     * @since    1.0.0
     * @param    array     $vector    Query vector.
     * @return   array                Query results.
     */
    private function query_index($vector) {
        // Prepare request
        $request_body = array(
            'vector' => $vector,
            'topK' => 5,
            'includeMetadata' => true
        );
        
        // Make API request
        $response = wp_remote_post("https://api.pinecone.io/v1/indexes/{$this->index_name}/query", array(
            'headers' => array(
                'Api-Key' => $this->api_key,
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
            return array('error' => $response_body['error']);
        }
        
        return $response_body;
    }

    /**
     * Format context from Pinecone response.
     *
     * @since    1.0.0
     * @param    array     $response    Pinecone response.
     * @return   string                 Formatted context.
     */
    private function format_context($response) {
        if (!isset($response['matches']) || empty($response['matches'])) {
            return '';
        }
        
        $context = '';
        
        foreach ($response['matches'] as $match) {
            if (isset($match['metadata']) && isset($match['metadata']['text'])) {
                $context .= $match['metadata']['text'] . "\n\n";
            }
        }
        
        return $context;
    }
}
