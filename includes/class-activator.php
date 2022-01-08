<?php

namespace Help_Manager;

/**
 * Fired during plugin activation
 *
 * @link       https://bohemiaplugins.com/
 * @since      1.0.0
 *
 * @package    Help_Manager
 * @subpackage Help_Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Help_Manager
 * @subpackage Help_Manager/includes
 * @author     Bohemia Plugins <contact@bohemiaplugins.com>
 */
class Help_Manager_Activator {

	/**
	 * Activation.
	 * 
	 * @since    1.0.0
	 */
	public static function activate() {

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$permissions = get_option( 'help-manager-permissions' );
		
		// If no options, create some with default values
		if( ! $permissions || ! isset( $permissions['admin'] ) || empty( $permissions['admin'] ) ) {

			$default_permissions = array();

			// Make current user super admin
			$default_permissions['admin'] = array();
			array_push( $default_permissions['admin'], get_current_user_id() );

			// Asign capabilities to common roles
			$default_permissions['editor'] = array( 'administrator', 'editor' );
			$default_permissions['reader'] = array( 'administrator', 'editor', 'author', 'contributor' );
			
			if( update_option( 'help-manager-permissions', $default_permissions ) ) {
				$permissions = $default_permissions;
			};

		}
		
		// Assign capabilities
		Help_Manager_Admin::revoke_capabilities( $permissions );
		Help_Manager_Admin::assign_capabilities( $permissions );

		// Create an example document if there are no documents published
		$documents = get_posts( array(
			'post_type' 		=> 'help-docs',
			'posts_per_page' 	=> 1
		) );
		if( ! $documents ) {

			// General information
			$example_post = array(
				"post_type" 	=> "help-docs",
				"post_title"    => wp_kses_post( __( "Example Help Document", "help-manager" ) ),
				"post_content"  => wp_kses_post( 
					"<!-- wp:paragraph -->" . PHP_EOL .
						"<p>" . __( "This help document will help your editors better understand the administration. Here you can insert instructions for creating new posts, modifying WordPress settings, or using plugins you use to extend the website's functionality. Your editors will thank you for that, and you will save yourself a lot of time you would otherwise spend answering repeated questions.", "help-manager" ) . "</p>" . PHP_EOL .
					"<!-- /wp:paragraph -->" . PHP_EOL . PHP_EOL .
					"<!-- wp:heading -->" . PHP_EOL . 
						"<h2>" . __( "What content can I include", "help-manager" ) . "</h2>" . PHP_EOL . 
					"<!-- /wp:heading -->" . PHP_EOL . PHP_EOL .
					"<!-- wp:paragraph -->" . PHP_EOL . 
						"<p>" . __( "You can insert formatted text, images, videos, code samples, audio recordings, anything you can think of. In addition, we have added useful features such as automatic heading anchor links, document navigation, responsive iframes (you'll appreciate that when embedding YouTube videos), responsive tables, or automatic opening of image links in a pop-up window. You can activate/deactivate those functions in", "help-manager" ) . " <a href='/wp-admin/admin.php?page=help-manager-settings&amp;tab=document' data-type='URL'>" . __( "the plugin settings", "help-manager" ) . "</a>." . "</p>" . PHP_EOL .
					"<!-- /wp:paragraph -->" . PHP_EOL . PHP_EOL .
					"<!-- wp:heading -->" . PHP_EOL .
						"<h2>" . __( "Who can access the help documents", "help-manager" ) . "</h2>" . PHP_EOL . 
					"<!-- /wp:heading -->" . PHP_EOL . PHP_EOL .
					"<!-- wp:paragraph -->" . PHP_EOL . 
						"<p>" . __( "The help documents are not public and are only visible to registered users inside the WordPress administration. You can customize the user roles allowed to edit or view documents in", "help-manager" ) . " <a href='/wp-admin/admin.php?page=help-manager-settings' data-type='URL'>" . __( "the plugin settings", "help-manager" ) . "</a>. " . __( "Custom roles are also supported.", "help-manager" ) . "</p>" . PHP_EOL . 
					"<!-- /wp:paragraph -->" . PHP_EOL . PHP_EOL .
					"<!-- wp:heading -->" . PHP_EOL .  
						"<h2>" . __( "Support for multilingual websites", "help-manager" ) . "</h2>" . PHP_EOL . 
					"<!-- /wp:heading -->" . PHP_EOL . PHP_EOL . 
					"<!-- wp:paragraph -->" . PHP_EOL . 
						"<p>" . __( "The plugin is 100% compatible with the popular", "help-manager" ) . " <a rel='noreferrer nofollow noopener' href='https://wpml.org/' data-type='URL' data-id='https://wpml.org/' target='_blank'>" . __( "WPML plugin", "help-manager" ) . "</a>. " . __( "So if your team is international, you can create multilingual help in no time.", "help-manager" ) . "</p>" . PHP_EOL  . 
					"<!-- /wp:paragraph -->" . PHP_EOL . PHP_EOL . 
					"<!-- wp:heading -->" . PHP_EOL . 
						"<h2>" . __( "Easy import and export documents", "help-manager" ) . "</h2>" . PHP_EOL . 
					"<!-- /wp:heading -->" . PHP_EOL . PHP_EOL .
					"<!-- wp:paragraph -->" . PHP_EOL . 
						"<p>" . __( "Once you have created help documents for the current website, we suppose you will want to use them on other sites as well. So we have prepared for you the possibility to", "help-manager" ) . " <a href='/wp-admin/admin.php?page=help-manager-tools' data-type='URL'>" . __( "import/export help documents easily", "help-manager" ) . "</a>. " . __( "The import uses the official WordPress import tool, which allows you to import multimedia without problems and replace links to related documents - just like you would when importing regular blog posts.", "help-manager" ) . "</p>" . PHP_EOL . 
					"<!-- /wp:paragraph -->"
				),
				"post_status"   => "publish",
				"post_author"   => get_current_user_id()
			);
			wp_insert_post( $example_post );

		}

	}

}