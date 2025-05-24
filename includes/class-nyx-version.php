<?php
/**
 * Plugin version and update configuration
 *
 * @link       https://github.com/yourusername/nyx-chatbot
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/includes
 */

/**
 * Plugin version constants and configuration
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/includes
 * @author     Your Name <email@example.com>
 */
class Nyx_Version {
    
    /**
     * Current plugin version
     * Update this when releasing new versions
     */
    const VERSION = '1.0.0';
    
    /**
     * GitHub repository in format: username/repository-name
     * Change 'yourusername' to your actual GitHub username
     */
    const GITHUB_REPO = 'edstead/nyx-chatbot';
    
    /**
     * GitHub Personal Access Token (optional)
     * Only needed for private repositories
     * Leave empty for public repositories
     */
    const GITHUB_ACCESS_TOKEN = '';
    
    /**
     * Plugin update path - should match GitHub repo
     */
    const UPDATE_PATH = 'edstead/nyx-chatbot';
    
    /**
     * Plugin slug (directory name)
     */
    const PLUGIN_SLUG = 'nyx-chatbot';
    
    /**
     * Main plugin file path relative to plugins directory
     */
    const PLUGIN_FILE = 'nyx-chatbot/nyx-chatbot.php';
    
    /**
     * Minimum WordPress version required
     */
    const MIN_WP_VERSION = '5.0';
    
    /**
     * Minimum PHP version required
     */
    const MIN_PHP_VERSION = '7.4';
    
    /**
     * Get current plugin version
     *
     * @return string Plugin version
     */
    public static function get_version() {
        return self::VERSION;
    }
    
    /**
     * Get GitHub repository URL
     *
     * @return string Repository URL
     */
    public static function get_repo_url() {
        return 'https://github.com/' . self::GITHUB_REPO;
    }
    
    /**
     * Get GitHub API URL for latest release
     *
     * @return string API URL
     */
    public static function get_api_url() {
        return 'https://api.github.com/repos/' . self::GITHUB_REPO . '/releases/latest';
    }
    
    /**
     * Check if system requirements are met
     *
     * @return array Array of requirements with status
     */
    public static function check_requirements() {
        global $wp_version;
        
        return array(
            'wp_version' => array(
                'required' => self::MIN_WP_VERSION,
                'current' => $wp_version,
                'met' => version_compare($wp_version, self::MIN_WP_VERSION, '>=')
            ),
            'php_version' => array(
                'required' => self::MIN_PHP_VERSION,
                'current' => phpversion(),
                'met' => version_compare(phpversion(), self::MIN_PHP_VERSION, '>=')
            )
        );
    }
}
