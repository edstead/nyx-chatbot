<?php
/**
 * GitHub Plugin Updater
 *
 * Handles automatic updates from GitHub releases for WordPress plugins
 *
 * @link       https://github.com/yourusername/nyx-chatbot
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/includes
 */

/**
 * GitHub Plugin Updater Class
 *
 * This class handles checking for updates, downloading, and installing
 * plugin updates from GitHub releases.
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/includes
 * @author     Your Name <email@example.com>
 */
class Nyx_GitHub_Updater {
    
    /**
     * Plugin file path
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_file    Plugin file path
     */
    private $plugin_file;
    
    /**
     * Plugin slug
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_slug    Plugin directory name
     */
    private $plugin_slug;
    
    /**
     * Current plugin version
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    Current version
     */
    private $version;
    
    /**
     * GitHub repository
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $github_repo    GitHub repo in format: username/repo
     */
    private $github_repo;
    
    /**
     * GitHub access token
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $github_token    GitHub personal access token
     */
    private $github_token;
    
    /**
     * Update data cache
     *
     * @since    1.0.0
     * @access   private
     * @var      mixed    $update_data    Cached update information
     */
    private $update_data;
    
    /**
     * Initialize the updater
     *
     * @since    1.0.0
     * @param    string    $plugin_file     Plugin file path
     * @param    string    $github_repo     GitHub repository
     * @param    string    $version         Current version
     * @param    string    $github_token    GitHub access token (optional)
     */
    public function __construct($plugin_file, $github_repo, $version, $github_token = '') {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = dirname($plugin_file);
        $this->version = $version;
        $this->github_repo = $github_repo;
        $this->github_token = $github_token;
        
        // Hook into WordPress update system
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
        add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
        add_filter('upgrader_pre_install', array($this, 'pre_install'), 10, 2);
        add_filter('upgrader_post_install', array($this, 'post_install'), 10, 2);
        
        // Add custom update checker
        add_action('admin_init', array($this, 'admin_init'));
    }
    
    /**
     * Admin initialization
     *
     * @since    1.0.0
     */
    public function admin_init() {
        // Add update notification
        add_action('in_plugin_update_message-' . $this->plugin_file, array($this, 'update_message'), 10, 2);
    }
    
    /**
     * Check for plugin updates
     *
     * @since    1.0.0
     * @param    mixed    $transient    WordPress update transient
     * @return   mixed    Modified transient
     */
    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        // Get remote version info
        $remote_version = $this->get_remote_version();
        
        if ($remote_version && version_compare($this->version, $remote_version, '<')) {
            $download_url = $this->get_download_url();
            
            if ($download_url) {
                $transient->response[$this->plugin_file] = (object) array(
                    'slug' => $this->plugin_slug,
                    'new_version' => $remote_version,
                    'url' => $this->get_repo_url(),
                    'package' => $download_url,
                    'tested' => get_bloginfo('version')
                );
            }
        }
        
        return $transient;
    }
    
    /**
     * Get remote version from GitHub
     *
     * @since    1.0.0
     * @return   string|false    Remote version or false on failure
     */
    private function get_remote_version() {
        if ($this->update_data) {
            return isset($this->update_data['tag_name']) ? ltrim($this->update_data['tag_name'], 'v') : false;
        }
        
        $request = wp_remote_get($this->get_api_url(), array(
            'headers' => $this->get_github_headers(),
            'timeout' => 30
        ));
        
        if (!is_wp_error($request) && wp_remote_retrieve_response_code($request) === 200) {
            $body = wp_remote_retrieve_body($request);
            $this->update_data = json_decode($body, true);
            
            if (isset($this->update_data['tag_name'])) {
                return ltrim($this->update_data['tag_name'], 'v');
            }
        }
        
        return false;
    }
    
    /**
     * Get download URL for the latest release
     *
     * @since    1.0.0
     * @return   string|false    Download URL or false on failure
     */
    private function get_download_url() {
        if (!$this->update_data) {
            $this->get_remote_version(); // Fetch data if not already loaded
        }
        
        if ($this->update_data && isset($this->update_data['zipball_url'])) {
            return $this->update_data['zipball_url'];
        }
        
        return false;
    }
    
    /**
     * Get GitHub API URL
     *
     * @since    1.0.0
     * @return   string    API URL
     */
    private function get_api_url() {
        return "https://api.github.com/repos/{$this->github_repo}/releases/latest";
    }
    
    /**
     * Get repository URL
     *
     * @since    1.0.0
     * @return   string    Repository URL
     */
    private function get_repo_url() {
        return "https://github.com/{$this->github_repo}";
    }
    
    /**
     * Get GitHub API headers
     *
     * @since    1.0.0
     * @return   array    Headers array
     */
    private function get_github_headers() {
        $headers = array(
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url')
        );
        
        if (!empty($this->github_token)) {
            $headers['Authorization'] = "token {$this->github_token}";
        }
        
        return $headers;
    }
    
    /**
     * Plugin information for the update screen
     *
     * @since    1.0.0
     * @param    mixed     $false      Default false value
     * @param    string    $action     API action
     * @param    object    $response   Response object
     * @return   object|false    Plugin information or false
     */
    public function plugin_info($false, $action, $response) {
        if ($action !== 'plugin_information') {
            return false;
        }
        
        if ($response->slug !== $this->plugin_slug) {
            return false;
        }
        
        if (!$this->update_data) {
            $this->get_remote_version(); // Fetch data if not already loaded
        }
        
        if ($this->update_data) {
            $plugin_info = new stdClass();
            $plugin_info->name = 'Nyx Chatbot';
            $plugin_info->slug = $this->plugin_slug;
            $plugin_info->version = ltrim($this->update_data['tag_name'], 'v');
            $plugin_info->author = '<a href="' . $this->get_repo_url() . '">Your Name</a>';
            $plugin_info->homepage = $this->get_repo_url();
            $plugin_info->short_description = 'AI-powered chatbot with OpenAI and Pinecone integration';
            $plugin_info->sections = array(
                'description' => $this->format_description($this->update_data['body']),
                'installation' => $this->get_installation_instructions(),
                'changelog' => $this->format_changelog($this->update_data['body']),
                'faq' => $this->get_faq()
            );
            $plugin_info->download_link = $this->update_data['zipball_url'];
            $plugin_info->tested = get_bloginfo('version');
            $plugin_info->requires = Nyx_Version::MIN_WP_VERSION;
            $plugin_info->requires_php = Nyx_Version::MIN_PHP_VERSION;
            $plugin_info->last_updated = $this->update_data['published_at'];
            
            return $plugin_info;
        }
        
        return false;
    }
    
    /**
     * Format description from GitHub release
     *
     * @since    1.0.0
     * @param    string    $body    Release body
     * @return   string    Formatted description
     */
    private function format_description($body) {
        $description = '<h4>Nyx Chatbot - AI-Powered WordPress Plugin</h4>';
        $description .= '<p>An advanced chatbot plugin that integrates OpenAI and Pinecone to provide intelligent, context-aware conversations on your WordPress website.</p>';
        $description .= '<h4>Key Features:</h4>';
        $description .= '<ul>';
        $description .= '<li>OpenAI GPT integration for natural language processing</li>';
        $description .= '<li>Pinecone vector database for conversation memory</li>';
        $description .= '<li>File upload and analysis capabilities</li>';
        $description .= '<li>Voice input and output support</li>';
        $description .= '<li>Customizable appearance and behavior</li>';
        $description .= '<li>Shortcode and floating button display options</li>';
        $description .= '</ul>';
        
        if (!empty($body)) {
            $description .= '<h4>Latest Changes:</h4>';
            $description .= '<p>' . wp_kses_post(nl2br($body)) . '</p>';
        }
        
        return $description;
    }
    
    /**
     * Get installation instructions
     *
     * @since    1.0.0
     * @return   string    Installation instructions
     */
    private function get_installation_instructions() {
        return '<ol>
            <li>Download the plugin from GitHub or update through WordPress admin</li>
            <li>If downloading manually, upload the plugin files to your /wp-content/plugins/ directory</li>
            <li>Activate the plugin through the "Plugins" menu in WordPress</li>
            <li>Configure your OpenAI and Pinecone API keys in the Nyx Chatbot settings</li>
            <li>Customize the appearance and features according to your needs</li>
            <li>Use the shortcode [nyx_chatbot] to embed the chatbot or enable the floating button</li>
        </ol>';
    }
    
    /**
     * Format changelog from release body
     *
     * @since    1.0.0
     * @param    string    $body    Release body
     * @return   string    Formatted changelog
     */
    private function format_changelog($body) {
        if (empty($body)) {
            return '<p>No changelog available for this release.</p>';
        }
        
        return '<div class="nyx-changelog">' . wp_kses_post(nl2br($body)) . '</div>';
    }
    
    /**
     * Get FAQ section
     *
     * @since    1.0.0
     * @return   string    FAQ content
     */
    private function get_faq() {
        return '<h4>How do I get API keys?</h4>
        <p>You need API keys from both OpenAI and Pinecone:</p>
        <ul>
            <li><strong>OpenAI:</strong> Visit <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI API Keys</a></li>
            <li><strong>Pinecone:</strong> Visit <a href="https://pinecone.io" target="_blank">Pinecone.io</a> and create an account</li>
        </ul>
        
        <h4>How do I display the chatbot?</h4>
        <p>You can display the chatbot in two ways:</p>
        <ul>
            <li><strong>Shortcode:</strong> Use [nyx_chatbot] on any page or post</li>
            <li><strong>Floating Button:</strong> Enable in settings to show on all pages</li>
        </ul>
        
        <h4>Is this plugin free?</h4>
        <p>The plugin is free, but you will need paid API keys from OpenAI and Pinecone to use their services.</p>
        
        <h4>Can I customize the appearance?</h4>
        <p>Yes! Visit the Appearance tab in plugin settings to customize colors, fonts, and add custom CSS.</p>';
    }
    
    /**
     * Display update message
     *
     * @since    1.0.0
     * @param    array    $plugin_data    Plugin data
     * @param    object   $response       Update response
     */
    public function update_message($plugin_data, $response) {
        if (isset($response->upgrade_notice)) {
            echo '<div class="update-message" style="color: #d54e21; font-weight: bold;">';
            echo esc_html($response->upgrade_notice);
            echo '</div>';
        }
    }
    
    /**
     * Pre-install hook
     *
     * @since    1.0.0
     * @param    bool     $true    Default true value
     * @param    array    $args    Install arguments
     * @return   bool     True to continue installation
     */
    public function pre_install($true, $args) {
        // You can add pre-installation checks here
        // For example, checking system requirements
        
        $requirements = Nyx_Version::check_requirements();
        
        foreach ($requirements as $requirement) {
            if (!$requirement['met']) {
                return new WP_Error(
                    'requirements_not_met',
                    sprintf(
                        __('This plugin requires %s version %s or higher. You have version %s.', 'nyx-chatbot'),
                        ucfirst(str_replace('_', ' ', key($requirements))),
                        $requirement['required'],
                        $requirement['current']
                    )
                );
            }
        }
        
        return $true;
    }
    
    /**
     * Post-install hook
     *
     * @since    1.0.0
     * @param    bool     $true    Default true value
     * @param    array    $args    Install arguments
     * @return   bool     True on success
     */
    public function post_install($true, $args) {
        // Flush rewrite rules if needed
        flush_rewrite_rules();
        
        // Clear any cached data
        delete_site_transient('update_plugins');
        wp_cache_flush();
        
        return $true;
    }
    
    /**
     * Force check for updates (for manual update button)
     *
     * @since    1.0.0
     * @return   bool    True if update available, false otherwise
     */
    public function force_check() {
        // Clear cached data
        $this->update_data = null;
        delete_site_transient('update_plugins');
        
        // Check for updates
        $remote_version = $this->get_remote_version();
        
        return $remote_version && version_compare($this->version, $remote_version, '<');
    }
    
    /**
     * Get update information
     *
     * @since    1.0.0
     * @return   array|false    Update info or false if no update
     */
    public function get_update_info() {
        $remote_version = $this->get_remote_version();
        
        if ($remote_version && version_compare($this->version, $remote_version, '<')) {
            return array(
                'current_version' => $this->version,
                'new_version' => $remote_version,
                'download_url' => $this->get_download_url(),
                'details_url' => $this->get_repo_url() . '/releases/latest',
                'release_notes' => isset($this->update_data['body']) ? $this->update_data['body'] : ''
            );
        }
        
        return false;
    }
}
