<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Nyx_Chatbot
 * @subpackage Nyx_Chatbot/includes
 * @author     Your Name <email@example.com>
 */
class Nyx_Deactivator {

    /**
     * Plugin deactivation actions.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // We don't delete tables or options on deactivation
        // This ensures user data is preserved if the plugin is reactivated
    }
}
