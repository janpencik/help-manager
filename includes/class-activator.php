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
		
		// If no options, create some with default values
		if( ! $permissions || ! isset( $permissions['admin'] ) || empty( $permissions['admin'] ) ) {

			$default_permissions = array();

			// Make current user super admin
			$default_permissions['admin'] = array();
			array_push( $default_permissions['admin'], get_current_user_id() );

			// Asign capabilities to common roles
			$default_permissions['editor'] = array( 'administrator', 'editor' );
			$default_permissions['reader'] = array( 'administrator', 'editor', 'author', 'contributor' );
			
			if( update_option( 'wp-help-manager-permissions', $default_permissions ) ) {
				$permissions = $default_permissions;
			};

		}
		
		// Assign capabilities
		Wp_Help_Manager_Admin::revoke_capabilities( $permissions );
		Wp_Help_Manager_Admin::assign_capabilities( $permissions );

		// Create an example document if there are no documents published
		$documents = get_posts( array(
			'post_type' 		=> 'wp-help-docs',
			'posts_per_page' 	=> 1
		) );
		if( ! $documents ) {

			// General information
			$example_post = array(
				"post_type" 	=> "wp-help-docs",
				"post_title"    => wp_kses_post( __( "Example Help Document", "wp-help-manager" ) ),
				"post_content"  => wp_kses_post( "<p>" . __( "This help document will help your editors better understand the administration. Here you can insert instructions for creating new posts, modifying WordPress settings, or using plugins you use to extend the website's functionality. Your editors will thank you for that, and you will save yourself a lot of time you would otherwise spend answering repeated questions.", "wp-help-manager" ) ."</p><h2>" . __( "What content can I upload", "wp-help-manager" ) . "</h2><p>" . __( "You can insert formatted text, images, videos, code samples, audio recordings, anything you can think of. In addition, we have added useful features such as automatic heading anchor links, document navigation, responsive iframes (you'll appreciate that when embedding YouTube videos), responsive tables, or automatic opening of image links in a pop-up window. You can activate/deactivate those functions in", "wp-help-manager" ) . " <a href='/wp-admin/admin.php?page=wp-help-manager-settings&amp;tab=document' data-type='URL'>" . __( "the plugin settings", "wp-help-manager" ) . "</a>.</p><h2>" . __( "Who can access the help documents", "wp-help-manager" ) . "</h2><p>" . __( "The help documents are not public and are only visible to registered users inside the WordPress administration. You can customize the user roles allowed to edit or view documents in", "wp-help-manager" ) . " <a href='/wp-admin/admin.php?page=wp-help-manager-settings' data-type='URL'>" . __( "the plugin settings", "wp-help-manager" ) . "</a>. " . __( "Custom roles are also supported.", "wp-help-manager" ) . "</p><h2>" . __( "Support for multilingual websites", "wp-help-manager" ) . "</h2><p>" . __( "The plugin is 100% compatible with the popular", "wp-help-manager" ) . " <a rel='noreferrer noopener' href='https://wpml.org/' data-type='URL' data-id='https://wpml.org/' target='_blank'>" . __( "WPML plugin", "wp-help-manager" ) . "</a>. " . __( "So if your team is international, you can create multilingual help in no time.", "wp-help-manager" ) . "</p><h2>" . __( "Easy import and export documents", "wp-help-manager" ) . "</h2><p>" . __( "Once you have created help documents for the current website, we suppose you will want to use them on other sites as well. So we have prepared for you the possibility to", "wp-help-manager" ) . " <a href='/wp-admin/admin.php?page=wp-help-manager-tools' data-type='URL'>" . __( "import/export help documents easily", "wp-help-manager" ) . "</a>. " . __( "The import uses the official WordPress import tool, which allows you to import multimedia without problems and replace links to related documents - just like you would when importing regular blog posts.", "wp-help-manager" ) . "</p>" ),
				"post_status"   => "publish",
				"post_author"   => get_current_user_id()
			);
			wp_insert_post( $example_post );

		}

	}

}