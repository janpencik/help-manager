<?php

namespace Wp_Help_Manager;

/**
 * Fired during plugin activation
 *
 * @link       https://bohemiaplugins.com/
 * @since      1.0.0
 *
 * @package    Wp_Help_Manager
 * @subpackage Wp_Help_Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Help_Manager
 * @subpackage Wp_Help_Manager/includes
 * @author     Bohemia Plugins <contact@bohemiaplugins.com>
 */
class Wp_Help_Manager_Activator {

	/**
	 * Activation.
	 * 
	 * @since    1.0.0
	 */
	public static function activate() {

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$permissions = get_option( 'wp-help-manager-permissions' );
		
		// If we have no options, create some with default values
		if( ! $permissions || ! isset( $permissions['admin'] ) || empty( $permissions['admin'] ) ) {

			$default_permissions = array();

			$admins = get_users( array( 
				'role' => 'administrator',
				'fields' => array( 'ID' )
			) );
			$default_permissions['admin'] = array();
			foreach( $admins as $admin ) {
				array_push( $default_permissions['admin'], $admin->ID );
			}

			$default_permissions['editor'] = array( 'administrator', 'editor' );
			$default_permissions['reader'] = array( 'administrator', 'editor', 'author', 'contributor' );
			
			update_option( 'wp-help-manager-permissions', $default_permissions );

		}

	}

}
