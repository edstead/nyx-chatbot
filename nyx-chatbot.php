<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://github.com/edstead/nyx-chatbot
 * @since             1.0.0
 * @package           Nyx_Chatbot
 *
 * @wordpress-plugin
 * Plugin Name:       Nyx Chatbot
 * Plugin URI:        https://github.com/edstead/nyx-chatbot
 * Description:       An AI-powered chatbot with OpenAI and Pinecone integration, featuring conversation memory, file uploads, and voice input/output.
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://developerondemand2@gmail.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nyx-chatbot
 * Domain Path:       /languages
 * GitHub Plugin URI: edstead/nyx-chatbot
 * GitHub Branch:     main
 * Requires at least: 5.0
 * Tested up to:      6.4
 * Requires PHP:      7.4
 * Update Server:     https://api.github.com/repos/edstead/nyx-chatbot/releases/latest

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define('NYX_CHATBOT_VERSION', '1.0.0');
define('NYX_CHATBOT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('NYX_CHATBOT_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-nyx-activator.php
 */
function activate_nyx_chatbot() {
    require_once NYX_CHATBOT_PLUGIN_DIR . 'includes/class-nyx-activator.php';
    Nyx_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-nyx-deactivator.php
 */
function deactivate_nyx_chatbot() {
    require_once NYX_CHATBOT_PLUGIN_DIR . 'includes/class-nyx-deactivator.php';
    Nyx_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_nyx_chatbot');
register_deactivation_hook(__FILE__, 'deactivate_nyx_chatbot');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require NYX_CHATBOT_PLUGIN_DIR . 'includes/class-nyx-chatbot.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_nyx_chatbot() {
    $plugin = new Nyx_Chatbot();
    $plugin->run();
}
run_nyx_chatbot();
