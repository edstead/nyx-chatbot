<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/admin
 * @author     Your Name <email@example.com>
 */
class Nyx_Admin {

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
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/nyx-admin.css', array(), $this->version, 'all');
        // Add color picker CSS
        wp_enqueue_style('wp-color-picker');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/nyx-admin.js', array('jquery', 'wp-color-picker'), $this->version, false);
        
        // Localize the script with new data
        wp_localize_script($this->plugin_name, 'nyx_admin_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nyx-admin-nonce')
        ));
    }

    /**
     * Add an options page under the Settings submenu
     *
     * @since  1.0.0
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            __('Nyx Chatbot Settings', 'nyx-chatbot'),
            __('Nyx Chatbot', 'nyx-chatbot'),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_setup_page'),
            'dashicons-format-chat',
            65
        );
        
        // Add submenus
        add_submenu_page(
            $this->plugin_name,
            __('General Settings', 'nyx-chatbot'),
            __('General', 'nyx-chatbot'),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_setup_page')
        );
        
        add_submenu_page(
            $this->plugin_name,
            __('OpenAI Settings', 'nyx-chatbot'),
            __('OpenAI', 'nyx-chatbot'),
            'manage_options',
            $this->plugin_name . '-openai',
            array($this, 'display_openai_settings_page')
        );
        
        add_submenu_page(
            $this->plugin_name,
            __('Pinecone Settings', 'nyx-chatbot'),
            __('Pinecone', 'nyx-chatbot'),
            'manage_options',
            $this->plugin_name . '-pinecone',
            array($this, 'display_pinecone_settings_page')
        );
        
        add_submenu_page(
            $this->plugin_name,
            __('Appearance Settings', 'nyx-chatbot'),
            __('Appearance', 'nyx-chatbot'),
            'manage_options',
            $this->plugin_name . '-appearance',
            array($this, 'display_appearance_settings_page')
        );
        
        add_submenu_page(
            $this->plugin_name,
            __('Features Settings', 'nyx-chatbot'),
            __('Features', 'nyx-chatbot'),
            'manage_options',
            $this->plugin_name . '-features',
            array($this, 'display_features_settings_page')
        );
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links($links) {
        $settings_link = array(
            '<a href="' . admin_url('admin.php?page=' . $this->plugin_name) . '">' . __('Settings', 'nyx-chatbot') . '</a>',
        );
        return array_merge($settings_link, $links);
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_setup_page() {
        $plugin_name = $this->plugin_name;
        include_once('partials/nyx-admin-display.php');
    }
    
    /**
     * Render the OpenAI settings page
     *
     * @since    1.0.0
     */
    public function display_openai_settings_page() {
        $plugin_name = $this->plugin_name;
        include_once('partials/nyx-settings-openai.php');
    }
    
    /**
     * Render the Pinecone settings page
     *
     * @since    1.0.0
     */
    public function display_pinecone_settings_page() {
        $plugin_name = $this->plugin_name;
        include_once('partials/nyx-settings-pinecone.php');
    }
    
    /**
     * Render the appearance settings page
     *
     * @since    1.0.0
     */
    public function display_appearance_settings_page() {
        $plugin_name = $this->plugin_name;
        include_once('partials/nyx-settings-appearance.php');
    }
    
    /**
     * Render the features settings page
     *
     * @since    1.0.0
     */
    public function display_features_settings_page() {
        $plugin_name = $this->plugin_name;
        include_once('partials/nyx-settings-features.php');
    }

    /**
     * Validate and update options
     *
     * @since    1.0.0
     */
    public function options_update() {
        // General Settings Group
        register_setting(
            'nyx_chatbot_general_settings',
            'nyx_chatbot_display_mode',
            array($this, 'validate_text_field')
        );
        
        register_setting(
            'nyx_chatbot_general_settings',
            'nyx_chatbot_guest_message_limit',
            array($this, 'validate_number_field')
        );

        // OpenAI Settings Group
        register_setting(
            'nyx_chatbot_openai_settings',
            'nyx_chatbot_openai_api_key',
            array($this, 'validate_api_key')
        );
        
        register_setting(
            'nyx_chatbot_openai_settings',
            'nyx_chatbot_openai_model',
            array($this, 'validate_text_field')
        );
        
        register_setting(
            'nyx_chatbot_openai_settings',
            'nyx_chatbot_max_tokens',
            array($this, 'validate_number_field')
        );
        
        register_setting(
            'nyx_chatbot_openai_settings',
            'nyx_chatbot_system_message',
            array($this, 'validate_textarea_field')
        );

        // Pinecone Settings Group
        register_setting(
            'nyx_chatbot_pinecone_settings',
            'nyx_chatbot_pinecone_api_key',
            array($this, 'validate_api_key')
        );
        
        register_setting(
            'nyx_chatbot_pinecone_settings',
            'nyx_chatbot_pinecone_index',
            array($this, 'validate_text_field')
        );
        
        register_setting(
            'nyx_chatbot_pinecone_settings',
            'nyx_chatbot_pinecone_dimension',
            array($this, 'validate_number_field')
        );

        // Appearance Settings Group
        register_setting(
            'nyx_chatbot_appearance_settings',
            'nyx_chatbot_primary_color',
            array($this, 'validate_color_field')
        );
        
        register_setting(
            'nyx_chatbot_appearance_settings',
            'nyx_chatbot_background_color',
            array($this, 'validate_color_field')
        );
        
        register_setting(
            'nyx_chatbot_appearance_settings',
            'nyx_chatbot_text_color',
            array($this, 'validate_color_field')
        );
        
        register_setting(
            'nyx_chatbot_appearance_settings',
            'nyx_chatbot_font_size',
            array($this, 'validate_number_field')
        );
        
        register_setting(
            'nyx_chatbot_appearance_settings',
            'nyx_chatbot_custom_css',
            array($this, 'validate_textarea_field')
        );
        
        register_setting(
            'nyx_chatbot_appearance_settings',
            'nyx_chatbot_floating_button_image',
            array($this, 'validate_text_field')
        );

        // Features Settings Group
        register_setting(
            'nyx_chatbot_features_settings',
            'nyx_chatbot_enable_file_upload',
            array($this, 'validate_checkbox_field')
        );
        
        register_setting(
            'nyx_chatbot_features_settings',
            'nyx_chatbot_file_upload_pdf',
            array($this, 'validate_checkbox_field')
        );
        
        register_setting(
            'nyx_chatbot_features_settings',
            'nyx_chatbot_file_upload_doc',
            array($this, 'validate_checkbox_field')
        );
        
        register_setting(
            'nyx_chatbot_features_settings',
            'nyx_chatbot_file_upload_txt',
            array($this, 'validate_checkbox_field')
        );
        
        register_setting(
            'nyx_chatbot_features_settings',
            'nyx_chatbot_enable_voice',
            array($this, 'validate_checkbox_field')
        );
        
        register_setting(
            'nyx_chatbot_features_settings',
            'nyx_chatbot_voice_input',
            array($this, 'validate_checkbox_field')
        );
        
        register_setting(
            'nyx_chatbot_features_settings',
            'nyx_chatbot_voice_output',
            array($this, 'validate_checkbox_field')
        );
        
        register_setting(
            'nyx_chatbot_features_settings',
            'nyx_chatbot_enable_history',
            array($this, 'validate_checkbox_field')
        );
        
        register_setting(
            'nyx_chatbot_features_settings',
            'nyx_chatbot_enable_rate_limit',
            array($this, 'validate_checkbox_field')
        );
        
        register_setting(
            'nyx_chatbot_features_settings',
            'nyx_chatbot_rate_limit_count',
            array($this, 'validate_number_field')
        );
        
        register_setting(
            'nyx_chatbot_features_settings',
            'nyx_chatbot_rate_limit_period',
            array($this, 'validate_text_field')
        );
    }
    
    /**
     * Validate API key
     *
     * @since    1.0.0
     */
    public function validate_api_key($input) {
        // Sanitize as a text field
        return sanitize_text_field($input);
    }
    
    /**
     * Validate text field
     *
     * @since    1.0.0
     */
    public function validate_text_field($input) {
        return sanitize_text_field($input);
    }
    
    /**
     * Validate number field
     *
     * @since    1.0.0
     */
    public function validate_number_field($input) {
        return intval($input);
    }
    
    /**
     * Validate textarea field
     *
     * @since    1.0.0
     */
    public function validate_textarea_field($input) {
        return sanitize_textarea_field($input);
    }
    
    /**
     * Validate checkbox field
     *
     * @since    1.0.0
     */
    public function validate_checkbox_field($input) {
        return (isset($input) && !empty($input)) ? 1 : 0;
    }
    
    /**
     * Validate color field
     *
     * @since    1.0.0
     */
    public function validate_color_field($input) {
        // Check if the string is a valid hex color
        if (preg_match('/^#[a-f0-9]{6}$/i', $input)) {
            return $input;
        }
        return '#325F6E'; // Default color
    }
}
