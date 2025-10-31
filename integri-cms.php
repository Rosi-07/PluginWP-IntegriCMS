<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://integri-cms
 * @since             1.0.0
 * @package           Integri_Cms
 *
 * @wordpress-plugin
 * Plugin Name:       IntegriCMS
 * Plugin URI:        https://github.com/Rosi-07/PluginWP-IntegriCMS.git
 * Description:       IntegriCMS is a WordPress plugin that protects file integrity by generating and comparing SHA-256 hashes. Detects unauthorized modifications, logs events, and sends automatic email alerts to ensure your CMS remains clean and secure.
 * Version:           1.0.0
 * Author:            Codesisters
 * Author URI:        https://github.com/Rosi-07
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       integri-cms
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) exit;

// Define paths
define('INTEGRICMS_PATH', plugin_dir_path(__FILE__));
define('INTEGRICMS_URL', plugin_dir_url(__FILE__));

// Include classes
require_once INTEGRICMS_PATH . 'includes/class-integricms-db.php';
require_once INTEGRICMS_PATH . 'includes/class-integricms-scanner.php';
require_once INTEGRICMS_PATH . 'admin/class-integricms-admin.php';

 //Plugin activation: create tables and run first scan
 
function integricms_activate() {
    IntegriCMS_DB::create_table();
    IntegriCMS_DB::create_logs_table();
    IntegriCMS_Scanner::initial_scan();
    integricms_schedule_cron();
}
register_activation_hook(__FILE__, 'integricms_activate');

//Plugin deactivation: clear cron
register_deactivation_hook(__FILE__, function() {
    wp_clear_scheduled_hook('integricms_daily_scan');
});

// to test cron every five minutes
add_filter('cron_schedules', function($schedules) {
    $schedules['every_five_minutes'] = [
        'interval' => 300, // 300 seconds = 5 minutes
        'display'  => __('Every 5 Minutes', 'integricms')
    ];
    return $schedules;
});

function integricms_schedule_cron() {
    if (!wp_next_scheduled('integricms_daily_scan')) {
        wp_schedule_event(time(), 'every_five_minutes', 'integricms_daily_scan');
    }
}

//Schedule a daily scan job
// function integricms_schedule_cron() {
//     if (!wp_next_scheduled('integricms_daily_scan')) {
//         wp_schedule_event(time(), 'daily', 'integricms_daily_scan');
//     }
// }


 // Run daily scan
add_action('integricms_daily_scan', function() {
    try {
        $changes = IntegriCMS_Scanner::compare_to_base();

        if (empty($changes)) {
            IntegriCMS_DB::log_event('OK', 'Daily scan completed successfully. No changes detected.');
        } else {
            $details = 'Modified files: ' . implode(', ', $changes);
            IntegriCMS_DB::log_event('ALERT', $details);
            IntegriCMS_DB::send_email_notification($changes);
        }
    } catch (Exception $e) {
        IntegriCMS_DB::log_event('ERROR', $e->getMessage());
    }
});

//Initialize admin panel
 
add_action('plugins_loaded', function() {
    new IntegriCMS_Admin();
});