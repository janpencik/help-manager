<?php

namespace Wp_Help_Manager;
use WP_User;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://bohemiaplugins.com/
 * @since      1.0.0
 *
 * @package    Wp_Help_Manager
 * @subpackage Wp_Help_Manager/admin
 */
class Wp_Help_Manager_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Determine if the current page being viewed is plugin related.
	 *
	 * @since    1.0.0
	 */
	public function is_plugin_page() {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && $screen->id === 'toplevel_page_wp-help-manager-documents'
			|| isset( $screen->id ) && $screen->id === 'publishing-help_page_wp-help-manager-settings'
			|| isset( $screen->id ) && $screen->id === 'publishing-help_page_wp-help-manager-tools'
			|| isset( $screen->id ) && $screen->id === 'edit-wp-help-docs'
			|| ( isset( $screen->id ) && $screen->id === 'wp-help-docs' && $screen->is_block_editor == false ) 
		) {
			return true;
		}
		return false;
	}

	/**
	 * Determine if the current page is documents view.
	 *
	 * @since    1.0.0
	 */
	public function is_plugin_documents_page() {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && $screen->id === 'toplevel_page_wp-help-manager-documents' ) {
			return true;
		}
		return false;
	}

	/**
	 * Determine if the current page is document editing page.
	 *
	 * @since    1.0.0
	 */
	public function is_plugin_document_edit_page() {
		$screen = get_current_screen();
		if( isset( $screen->base ) && isset( $screen->id ) && isset( $_GET['action'] ) && $screen->base === 'post' && $screen->id === 'wp-help-docs' && $_GET['action'] === 'edit' ) {
			return true;
		}
		return false;
	}

	/**
	 * Determine if the current page is plugins settings view.
	 *
	 * @since    1.0.0
	 */
	public function is_plugin_settings_page() {
		$screen = get_current_screen();
		if ( isset( $screen->id ) && ( $screen->id === 'publishing-help_page_wp-help-manager-settings' || $screen->id === 'publishing-help_page_wp-help-manager-tools' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if( $this->is_plugin_page() === true ) {

			// General admin CSS
			wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css', array(), $this->version, 'all' );

		}
		
		if( $this->is_plugin_documents_page() === true ) {

			// See if functions are active
			$document_settings = get_option( $this->plugin_name . '-document' );
			$image_popup = ( isset( $document_settings ) && isset( $document_settings['image_popup'] ) ) ? $document_settings['image_popup'] : true;

			// Documents style dependency array
			$dependencies = array();
			
			// Magnific Popup CSS
			if( $image_popup ) {
				wp_enqueue_style( $this->plugin_name . '-magnific-popup', plugin_dir_url( __FILE__ ) . 'libs/magnific-popup-rtl/magnific-popup.min.css', array(), $this->version, 'all' );
				array_push( $dependencies, $this->plugin_name . '-magnific-popup' );
			}
			
			// Documents main CSS
			wp_enqueue_style( $this->plugin_name . '-documents', plugin_dir_url( __FILE__ ) . 'assets/css/documents.css', $dependencies, $this->version, 'all' );
		
		} elseif( $this->is_plugin_settings_page() === true ) {

			// CodeMirror editor CSS
			wp_enqueue_style( 'wp-codemirror' );

			// Settings main CSS
			wp_enqueue_style( $this->plugin_name . '-settings', plugin_dir_url( __FILE__ ) . 'assets/css/settings.css', array(), $this->version, 'all' );

		}

	}

	/**
	 * Scripts for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if( $this->is_plugin_documents_page() === true ) {
			
			// See if functions are active
			$document_settings = get_option( $this->plugin_name . '-document' );
			$format_iframes = ( isset( $document_settings ) && isset( $document_settings['format_iframes'] ) ) ? $document_settings['format_iframes'] : true;
			$image_popup = ( isset( $document_settings ) && isset( $document_settings['image_popup'] ) ) ? $document_settings['image_popup'] : true;

			// Nested Sortable plugin for jQuery UI Sortable
			wp_register_script( $this->plugin_name . '-nested-sortable', plugin_dir_url( __FILE__ ) . 'libs/nested-sortable/nested-sortable.min.js', array( 'jquery-core', 'jquery-ui-sortable' ), $this->version, false );

			// Main script dependency array
			$dependencies = array( 'jquery-core', 'jquery-ui-sortable', $this->plugin_name . '-nested-sortable' );

			// Magnific Popup JS
			if( $image_popup ) {
				wp_enqueue_script( $this->plugin_name . '-magnific-popup', plugin_dir_url( __FILE__ ) . 'libs/magnific-popup-rtl/jquery.magnific-popup-rtl.min.js', array( 'jquery-core' ), $this->version, false );
				array_push( $dependencies, $this->plugin_name . '-magnific-popup' );
			}

			// Reframe.js JS
			if( $format_iframes ) {
				wp_enqueue_script( $this->plugin_name . '-reframe', plugin_dir_url( __FILE__ ) . 'libs/reframe.js/dist/reframe.min.js', array(), $this->version, false );
				array_push( $dependencies, $this->plugin_name . '-reframe' );
			}

			// Documents main JS
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/documents.js', $dependencies, $this->version, false );
			wp_localize_script( $this->plugin_name, 'wphm_vars', array( 
				'format_iframes' => json_encode( $format_iframes ),
				'image_popup' => json_encode( $image_popup )
			) );
		
		} elseif( $this->is_plugin_settings_page() === true ) {

			// CodeMirror editor JS
			$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
			wp_localize_script( 'jquery', 'cm_settings', $cm_settings );
			wp_enqueue_script( 'wp-theme-plugin-editor' );

			// Settings main JS
			wp_enqueue_script( $this->plugin_name . '-settings', plugin_dir_url( __FILE__ ) . 'assets/js/settings.js', array(), $this->version, $this->version, false );

		}

	}

	/**
	 * Add custom CSS globally to admin.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function custom_admin_css() {
		echo '<style type="text/css">
			#wpadminbar #wp-admin-bar-wphm-admin-bar-menu .ab-icon:before { 
				top: 2px; 
			}
			@media screen and (max-width: 782px) {
				#wpadminbar #wp-admin-bar-wphm-admin-bar-edit {
					display: list-item;
				}
			}
			#wpadminbar #wp-admin-bar-wphm-admin-bar-edit .ab-icon:before {
				content: "\f464";
    			top: 2px;
			}
			#wpadminbar #wp-admin-bar-wphm-admin-bar-menu .ab-sub-wrapper .ab-item {
				display: flex;
    			align-items: center;
			}
			#wpadminbar #wp-admin-bar-wphm-admin-bar-menu .ab-sub-label {
				margin-right: 16px;
				align-items: center;
    			display: flex;
			}
			body.rtl #wpadminbar #wp-admin-bar-wphm-admin-bar-menu .ab-sub-label {
				margin-right: 0;
				margin-left: 16px;
			}
			#wpadminbar #wp-admin-bar-wphm-admin-bar-menu .ab-sub-icon { 
				position: relative;
				float: none;
				font: normal 16px/1 dashicons;
				speak: never;
				padding: 0;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				background-image: none!important;
				margin-right: 6px;
			}
			body.rtl #wpadminbar #wp-admin-bar-wphm-admin-bar-menu .ab-sub-icon { 
				margin-right: 0;
				margin-left: 6px;
			}
			#wpadminbar #wp-admin-bar-wphm-admin-bar-menu .ab-sub-icon:before { 
				position: relative;
				transition: all .1s ease-in-out;
				color: rgba(240,246,252,.6);
			}
			#wpadminbar #wp-admin-bar-wphm-admin-bar-menu li:hover .ab-sub-icon:before { 
				color: #72aee6;
			}
		</style>';
	}
	
	/**
	 * Add custom CSS to document page.
	 *
	 * @since    1.0.0
	 */
	public function custom_document_css() {
		if( $this->is_plugin_documents_page() === true ) {
			$custom_css = get_option( $this->plugin_name . '-custom-css' );
			if( isset( $custom_css ) && isset( $custom_css['custom-css'] ) && $custom_css['custom-css'] !== '' ) {
				echo '<style id="wphm-custom-css">' . $custom_css['custom-css'] . '</style>';
			}
		}
	}

	/**
	 * Remove editor styles from classic editor.
	 *
	 * @since    1.0.0
	 */
	public function remove_classic_editor_styles() {
		$screen = get_current_screen();

		// Classic editor
		if ( $screen->base === 'post' && $screen->post_type === 'wp-help-docs' ) {
			remove_editor_styles();
		}
	}

	/**
	 * Assets for block editor.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_block_editor_assets() {
		$screen = get_current_screen();

		// Gutenberg
		if ( $screen->is_block_editor && $screen->post_type === 'wp-help-docs' ) {
			wp_dequeue_style( 'twentytwenty-block-editor-styles' );
			wp_enqueue_style( $this->plugin_name . '-gutenberg', plugin_dir_url( __FILE__ ) . 'assets/css/gutenberg.css', array( 'wp-edit-blocks' ), $this->version, 'all' );
		}
	}

	/**
	 * Add top level menu item.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_plugin_admin_menu() {

		// Get admin settings
		$admin_settings = get_option( $this->plugin_name . '-admin' );
		$menu_icon = ( isset( $admin_settings ) && isset( $admin_settings['menu_icon'] ) && $admin_settings['menu_icon'] ) ? esc_html( $admin_settings['menu_icon'] ) : 'dashicons-editor-help';
		$menu_position = ( isset( $admin_settings ) && isset( $admin_settings['menu_position'] ) && absint( $admin_settings['menu_position'] ) !== 0 ) ? absint( $admin_settings['menu_position'] ) : 2;

		// Top level menu item
		add_menu_page(
			__( 'Publishing Help', 'wp-help-manager' ),
			__( 'Publishing Help', 'wp-help-manager' ),
			'read_documents',
			'wp-help-manager-documents',
			array( $this, 'display_documents_page' ),
			$menu_icon,
			$menu_position
		);

		// Submenu item - Manage
		add_submenu_page(
			'wp-help-manager-documents',
			__( 'Manage', 'wp-help-manager' ),
			__( 'Manage', 'wp-help-manager' ),
			'edit_documents',
			'edit.php?post_type=wp-help-docs',
			null
		);

		// Submenu item - Add new
		add_submenu_page(
			'wp-help-manager-documents',
			__( 'Add New', 'wp-help-manager' ),
			__( 'Add New', 'wp-help-manager' ),
			'edit_documents',
			'post-new.php?post_type=wp-help-docs',
			null
		);

		// Submenu item - Settings
		add_submenu_page(
			'wp-help-manager-documents',
			__( 'Settings', 'wp-help-manager' ),
			__( 'Settings', 'wp-help-manager' ),
			'access_wphm_settings',
			'wp-help-manager-settings',
			array( $this, 'display_settings_page' )
		);

		// Submenu item - Tools
		add_submenu_page(
			'wp-help-manager-documents',
			__( 'Tools', 'wp-help-manager' ),
			__( 'Tools', 'wp-help-manager' ),
			'access_wphm_settings',
			'wp-help-manager-tools',
			array( $this, 'display_tools_page' )
		);

	}

	/**
	 * Modify plugin menu.
	 *
	 * @since 1.0.0
	 * @access   public
	 */
	public function modify_plugin_menu() {
		global $menu;
		global $submenu;

		$admin_settings = get_option( $this->plugin_name . '-admin' );

		// Change plugin's menu item name according to user settings
		foreach( $menu as $key => $m ) {
			if( $m[2] === 'wp-help-manager-documents' ) {
				$menu_item_key = $key;
			}
		}

		// Make headline WPML translatable
		if( class_exists( 'SitePress' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$current_language = sanitize_key( ICL_LANGUAGE_CODE );
			$menu[$menu_item_key][0] = ( isset( $admin_settings ) && isset( $admin_settings['headline_' . $current_language] ) && $admin_settings['headline_' . $current_language] !== '' ) ? esc_html( $admin_settings['headline_' . $current_language] ) : __( 'Publishing Help', 'wp-help-manager' );
		} else {
			$menu[$menu_item_key][0] = ( isset( $admin_settings ) && isset( $admin_settings['headline'] ) && $admin_settings['headline'] !== '' ) ? esc_html( $admin_settings['headline'] ) : __( 'Publishing Help', 'wp-help-manager' );

		}

		// Change post type menu item name
		if( isset( $submenu['wp-help-manager-documents'] ) ) {
			$submenu['wp-help-manager-documents'][0][0] = __( 'Documents', 'wp-help-manager' );
		}

	}

	/**
	 * Highlight menu item on document edit page.
	 *
	 * @since 1.0.0
	 * @access   public
	 */
	public function highlight_menu_on_document_edit_page( $parent_file ) {
		global $plugin_page;

        if( $this->is_plugin_document_edit_page() === true ) {
			$plugin_page = 'wp-help-manager-documents';
		}

        return $parent_file;
	}

	/**
	 * Add admin bar menu.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function admin_bar_menu( $admin_bar ) {

		if( ! current_user_can( 'read_documents' ) ) {
		    return;
		}

		if( is_admin() ) {

			$admin_settings = get_option( $this->plugin_name . '-admin' );

			// Edit document button
			if( $this->is_plugin_documents_page() && current_user_can( 'edit_documents' ) ) {
				
				// Get search parameter
				$search_string = '';
				if( isset( $_GET['s'] ) ) {
					$search_string = esc_attr( $_GET['s'] );
				}

				// Get document parameter
				if( isset( $_GET['document'] ) ) {
					$requested_id = intval( $_GET['document'] );
							
					// Check if document exists
					if( get_post_status( $requested_id ) !== false ) {
						$document_id = $requested_id;
					} else {
						$document_id = false;
					}
					
				} else {
					$document_id = $this->get_default_document();
				}

				if( ! $search_string && $document_id ) {
					$admin_bar->add_node( array(
						'id' 		=> 'wphm-admin-bar-edit',
						'title' 	=> '<span class="ab-icon"></span><span class="ab-label">' . __( 'Edit document', 'wp-help-manager' ) . '</span>',
						'parent' 	=> false,
						'href' 		=> esc_url( get_edit_post_link( $document_id ) )
					) );
				}
				
			}

			// Help documents menu
			if( ( isset( $admin_settings ) && isset( $admin_settings['admin_bar'] ) && $admin_settings['admin_bar'] ) || ( ! $admin_settings ) ) {

				// Make headline WPML translatable
				if( class_exists( 'SitePress' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
					$current_language = sanitize_key( ICL_LANGUAGE_CODE );
					$headline = ( isset( $admin_settings ) && isset( $admin_settings['headline_' . $current_language] ) && $admin_settings['headline_' . $current_language] !== '' ) ? esc_html( $admin_settings['headline_' . $current_language] ) : __( 'Publishing Help', 'wp-help-manager' );
				} else {
					$headline = ( isset( $admin_settings ) && isset( $admin_settings['headline'] ) && $admin_settings['headline'] !== '' ) ? esc_html( $admin_settings['headline'] ) : __( 'Publishing Help', 'wp-help-manager' );
				}

				$menu_icon = ( isset( $admin_settings ) && isset( $admin_settings['menu_icon'] ) && $admin_settings['menu_icon'] ) ? esc_html( $admin_settings['menu_icon'] ) : 'dashicons-editor-help';

				// Add to admin bar
				$admin_bar->add_node( array(
					'id' 		=> 'wphm-admin-bar-menu',
					'title' 	=> '<span class="ab-icon ' . $menu_icon . '"></span><span class="ab-label">' . $headline . '</span>',
					'parent' 	=> 'top-secondary',
					'href' 		=> admin_url( 'admin.php?page=wp-help-manager-documents' )
				) );

				// Get all top level documents
				$documents = get_posts( array( 
					'post_type' 		=> 'wp-help-docs', 
					'post_status' 		=> array( 'publish', 'private' ), 
					'posts_per_page' 	=> 20,
					'fields' 			=> 'ids',
					'post_parent' 		=> 0,
					'suppress_filters'	=> false
				) );

				// Add documents as submenu items
				if( $documents ) {
					foreach( $documents as $document_id ) {
						$admin_bar->add_node( array(
							'id' 		=> 'wphm-admin-bar-menu-' . $document_id, 
							'title' 	=> '<span class="ab-sub-icon dashicons-external"></span><span class="ab-sub-label">' . esc_attr( get_the_title( $document_id ) ) . '</span>', 
							'parent' 	=> 'wphm-admin-bar-menu', 
							'href' 		=> esc_attr( esc_url( get_permalink( $document_id ) ) ), 
							'meta' 		=> array( 'target' => '_blank' )
						) );
					}
				}

			}

		}
		
	}

	/**
	 * Set default menu order for newly created document.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function set_menu_order_for_new_document( $data, $postarr ) {
		if( $data['post_type'] === 'wp-help-docs' && $data['post_status'] === 'auto-draft' ) {
			$last_post = get_posts( array(
				'post_type' 		=> 'wp-help-docs',
				'posts_per_page' 	=> 1,
				'post_status'		=> array( 'publish', 'private' ),
				'orderby'          	=> 'menu_order',
				'order'            	=> 'DESC',
				'suppress_filters'	=> false
			) );
			if( $last_post ) {
				$data['menu_order'] = $last_post[0]->menu_order + 10;
			}
		}
		return $data;
	}
	
	/**
	 * Register plugin settings using the Settings API.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function options_update() {

		// Admin
		register_setting(
			$this->plugin_name . '-admin',
			$this->plugin_name . '-admin',
			array( $this, 'validate_admin' )
		);

		// Document
		register_setting(
			$this->plugin_name . '-document',
			$this->plugin_name . '-document',
			array( $this, 'validate_document' )
		);

		// Permissions
		register_setting(
			$this->plugin_name . '-permissions',
			$this->plugin_name . '-permissions',
			array( $this, 'validate_permissions' )
		);

		// Custom CSS
		register_setting(
			$this->plugin_name . '-custom-css',
			$this->plugin_name . '-custom-css',
			array( $this, 'validate_custom_css' )
		);

		// Advanced
		register_setting(
			$this->plugin_name . '-advanced',
			$this->plugin_name . '-advanced',
			array( $this, 'validate_advanced' )
		);

	}

	/**
	 * Validate data on Settings page - Admin tab.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function validate_admin( $input ) {

		$valid = array();

		$dashicons = array( 'dashicons-menu', 'dashicons-dashboard', 'dashicons-admin-site', 'dashicons-admin-media', 'dashicons-admin-page', 'dashicons-admin-comments', 'dashicons-admin-appearance', 'dashicons-admin-plugins', 'dashicons-admin-users', 'dashicons-admin-tools', 'dashicons-admin-settings', 'dashicons-admin-network', 'dashicons-admin-generic', 'dashicons-admin-home', 'dashicons-admin-collapse', 'dashicons-admin-links', 'dashicons-admin-post', 'dashicons-format-standard', 'dashicons-format-image', 'dashicons-format-gallery', 'dashicons-format-audio', 'dashicons-format-video', 'dashicons-format-links', 'dashicons-format-chat', 'dashicons-format-status', 'dashicons-format-aside', 'dashicons-format-quote', 'dashicons-welcome-write-blog', 'dashicons-welcome-edit-page', 'dashicons-welcome-add-page', 'dashicons-welcome-view-site', 'dashicons-welcome-widgets-menus', 'dashicons-welcome-comments', 'dashicons-welcome-learn-more', 'dashicons-image-crop', 'dashicons-image-rotate-left', 'dashicons-image-rotate-right', 'dashicons-image-flip-vertical', 'dashicons-image-flip-horizontal', 'dashicons-undo', 'dashicons-redo', 'dashicons-editor-bold', 'dashicons-editor-italic', 'dashicons-editor-ul', 'dashicons-editor-ol', 'dashicons-editor-quote', 'dashicons-editor-alignleft', 'dashicons-editor-aligncenter', 'dashicons-editor-alignright', 'dashicons-editor-insertmore', 'dashicons-editor-spellcheck', 'dashicons-editor-distractionfree', 'dashicons-editor-expand', 'dashicons-editor-contract', 'dashicons-editor-kitchensink', 'dashicons-editor-underline', 'dashicons-editor-justify', 'dashicons-editor-textcolor', 'dashicons-editor-paste-word', 'dashicons-editor-paste-text', 'dashicons-editor-removeformatting', 'dashicons-editor-video', 'dashicons-editor-customchar', 'dashicons-editor-outdent', 'dashicons-editor-indent', 'dashicons-editor-help', 'dashicons-editor-strikethrough', 'dashicons-editor-unlink', 'dashicons-editor-rtl', 'dashicons-editor-break', 'dashicons-editor-code', 'dashicons-editor-paragraph', 'dashicons-align-left', 'dashicons-align-right', 'dashicons-align-center', 'dashicons-align-none', 'dashicons-lock', 'dashicons-calendar', 'dashicons-visibility', 'dashicons-post-status', 'dashicons-edit', 'dashicons-post-trash', 'dashicons-trash', 'dashicons-external', 'dashicons-arrow-up', 'dashicons-arrow-down', 'dashicons-arrow-left', 'dashicons-arrow-right', 'dashicons-arrow-up-alt', 'dashicons-arrow-down-alt', 'dashicons-arrow-left-alt', 'dashicons-arrow-right-alt', 'dashicons-arrow-up-alt2', 'dashicons-arrow-down-alt2', 'dashicons-arrow-left-alt2', 'dashicons-arrow-right-alt2', 'dashicons-leftright', 'dashicons-sort', 'dashicons-randomize', 'dashicons-list-view', 'dashicons-exerpt-view', 'dashicons-hammer', 'dashicons-art', 'dashicons-migrate', 'dashicons-performance', 'dashicons-universal-access', 'dashicons-universal-access-alt', 'dashicons-tickets', 'dashicons-nametag', 'dashicons-clipboard', 'dashicons-heart', 'dashicons-megaphone', 'dashicons-schedule', 'dashicons-wordpress', 'dashicons-wordpress-alt', 'dashicons-pressthis,', 'dashicons-update,', 'dashicons-screenoptions', 'dashicons-info', 'dashicons-cart', 'dashicons-feedback', 'dashicons-cloud', 'dashicons-translation', 'dashicons-tag', 'dashicons-category', 'dashicons-archive', 'dashicons-tagcloud', 'dashicons-text', 'dashicons-media-archive', 'dashicons-media-audio', 'dashicons-media-code', 'dashicons-media-default', 'dashicons-media-document', 'dashicons-media-interactive', 'dashicons-media-spreadsheet', 'dashicons-media-text', 'dashicons-media-video', 'dashicons-playlist-audio', 'dashicons-playlist-video', 'dashicons-yes', 'dashicons-no', 'dashicons-no-alt', 'dashicons-plus', 'dashicons-plus-alt', 'dashicons-minus', 'dashicons-dismiss', 'dashicons-marker', 'dashicons-star-filled', 'dashicons-star-half', 'dashicons-star-empty', 'dashicons-flag', 'dashicons-share', 'dashicons-share1', 'dashicons-share-alt', 'dashicons-share-alt2', 'dashicons-twitter', 'dashicons-rss', 'dashicons-email', 'dashicons-email-alt', 'dashicons-facebook', 'dashicons-facebook-alt', 'dashicons-networking', 'dashicons-googleplus', 'dashicons-location', 'dashicons-location-alt', 'dashicons-camera', 'dashicons-images-alt', 'dashicons-images-alt2', 'dashicons-video-alt', 'dashicons-video-alt2', 'dashicons-video-alt3', 'dashicons-vault', 'dashicons-shield', 'dashicons-shield-alt', 'dashicons-sos', 'dashicons-search', 'dashicons-slides', 'dashicons-analytics', 'dashicons-chart-pie', 'dashicons-chart-bar', 'dashicons-chart-line', 'dashicons-chart-area', 'dashicons-groups', 'dashicons-businessman', 'dashicons-id', 'dashicons-id-alt', 'dashicons-products', 'dashicons-awards', 'dashicons-forms', 'dashicons-testimonial', 'dashicons-portfolio', 'dashicons-book', 'dashicons-book-alt', 'dashicons-download', 'dashicons-upload', 'dashicons-backup', 'dashicons-clock', 'dashicons-lightbulb', 'dashicons-microphone', 'dashicons-desktop', 'dashicons-tablet', 'dashicons-smartphone', 'dashicons-smiley' );

		$valid['headline'] = sanitize_text_field( $input['headline'] );
		
		// Make headline WPML translatable
		if( class_exists( 'SitePress' ) ) {
			$languages = icl_get_languages('skip_missing=0&orderby=code');
			if( ! empty( $languages ) ) {
				if( defined( 'ICL_LANGUAGE_CODE' ) ) {
					$current_language = sanitize_key( ICL_LANGUAGE_CODE );
					foreach( $languages as $language ) {
						if( $language['language_code'] == $current_language ) {
							$valid['headline_' . $current_language] = sanitize_text_field( $input['headline_' . $current_language] );
						}
					}
				}
			}
		}

		$valid['menu_icon'] = ( in_array( $input['menu_icon'], $dashicons ) ) 
			? sanitize_key( $input['menu_icon'] ) 
			: 'dashicons-editor-help';
		$valid['menu_position'] = absint( $input['menu_position'] ) !== 0 
			? absint( $input['menu_position'] )
			: 2;
		$valid['dashboard_widget'] = boolval( $input['dashboard_widget'] );
		$valid['admin_bar'] = boolval( $input['admin_bar'] );

		return $valid;

	}

	/**
	 * Validate data on Settings page - Admin tab.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function validate_document( $input ) {

		$valid = array();

		$valid['child_navigation'] = boolval( $input['child_navigation'] );
		$valid['post_navigation'] = boolval( $input['post_navigation'] );
		$valid['format_tables'] = boolval( $input['format_tables'] );
		$valid['format_iframes'] = boolval( $input['format_iframes'] );
		$valid['image_popup'] = boolval( $input['image_popup'] );
	
		return $valid;

	}

	/**
	 * Validate data on Settings page - Permissions tab.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function validate_permissions( $input ) {

		$valid = array();

		$valid['admin'] = array();
		if( ! empty( $input['admin'] ) ) {
			foreach( $input['admin'] as $user_id ) {
				$user = get_userdata( $user_id );
				if( $user && $user->roles ) {
					if( in_array( 'administrator', (array) $user->roles ) ) {
						array_push( $valid['admin'], intval( $user->ID ) );
					}
				}
			}
		} else {
			// show notice if no admin was selected
			wp_redirect( add_query_arg( 'wphm-notice', 'no-admin-selected', admin_url( '/admin.php?page=wp-help-manager-settings&tab=permissions' ) ) );
			exit;
		}

		$valid['editor'] = array();
		if( ! empty( $input['editor'] ) ) {
			foreach( $input['editor'] as $role_allowed_to_edit ) {
				if( get_role( $role_allowed_to_edit ) ) {
					array_push( $valid['editor'], sanitize_key( $role_allowed_to_edit ) );
				}
			}
		}

		$valid['reader'] = array();
		if( ! empty( $input['reader'] ) ) {
			foreach( $input['reader'] as $role_allowed_to_read ) {
				if( get_role( $role_allowed_to_read ) ) {
					array_push( $valid['reader'], sanitize_key( $role_allowed_to_read ) );
				}
			}
		}

		// Assign/revoke capabilities
		$this->revoke_capabilities( $valid );
		$this->assign_capabilities( $valid );

		return $valid;

	}

	/**
	 * Validate data on Settings page - Custom CSS tab.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function validate_custom_css( $input ) {

		$valid = array();

		$valid['custom-css'] = $input['custom-css']; // No validation, because CodeMirror is doing it for us

		return $valid;

	}

	/**
	 * Validate data on Settings page - Advanced tab.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function validate_advanced( $input ) {

		$valid = array();

		$valid['delete_options'] = boolval( $input['delete_options'] );
		$valid['delete_documents'] = boolval( $input['delete_documents'] );

		return $valid;
		
	}

	/**
	 * Get post ID of a default document.
	 *
	 * @since 	 1.0.0
	 * @access   public
	 */
	public function get_default_document() {
		$oldest_docs = get_posts( array(
			'post_type' 		=> 'wp-help-docs',
			'posts_per_page' 	=> 1,
			'post_status'		=> array( 'publish', 'private' ),
			'orderby'          	=> 'menu_order',
			'order'            	=> 'ASC',
			'fields' 			=> 'ids',
			'suppress_filters'	=> false
		) );
		if( $oldest_docs ) {
			return $oldest_docs[0];
		} else {
			return null;
		}
	}

	/**
	 * Redirect to default document in case of wrong document ID.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function redirect_to_default_document() {

		// Get current or default document ID
		if ( isset( $_GET['document'] ) ) {

			$requested_id = absint( $_GET['document'] );

			// WPML compatibility
			if( class_exists( 'SitePress' ) ) {
				
				global $sitepress;
				$wpml_post_id = $sitepress->get_element_trid( $requested_id, 'post_wp-help-docs' );
				$translations = $sitepress->get_element_translations( $wpml_post_id, 'post_wp-help-docs', false, true );

				// Document doesn't exist
				if( ! $wpml_post_id ) {
					$default_document_id = $this->get_default_document();
					wp_redirect( add_query_arg( 'wphm-notice', 'not-found', admin_url( 'admin.php?page=wp-help-manager-documents&document=' . $default_document_id ) ) );
					exit;
				}

				// Document is not available for current language
				if( defined( 'ICL_LANGUAGE_CODE' ) && ! isset( $translations[ICL_LANGUAGE_CODE] ) ) {
					$default_document_id = $this->get_default_document();
					wp_redirect( add_query_arg( 'wphm-notice', 'translation-not-found', admin_url( 'admin.php?page=wp-help-manager-documents&document=' . $default_document_id ) ) );
					exit;
				}

			}

			// Document doesn't exist
			if( get_post_status( $requested_id ) === false ) {
				$default_document_id = $this->get_default_document();
				wp_redirect( add_query_arg( 'wphm-notice', 'not-found', admin_url( 'admin.php?page=wp-help-manager-documents&document=' . $default_document_id ) ) );
				exit;
			}

		}

	}

	/**
	 * Includes template for displaying published documents.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_documents_page() {

		// Get current or default document ID
		if ( isset( $_GET['document'] ) ) {

			$requested_id = absint( $_GET['document'] );
			
			// WPML compatibility
			if( class_exists( 'SitePress' ) ) {
				$requested_id = icl_object_id( $requested_id, 'wp-help-docs', false, ICL_LANGUAGE_CODE );
			}

			// Check if document exists
			if( get_post_status( $requested_id ) !== false ) {
				$document_id = $requested_id;
			} else {
				$document_id = false;
			}

		} else {
			$document_id = $this->get_default_document();
		}

		// Get search parameter
		$search_string = '';
		if( isset( $_GET['s'] ) ) {
			$search_string = esc_attr( $_GET['s'] );
		}

		include_once( 'partials/documents.php' );
	}

	/**
	 * Add admin notices.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	function add_admin_notices() {
		if ( isset( $_GET['wphm-notice'] ) ) {

			// No documents selected for export
			if ( $_GET['wphm-notice'] === 'empty-export' ) {
				?>
				<div class="notice notice-warning my-dismiss-notice is-dismissible wphm-notice">
					<p><?php esc_html_e( 'No documents selected.', 'wp-help-manager' ); ?></p>
				</div>
				<?php
			
			// Document not found
			} elseif( $_GET['wphm-notice'] === 'not-found' ) {
				?>
				<div class="notice notice-warning my-dismiss-notice is-dismissible wphm-notice">
					<p><?php esc_html_e( 'The requested document was not found. You\'ve been redirected to the default document.', 'wp-help-manager' ); ?></p>
				</div>
				<?php

			// Translation not found
			} elseif( $_GET['wphm-notice'] === 'translation-not-found' ) {
				?>
				<div class="notice notice-warning my-dismiss-notice is-dismissible wphm-notice">
					<p><?php esc_html_e( 'No translation is available for this document. You\'ve been redirected to the default document.', 'wp-help-manager' ); ?></p>
				</div>
				<?php

			// No admin selected in settings
			} elseif( $_GET['wphm-notice'] === 'no-admin-selected' ) {
				?>
				<div class="notice notice-warning my-dismiss-notice is-dismissible wphm-notice">
					<p><?php esc_html_e( 'Please select at least one plugin admin.', 'wp-help-manager' ); ?></p>
				</div>
				<?php
			}

		}
	}

	/**
	 * Add sort handles to document sidebar navigation.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function list_pages_add_handle( $html ) {
		if( $this->current_user_is_editor() ) {
			$html = preg_replace( '#<li [^>]+>#', '$0<span><img class="sort-handle" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAATklEQVQ4jWNgwA/sCcjjBQ0MDAz/GRgYOijRDMNkGcIA1YwXMJFrMgwwEmsTHr2jLiAAaB+NuAwoR7L9PwMDQz05hsMMIUszDFCUG4kCAJk9EHttc8pQAAAAAElFTkSuQmCC">', $html );
			$html = preg_replace( '#</a>#', '$0</span>', $html );
		} else {
			$html = preg_replace( '#<li [^>]+>#', '$0<span><img class="wphm-document-icon" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAQCAYAAAAmlE46AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDYuMC1jMDA2IDc5LmRhYmFjYmIsIDIwMjEvMDQvMTQtMDA6Mzk6NDQgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCAyMi40IChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozQ0RBRENGMjQ5NTIxMUVDODU3NTlERTQyNkEzNTkxNiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozQ0RBRENGMzQ5NTIxMUVDODU3NTlERTQyNkEzNTkxNiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjNDREFEQ0YwNDk1MjExRUM4NTc1OURFNDI2QTM1OTE2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjNDREFEQ0YxNDk1MjExRUM4NTc1OURFNDI2QTM1OTE2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+QfuNFwAAAVFJREFUeNqMU8Fqg0AQXTVf4KUe2thLIOQkFNs/8P/6FSL4Iz0LXnLzFAJqQdREjdt5i7OsWEoHHuPMzpt5O6olhHgmPBEc8bddCYWUcubE2zAM39M0PcgkQM8KHN/vdxnH8SfVvhJsIiviO0hd18m+7zXatpVN08iqqlSTsixlkiSazETVdRxH5QEm1nWtpt5uN4nmaZoqsrUQv+Z5FlmWCSJrEEHQmYiiSEmDRMuyhOM4HzskENi2LYIg0AVoBCJAk3Xsuq6q2fGGQM7zXE1AASbimQnwYRjqFe9MCafTaSWJJzPZNC0Vdj6ftTxzEjfgq6yk4vBwOOhpZh4k5OA3UmFFUegppkQmHY/HrVSQ9/u9MBux/81WW71cLqs7sTRu4Pv+hogTy/O8zVbZG0tU3fDNzXSPHt4sMOUiXnKo7Sj9QIuX5beyxf8ML/T6I8AAa2pisoVV6hgAAAAASUVORK5CYII=">', $html );
			$html = preg_replace( '#</a>#', '$0</span>', $html );
		}
		return $html;
	}

	/**
	 * Reorder documents and set parents.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function ajax_reorder() {
		if( check_ajax_referer( 'wphm-docs-reorder', 'security' ) ) {

			// Sanitize values
			$order = array();
			foreach( $_POST['order'] as $o ) {
				$order[] = array(
					'item_id' => intval( $o['id'] ),
					'parent_id' => intval( $o['parent_id'] )
				);
			}
			
			$val = -20;
			foreach ( $order as $o ) {
				$val += 10;

				// Check if post exists
				if( $p = get_post( $o['item_id'] ) ) {

					// Set parents for nested posts
					$new_parent = intval( $p->post_parent );
					if( get_post( $o['parent_id'] || $o['parent_id'] === 0 ) ) {
						$new_parent = $o['parent_id'];
					}

					// Set menu order
					$new_order = intval( $p->menu_order );
					if( intval( $p->menu_order ) !== $val ) {
						$new_order = $val;
					}

					// Update post
					$update_post = wp_update_post( array( 'ID' => $p->ID, 'menu_order' => $new_order, 'post_parent' => $new_parent ), true );
					
					/// Send success=false
					if( is_wp_error( $update_post ) ) {
						wp_send_json_error();
					}

				}

			}

			// Send success=true
			wp_send_json_success();

		}
	}

	/**
	 * Assign plugin capabilities.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public static function assign_capabilities( $permissions ) {
		
		// Add capabilities to allowed roles
		$allowed_roles = array_unique( array_merge( $permissions['reader'], $permissions['editor'] ) );

		if( $allowed_roles ) {
			foreach( $allowed_roles as $allowed_role ) {

				// Editor capabilities
				if( in_array( $allowed_role, $permissions['editor'] ) ) {
					$role = get_role( $allowed_role );
					$role->add_cap( 'edit_document' );
					$role->add_cap( 'read_document' );
					$role->add_cap( 'delete_document' );
					$role->add_cap( 'edit_documents' );
					$role->add_cap( 'edit_others_documents' );
					$role->add_cap( 'delete_documents' );
					$role->add_cap( 'publish_documents' );
					$role->add_cap( 'read_private_documents' );
					$role->add_cap( 'read_documents' );
					$role->add_cap( 'delete_private_documents' );
					$role->add_cap( 'delete_others_documents' );
					$role->add_cap( 'delete_published_documents' );
					$role->add_cap( 'edit_private_documents' );
					$role->add_cap( 'edit_published_documents' );
					$role->add_cap( 'create_documents' );
					$role->remove_cap( 'access_wphm_settings' );
				}

				// Reader capabilities
				elseif( in_array( $allowed_role, $permissions['reader'] ) ) {
					$role = get_role( $allowed_role );
					$role->remove_cap( 'edit_document' );
					$role->add_cap( 'read_document' );
					$role->remove_cap( 'delete_document' );
					$role->remove_cap( 'edit_documents' );
					$role->remove_cap( 'edit_others_documents' );
					$role->remove_cap( 'delete_documents' );
					$role->remove_cap( 'publish_documents' );
					$role->remove_cap( 'read_private_documents' );
					$role->add_cap( 'read_documents' );
					$role->remove_cap( 'delete_private_documents' );
					$role->remove_cap( 'delete_others_documents' );
					$role->remove_cap( 'delete_published_documents' );
					$role->remove_cap( 'edit_private_documents' );
					$role->remove_cap( 'edit_published_documents' );
					$role->remove_cap( 'create_documents' );
					$role->remove_cap( 'access_wphm_settings' );
				}
				

			}
		}

		// Remove capabilities from not allowed roles
		$all_roles = Wp_Help_Manager_Admin::get_all_user_roles();
		foreach( $all_roles as $role_slug => $role ) {
			if( ! in_array( $role_slug, $allowed_roles ) ) {
				$role = get_role( $role_slug );
				$role->remove_cap( 'edit_document' );
				$role->remove_cap( 'read_document' );
				$role->remove_cap( 'delete_document' );
				$role->remove_cap( 'edit_documents' );
				$role->remove_cap( 'edit_others_documents' );
				$role->remove_cap( 'delete_documents' );
				$role->remove_cap( 'publish_documents' );
				$role->remove_cap( 'read_private_documents' );
				$role->remove_cap( 'read_documents' );
				$role->remove_cap( 'delete_private_documents' );
				$role->remove_cap( 'delete_others_documents' );
				$role->remove_cap( 'delete_published_documents' );
				$role->remove_cap( 'edit_private_documents' );
				$role->remove_cap( 'edit_published_documents' );
				$role->remove_cap( 'create_documents' );
				$role->remove_cap( 'access_wphm_settings' );
			}
		}

		// Add capabilities to checked admin users
		if( $permissions['admin'] ) {
			foreach( $permissions['admin'] as $user_id ) {
				$user = new WP_User( $user_id );
				if ( in_array( 'administrator', (array) $user->roles ) ) {
					$user->add_cap( 'edit_document' );
					$user->add_cap( 'read_document' );
					$user->add_cap( 'delete_document' );
					$user->add_cap( 'edit_documents' );
					$user->add_cap( 'edit_others_documents' );
					$user->add_cap( 'delete_documents' );
					$user->add_cap( 'publish_documents' );
					$user->add_cap( 'read_private_documents' );
					$user->add_cap( 'read_documents' );
					$user->add_cap( 'delete_private_documents' );
					$user->add_cap( 'delete_others_documents' );
					$user->add_cap( 'delete_published_documents' );
					$user->add_cap( 'edit_private_documents' );
					$user->add_cap( 'edit_published_documents' );
					$user->add_cap( 'create_documents' );
					$user->add_cap( 'access_wphm_settings' );
				}
			}
		}

	}

	/**
	 * Revoke plugin capabilities.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public static function revoke_capabilities( $permissions ) {

		if( ! $permissions ) {
			$permissions = get_option( 'wp-help-manager-permissions' );
		}

		// Remove admin capabilities from unchecked/past admin users
		$admin_users = get_users( array(
			'role__in' 	=> 'access_wphm_settings'
		) );
		foreach( $admin_users as $admin_user ) {
			$user = new WP_User( $admin_user );
			if ( ! in_array( 'administrator', (array) $user->roles ) ) {
				$user->remove_cap( 'edit_document' );
				$user->remove_cap( 'read_document' );
				$user->remove_cap( 'delete_document' );
				$user->remove_cap( 'edit_documents' );
				$user->remove_cap( 'edit_others_documents' );
				$user->remove_cap( 'delete_documents' );
				$user->remove_cap( 'publish_documents' );
				$user->remove_cap( 'read_private_documents' );
				$user->remove_cap( 'read_documents' );
				$user->remove_cap( 'delete_private_documents' );
				$user->remove_cap( 'delete_others_documents' );
				$user->remove_cap( 'delete_published_documents' );
				$user->remove_cap( 'edit_private_documents' );
				$user->remove_cap( 'edit_published_documents' );
				$user->remove_cap( 'create_documents' );
				$user->remove_cap( 'access_wphm_settings' );
			}
		}

	}

	/**
	 * Check if current plugin admin still has administrator role -> if not, revoke capabilities.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function check_current_admin_capabilities() {

		if( $this->current_user_is_admin() ) {

			$user = wp_get_current_user();
			if ( ! in_array( 'administrator', (array) $user->roles ) ) {
				$user->remove_cap( 'edit_document' );
				$user->remove_cap( 'read_document' );
				$user->remove_cap( 'delete_document' );
				$user->remove_cap( 'edit_documents' );
				$user->remove_cap( 'edit_others_documents' );
				$user->remove_cap( 'delete_documents' );
				$user->remove_cap( 'publish_documents' );
				$user->remove_cap( 'read_private_documents' );
				$user->remove_cap( 'read_documents' );
				$user->remove_cap( 'delete_private_documents' );
				$user->remove_cap( 'delete_others_documents' );
				$user->remove_cap( 'delete_published_documents' );
				$user->remove_cap( 'edit_private_documents' );
				$user->remove_cap( 'edit_published_documents' );
				$user->remove_cap( 'create_documents' );
				$user->remove_cap( 'access_wphm_settings' );
			}

		}

	}

	/**
	 * Check for admin permissions.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function current_user_is_admin() {
		if( current_user_can( 'access_wphm_settings' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check for editor permissions.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function current_user_is_editor() {
		if( current_user_can( 'edit_documents' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check for reader permissions.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function current_user_is_reader() {
		if( current_user_can( 'read_documents' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Get allowed user roles.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_allowed_user_roles() {
		$permissions = get_option( 'wp-help-manager-permissions' );
		$allowed_roles = array_unique( array_merge( $permissions['admin'], $permissions['editor'], $permissions['reader'] ) );
		return $allowed_roles;
	}

	/**
	 * Get list of child documents.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_document_children( $id ) {
		$children = wp_list_pages( array(
			'post_type'         => 'wp-help-docs',
			'post_status'       => array( 'publish', 'private' ),
			'child_of'          => $id,
			'hierarchical'      => true,
			'echo'              => false,
			'title_li'          => ''
		) );
		$children = trim( $this->list_pages_add_icon( $children ) );
		return $children;
	}

	/**
	 * Includes template of plugin settings.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_settings_page() {
		include_once( 'partials/settings.php' );
	}

	/**
	 * Includes template of plugin tools.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_tools_page() {
		include_once( 'partials/tools.php' );
	}

	/**
	 * Add toolbar with navigation to all plugin pages.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_toolbar_menu() {

		// Get search parameter
		$search_string = '';
		if( isset( $_GET['s'] ) ) {
			$search_string = esc_attr( $_GET['s'] );
		}

		if( $this->is_plugin_page() === true ) {
			include_once( 'partials/toolbar.php' );
		}

	}

	/**
	 * Change left side of footer text on plugin admin pages.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function change_left_admin_footer_text( $footer_text ) {
		if( $this->is_plugin_page() === true ) {
			$plugin_footer_text = sprintf(
				'%s <a href="https://wordpress.org/plugins/wp-help-manager/" target="_blank">WP Help Manager</a>.',
				esc_html__( 'Thank you for creating with', 'wp-help-manager' )
			);
			return $plugin_footer_text;
		} else {
			return $footer_text;
		}
	}

	/**
	 * Remove right side of footer text on plugin admin pages.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function change_right_admin_footer_text( $footer_text ) {
		if( $this->is_plugin_page() === true ) {
			return __( 'Version', 'wp-help-manager' ) . ' ' . $this->version;
		} else {
			return $footer_text;
		}
	}

	/**
	 * Add dashboard widget.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function dashboard_setup() {
		$admin_settings = get_option( $this->plugin_name . '-admin' );
		if( ( isset( $admin_settings ) && isset( $admin_settings['dashboard_widget'] ) && $admin_settings['dashboard_widget'] ) || ( ! $admin_settings ) ) {

			// Make headline WPML translatable
			if( class_exists( 'SitePress' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
				$current_language = sanitize_key( ICL_LANGUAGE_CODE );
				$headline = ( isset( $admin_settings ) && isset( $admin_settings['headline_' . $current_language] ) && $admin_settings['headline_' . $current_language] !== '' ) ? esc_html( $admin_settings['headline_' . $current_language] ) : __( 'Publishing Help', 'wp-help-manager' );
			} else {
				$headline = ( isset( $admin_settings ) && isset( $admin_settings['headline'] ) && $admin_settings['headline'] !== '' ) ? esc_html( $admin_settings['headline'] ) : __( 'Publishing Help', 'wp-help-manager' );
			}

			if( get_posts( array( 'post_type' => 'wp-help-docs', 'post_status' => array( 'publish', 'private' ), 'posts_per_page' => 1, 'fields' => 'ids', 'suppress_filters' => false ) ) && $this->current_user_is_reader() )
				wp_add_dashboard_widget( 'wphm-dashboard-docs', esc_html( $headline ), array( $this, 'dashboard_widget' ) );
		}
	}
	public function dashboard_widget() {
		$docs = wp_list_pages( array(
			'post_type'         => 'wp-help-docs',
			'post_status'       => array( 'publish', 'private' ),
			'sort_column'		=> 'menu_order',
			'sort_order'		=> 'ASC',
			'hierarchical'      => true,
			'echo'              => false,
			'title_li'          => ''
		));
		$docs = trim( $this->list_pages_add_icon( $docs ) );
		include_once( 'partials/dashboard.php' );
	}

	/**
	 * Add document icons to document sidebar navigation.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function list_pages_add_icon( $html ) {
		$html = preg_replace( '#<a [^>]+>#', '$0<img class="wphm-document-icon" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA4AAAAQCAYAAAAmlE46AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDYuMC1jMDA2IDc5LmRhYmFjYmIsIDIwMjEvMDQvMTQtMDA6Mzk6NDQgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCAyMi40IChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDozQ0RBRENGMjQ5NTIxMUVDODU3NTlERTQyNkEzNTkxNiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDozQ0RBRENGMzQ5NTIxMUVDODU3NTlERTQyNkEzNTkxNiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjNDREFEQ0YwNDk1MjExRUM4NTc1OURFNDI2QTM1OTE2IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjNDREFEQ0YxNDk1MjExRUM4NTc1OURFNDI2QTM1OTE2Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+QfuNFwAAAVFJREFUeNqMU8Fqg0AQXTVf4KUe2thLIOQkFNs/8P/6FSL4Iz0LXnLzFAJqQdREjdt5i7OsWEoHHuPMzpt5O6olhHgmPBEc8bddCYWUcubE2zAM39M0PcgkQM8KHN/vdxnH8SfVvhJsIiviO0hd18m+7zXatpVN08iqqlSTsixlkiSazETVdRxH5QEm1nWtpt5uN4nmaZoqsrUQv+Z5FlmWCSJrEEHQmYiiSEmDRMuyhOM4HzskENi2LYIg0AVoBCJAk3Xsuq6q2fGGQM7zXE1AASbimQnwYRjqFe9MCafTaSWJJzPZNC0Vdj6ftTxzEjfgq6yk4vBwOOhpZh4k5OA3UmFFUegppkQmHY/HrVSQ9/u9MBux/81WW71cLqs7sTRu4Pv+hogTy/O8zVbZG0tU3fDNzXSPHt4sMOUiXnKo7Sj9QIuX5beyxf8ML/T6I8AAa2pisoVV6hgAAAAASUVORK5CYII="><span>', $html );
		$html = preg_replace( '#</a>#', '</span>$0', $html );
		return $html;
	}

	/**
	 * Add wrapper to tables to make them responsive.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	function responsive_tables( $content ) {
		$document_settings = get_option( $this->plugin_name . '-document' );
		if( ( isset( $document_settings ) && isset( $document_settings['format_tables'] ) && $document_settings['format_tables'] ) || ! $document_settings ) {
			$content = preg_replace( "/<table/Si", '<div class="table-wrapper"><table', $content );
			$content = preg_replace( "/<\/table>/Si", '</table></div>', $content );
		}
		return $content;
	}

	/**
	 * Get previous and next post navigation links (respecting post hierarchy).
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_post_navigation_links( $post_id ) {

		// Get all documents
		$pages = get_pages( array(
			'post_type'         => 'wp-help-docs',
			'post_status'       => array( 'publish', 'private' ),
			'sort_column'		=> 'menu_order',
			'sort_order'		=> 'ASC',
			'hierarchical'      => true
		));

		// Get last index
		$total = 0;
		$last_index = 0;
		if( $pages ) {
			$total = count( $pages );
			$last_index = $total - 1;
		}

		// Get index of current post
		$i = 0;
		foreach( $pages as $page ) {
			if( $page->ID === $post_id ) {
				$post_index = $i;
			}
			$i++;
		}

		// Get previous link
		$prev_post_index = false;
		$prev_post = false;
		if( isset( $post_index ) ) {
			if( $post_index > 0 ) {
				$prev_post_index = $post_index - 1;
				$prev_post = $pages[ $prev_post_index ];
			}
		}

		// Get next link
		$prev_post_index = false;
		$next_post = false;
		if( isset( $post_index ) ) {
			if( $post_index < $last_index ) {
				$next_post_index = $post_index + 1;
				$next_post = $pages[ $next_post_index ];
			}
		}

		$document_navigation = array(
			'prev_post' => $prev_post,
			'next_post' => $next_post
		);

		return (object) $document_navigation;

	}

	/**
	 * Automatically add IDs to headings.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author	 Jeroen Sormani
	 * @link	 https://jeroensormani.com/automatically-add-ids-to-your-headings/
	 */
	function auto_id_headings( $content ) {
		if( get_post_type() === 'wp-help-docs' ) {
			$content = preg_replace_callback( '/(\<h[1-6](.*?))\>(.*)(<\/h[1-6]>)/i', function( $matches ) {
				if ( ! stripos( $matches[0], 'id=' ) ) :
					$matches[0] = $matches[1] . $matches[2] . ' id="' . sanitize_title( $matches[3] ) . '">' . $matches[3] . $matches[4];
				endif;
				return $matches[0];
			}, $content );
			return $content;
		}
	}

	/**
	 * Get all user roles.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public static function get_all_user_roles() {
		global $wp_roles;
		$roles = $wp_roles->roles;
		return $roles;
	}

	/**
	 * Handle import/export of help documents.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function plugin_tools() {

		// Export documents
		if( isset( $_POST['action'] ) && $_POST['action'] === 'export_help_documents' ) {
			if( isset( $_POST['wphm_docs'] ) ) {
				if( check_admin_referer( 'wphm_export_form', 'wphm_export_nonce' ) ) {
					$this->export_help_documents();
				}
			} else {
				wp_redirect( admin_url( '/admin.php?page=wp-help-manager-tools&wphm-notice=empty-export' ) );
				exit;
			}
		}

	}

	/**
	 * Get active WordPress Importer plugin URL
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   mixed    False if not installed, string if installed/activated.
	 */
	public function get_importer_status() {
		$importer_status = false;
		$installed_plugins = get_plugins();
		if( $installed_plugins ) {
			foreach( $installed_plugins as $plugin_filename => $plugin ) {
				if( $plugin['TextDomain'] == 'wordpress-importer' ) {
					$importer_status = 'installed';
					if( is_plugin_active( $plugin_filename ) ) {
						$importer_status = 'active';
						break;
					}
				}
			}
		}
		return $importer_status;
	}

	/**
	 * Get filename of first installed WordPress Importer 
	 *
	 * @since    1.0.0
	 * @access   public
	 * @return   mixed    False if not installed, string if found.
	 */
	public function get_installed_importer_filename() {
		$importer_filename = false;
		$installed_plugins = get_plugins();
		if( $installed_plugins ) {
			foreach( $installed_plugins as $plugin_filename => $plugin ) {
				if( $plugin['TextDomain'] == 'wordpress-importer' ) {
					$importer_filename = $plugin_filename;
					break;
				}
			}
		}
		return $importer_filename;
	}

	/**
	 * Get URL for installing the WordPress Importer plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_importer_install_url() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return false;
		}
		$install_url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'install-plugin',
					'plugin' => 'wordpress-importer',
					'from'   => 'import',
				),
				self_admin_url( 'update.php' )
			),
			'install-plugin_wordpress-importer'
		);
		return $install_url;
	}

	/**
	 * Get URL for activating the WordPress Importer plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_importer_activate_url( $filename ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return false;
		}
		$activate_url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'activate',
					'plugin' => $filename,
					'from'   => 'import',
				),
				self_admin_url( 'plugins.php' )
			),
			'activate-plugin_' . $filename
		);
		return $activate_url;
	}
	
	/**
	 * Export help documents.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function export_help_documents() {
		require_once ABSPATH . 'wp-admin/includes/export.php';
		
		// Change export filename
		add_filter( 'export_wp_filename', array( $this, 'export_change_filename' ), 10, 3 );

		// Increase menu order for all items before the export is executed (so on website where documents are imported documents show on the end of list and don't mess the existing order)
		$documents = get_posts( array(
			'post_type' 		=> 'wp-help-docs',
			'fields' 			=> 'ids',
			'numberposts' 		=> -1,
			'suppress_filters' 	=> false
		) );
		foreach( $documents as $document ) {
			$document_obj = get_post( $document );
			if( $document_obj ) {
				$new_menu_order = $document_obj->menu_order + 10000; 
				wp_update_post( array( 
					'ID' => $document_obj->ID,
					'menu_order' => $new_menu_order
				), true );
			}
		}

		// Execute export
		export_wp( array( 'content' => 'wp-help-docs' ) );

		// Change menu order back to original values
		foreach( $documents as $document ) {
			$document_obj = get_post( $document );
			if( $document_obj ) {
				$original_menu_order = $document_obj->menu_order - 10000; 
				wp_update_post( array( 
					'ID' => $document_obj->ID,
					'menu_order' => $original_menu_order
				), true );
			}
		}

		die();
	}

	/**
	 * Change export filename.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function export_change_filename( $wp_filename, $sitename, $date ) {
		$wp_filename = 'wp-help-export-' . $date . '.xml';
		return $wp_filename;
	}

	/**
	 * Modify the export query.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function modify_export_query( $args ) {
		if( isset( $_POST['action'] ) && $_POST['action'] === 'export_help_documents' && isset( $_POST['wphm_docs'] ) ) {
			add_filter( 'query', array( $this, 'filter_exported_docs' ) );
		}
		return $args;
	}

	/**
	 * Inject sub-query to export documents with specific IDs
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function filter_exported_docs( $query ) {
		global $wpdb;
		$exported_docs = implode( ',', $_POST['wphm_docs'] );

		// Make sure that we target only the query for our post type
		if( false === strpos( $query, "{$wpdb->posts}.post_type = 'wp-help-docs'" ) ) 
			return $query;

		// Remove filter callback
    	remove_filter( current_filter(), __FUNCTION__ );

		// Inject sub-query to export only selected documents
		$sql = " {$wpdb->posts}.ID IN ($exported_docs) AND ";

		return str_replace( ' WHERE ', ' WHERE ' . $sql, $query );
	}

}
