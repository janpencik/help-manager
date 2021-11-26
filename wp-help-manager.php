<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://bohemiaplugins.com/
 * @since             1.0.0
 * @package           Wp_Help_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       WP Help Manager
 * Plugin URI:        https://wphelpmanager.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Bohemia Plugins
 * Author URI:        https://bohemiaplugins.com/
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       wp-help-manager
 * Domain Path:       /languages
 */

namespace Wp_Help_Manager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_HELP_MANAGER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-help-manager-activator.php
 */
register_activation_hook( __FILE__, function() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-activator.php';
	Wp_Help_Manager_Activator::activate();
} );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-help-manager-deactivator.php
 */
register_deactivation_hook( __FILE__, function() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
	Wp_Help_Manager_Deactivator::deactivate();
} );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-main.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_help_manager() {

	$plugin = new Wp_Help_Manager();
	$plugin->run();

}
run_wp_help_manager();
