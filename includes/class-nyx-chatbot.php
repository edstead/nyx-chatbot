<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/includes
 * @author     Your Name <email@example.com>
 */
class Nyx_Chatbot {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Nyx_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('NYX_CHATBOT_VERSION')) {
            $this->version = NYX_CHATBOT_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'nyx-chatbot';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_api_hooks();
        $this->init_updater();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Nyx_Loader. Orchestrates the hooks of the plugin.
     * - Nyx_i18n. Defines internationalization functionality.
     * - Nyx_Admin. Defines all hooks for the admin area.
     * - Nyx_Public. Defines all hooks for the public side of the site.
     * - Nyx_API. Defines all API endpoints.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-nyx-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-nyx-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-nyx-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-nyx-public.php';

        /**
         * The class responsible for defining all API endpoints.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'api/class-nyx-api.php';

        $this->loader = new Nyx_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Nyx_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new Nyx_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Nyx_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        
        // Add menu item
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        
        // Add Settings link to the plugin
        $plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_name . '.php');
        $this->loader->add_filter('plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links');
        
        // Save/Update our plugin options
        $this->loader->add_action('admin_init', $plugin_admin, 'options_update');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Nyx_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        
        // Add shortcode
        $this->loader->add_shortcode('nyx_chatbot', $plugin_public, 'shortcode_callback');
        
        // Add floating button if enabled
        $this->loader->add_action('wp_footer', $plugin_public, 'add_floating_button');
    }

    /**
     * Register all of the hooks related to the API functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_api_hooks() {
        $plugin_api = new Nyx_API($this->get_plugin_name(), $this->get_version());

        // Register REST API endpoints
        $this->loader->add_action('rest_api_init', $plugin_api, 'register_routes');
    }

    /**
     * Initialize the GitHub updater
     *
     * @since    1.0.0
     * @access   private
     */
    private function init_updater() {
        // Only initialize updater in admin area
        if (is_admin()) {
            // Load version configuration
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-nyx-version.php';
            
            // Load GitHub updater
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-nyx-github-updater.php';
            
            // Initialize the updater
            new Nyx_GitHub_Updater(
                plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_name . '.php'),
                Nyx_Version::GITHUB_REPO,
                Nyx_Version::VERSION,
                Nyx_Version::GITHUB_ACCESS_TOKEN
            );
        }
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Nyx_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}
