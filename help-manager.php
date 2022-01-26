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
 * @package           Help_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Help Manager
 * Plugin URI:        https://bohemiaplugins.com/help-manager
 * Description:       Create documentation for the site's authors, editors, and contributors viewable in the WordPress admin and avoid repeated "how-to" questions.
 * Version:           1.0.0
 * Author:            Bohemia Plugins
 * Author URI:        https://bohemiaplugins.com/
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       help-manager
 * Domain Path:       /languages
 * 
 * Help Manager, 
 * Copyright (C) 2022, Bohemia Plugins, contact@bohemiaplugins.com
 * 
 * Help Manager is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Help Manager is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Help Manager. If not, see <https://www.gnu.org/licenses/>.
 * 
 * Help Manager is inspired by the awesome WordPress plugin "WP Help" 
 * <https://wordpress.org/plugins/wp-help/> by Mark Jaquith <https://coveredweb.com/>.
 */

namespace Help_Manager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'HELP_MANAGER_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-help-manager-activator.php
 */
register_activation_hook( __FILE__, function() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-activator.php';
	Help_Manager_Activator::activate();
} );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-help-manager-deactivator.php
 */
register_deactivation_hook( __FILE__, function() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-deactivator.php';
	Help_Manager_Deactivator::deactivate();
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
function run_help_manager() {

	$plugin = new Help_Manager();
	$plugin->run();

}
run_help_manager();
