<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/public
 * @author     Your Name <email@example.com>
 */
class Nyx_Public {

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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/nyx-chatbot.css', array(), $this->version, 'all');
        
        // Add custom CSS if set
        $custom_css = get_option('nyx_chatbot_custom_css');
        if (!empty($custom_css)) {
            wp_add_inline_style($this->plugin_name, $custom_css);
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        // Main chatbot script
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/nyx-chatbot.js', array('jquery'), $this->version, false);
        
        // Voice functionality if enabled
        if (get_option('nyx_chatbot_enable_voice', 1)) {
            wp_enqueue_script($this->plugin_name . '-voice', plugin_dir_url(__FILE__) . 'js/nyx-voice.js', array('jquery', $this->plugin_name), $this->version, false);
        }
        
        // File upload functionality if enabled
        if (get_option('nyx_chatbot_enable_file_upload', 1)) {
            wp_enqueue_script($this->plugin_name . '-file-upload', plugin_dir_url(__FILE__) . 'js/nyx-file-upload.js', array('jquery', $this->plugin_name), $this->version, false);
        }
        
        // Localize the script with new data
        wp_localize_script($this->plugin_name, 'nyx_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => esc_url_raw(rest_url('nyx-chatbot/v1')),
            'nonce' => wp_create_nonce('wp_rest'),
            'user_logged_in' => is_user_logged_in(),
            'guest_message_limit' => get_option('nyx_chatbot_guest_message_limit', 3),
            'primary_color' => get_option('nyx_chatbot_primary_color', '#325F6E'),
            'background_color' => get_option('nyx_chatbot_background_color', '#F6F5EF'),
            'text_color' => get_option('nyx_chatbot_text_color', '#325F6E'),
            'enable_voice' => get_option('nyx_chatbot_enable_voice', 1),
            'enable_file_upload' => get_option('nyx_chatbot_enable_file_upload', 1),
            'enable_history' => get_option('nyx_chatbot_enable_history', 1),
            'login_url' => wp_login_url(get_permalink()),
            'register_url' => wp_registration_url(),
            'strings' => array(
                'thinking' => __('Thinking...', 'nyx-chatbot'),
                'send' => __('Type Message', 'nyx-chatbot'),
                'placeholder' => __('Ask a question...', 'nyx-chatbot'),
                'login_prompt' => __('You\'ve reached the message limit for guests. Please log in to continue.', 'nyx-chatbot'),
                'error' => __('An error occurred. Please try again.', 'nyx-chatbot'),
                'file_upload' => __('Upload File', 'nyx-chatbot'),
                'voice_start' => __('Start Recording', 'nyx-chatbot'),
                'voice_stop' => __('Stop Recording', 'nyx-chatbot'),
                'voice_play' => __('Play Response', 'nyx-chatbot'),
                'voice_pause' => __('Pause Response', 'nyx-chatbot'),
                'history' => __('View History', 'nyx-chatbot'),
                'clear' => __('Clear Chat', 'nyx-chatbot'),
            )
        ));
    }

    /**
     * Shortcode callback function
     *
     * @since    1.0.0
     * @param    array    $atts    Shortcode attributes
     * @return   string             Shortcode output
     */
    public function shortcode_callback($atts) {
        // Extract shortcode attributes
        $atts = shortcode_atts(
            array(
                'height' => '300px',
            ),
            $atts,
            'nyx_chatbot'
        );
        
        // Start output buffering
        ob_start();
        
        // Include the template
        include_once('partials/nyx-chatbot-shortcode.php');
        
        // Return the buffered content
        return ob_get_clean();
    }

    /**
     * Add floating button if enabled
     *
     * @since    1.0.0
     */
    public function add_floating_button() {
        $display_mode = get_option('nyx_chatbot_display_mode', 'shortcode');
        
        // Only add floating button if enabled
        if ($display_mode === 'floating' || $display_mode === 'both') {
            include_once('partials/nyx-chatbot-floating.php');
        }
    }
}
