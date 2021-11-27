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
			|| isset( $screen->id ) && $screen->id === 'toplevel_page_wp-help-manager-settings'
			|| isset( $screen->id ) && $screen->id === 'edit-wp-help-docs'
			|| ( isset( $screen->id ) && $screen->id === 'wp-help-docs' && $screen->is_block_editor == false ) 
		) {

			// Add help tab
			$this->setup_help_tab();

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
	 * Determine if the current page is plugins settings view.
	 *
	 * @since    1.0.0
	 */
	public function is_plugin_settings_page() {

		$screen = get_current_screen();
		if ( isset( $screen->id ) && $screen->id === 'toplevel_page_wp-help-manager-settings' ) {
			return true;
		}
		return false;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if( $this->is_plugin_page() === true ) {

			// General admin CSS
			wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css', array(), $this->version, 'all' );

		}
		
		if( $this->is_plugin_documents_page() === true ) {
			
			// Documents CSS libraries
			wp_enqueue_style( $this->plugin_name . '-documents-libs', plugin_dir_url( __FILE__ ) . 'assets/css/documents-libs.css', array(), $this->version, 'all' );
			
			// Documents main CSS
			wp_enqueue_style( $this->plugin_name . '-documents', plugin_dir_url( __FILE__ ) . 'assets/css/documents.css', array( $this->plugin_name . '-documents-libs' ), $this->version, 'all' );
		
		} elseif( $this->is_plugin_settings_page() === true ) {

			// CodeMirror editor CSS
			wp_enqueue_style( 'wp-codemirror' );

			// Settings CSS libraries
			// wp_register_style( $this->plugin_name . '-settings-libs', plugin_dir_url( __FILE__ ) . 'assets/css/settings-libs.css', array(), $this->version, 'all' );

			// Settings main CSS
			wp_enqueue_style( $this->plugin_name . '-settings', plugin_dir_url( __FILE__ ) . 'assets/css/settings.css', array(), $this->version, 'all' );

		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if( $this->is_plugin_documents_page() === true ) {
			
			// Documents JS libraries
			wp_enqueue_script( $this->plugin_name . '-documents-libs', plugin_dir_url( __FILE__ ) . 'assets/js/documents-libs.js', array(), $this->version, false );

			// NestedSortable plugin for jQuery UI Sortable
			wp_register_script( $this->plugin_name . '-nestedsortable', plugin_dir_url( __FILE__ ) . 'assets/js/nested-sortable.js', array(), $this->version, false );

			// Documents main JS
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/documents.js', array( 'jquery-core', 'jquery-ui-sortable', $this->plugin_name . '-nestedsortable', $this->plugin_name . '-documents-libs' ), $this->version, false );
		
		} elseif( $this->is_plugin_settings_page() === true ) {

			// CodeMirror editor JS
			$cm_settings['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
			wp_localize_script( 'jquery', 'cm_settings', $cm_settings );
			wp_enqueue_script( 'wp-theme-plugin-editor' );

			// Settings JS libraries
			// wp_register_script( $this->plugin_name . '-settings-libs', plugin_dir_url( __FILE__ ) . 'assets/js/settings-libs.js', array(), $this->version, $this->version, false );
			
			// Settings main JS
			wp_enqueue_script( $this->plugin_name . '-settings', plugin_dir_url( __FILE__ ) . 'assets/js/settings.js', array(), $this->version, $this->version, false );

		}

	}

	/**
	 * Add top level menu item.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function add_plugin_admin_menu() {

		if( current_user_can( 'read_document' ) ) {

			// Top level menu item
			add_menu_page(
				__( 'Publishing Help', 'wp-help-manager' ),
				__( 'Publishing Help', 'wp-help-manager' ),
				'read',
				'wp-help-manager-documents',
				array( $this, 'display_documents_page' ),
				'dashicons-editor-help',
				2
			);

			// Submenu item - Settings
			add_menu_page(
				__( 'WP Help Manager Settings', 'wp-help-manager' ),
				__( 'WP Help Manager Settings', 'wp-help-manager' ),
				'read',
				'wp-help-manager-settings',
				array( $this, 'display_settings_page' )
			);

		}

	}

	/**
	 * Remove post type menu items.
	 *
	 * @since 1.0.0
	 * @access   public
	 */
	public function remove_plugin_admin_menu() {
		remove_menu_page( 'wp-help-manager-settings' );
	}

	/**
	 * Register plugin settings using the Settings API.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function options_update() {

		register_setting(
			$this->plugin_name . '-admin',
			$this->plugin_name . '-admin',
			array( $this, 'validate_admin' )
		);

		register_setting(
			$this->plugin_name . '-document',
			$this->plugin_name . '-document',
			array( $this, 'validate_document' )
		);

		register_setting(
			$this->plugin_name . '-permissions',
			$this->plugin_name . '-permissions',
			array( $this, 'validate_permissions' )
		);

		register_setting(
			$this->plugin_name . '-custom-css',
			$this->plugin_name . '-custom-css',
			array( $this, 'validate_custom_css' )
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
		$valid['menu_icon'] = ( in_array( $input['menu_icon'], $dashicons ) ) 
			? sanitize_key( $input['menu_icon'] ) 
			: 'dashicons-help';
		$valid['menu_position'] = abs( intval( $input['menu_position'] ) ) !== 0 
			? abs( intval( $input['menu_position'] ) ) 
			: 1;
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
		foreach( $input['admin'] as $user_id ) {
			$user = get_userdata( $user_id );
			if( $user && $user->roles ) {
				if( in_array( 'administrator', (array) $user->roles ) ) {
					array_push( $valid['admin'], intval( $user_id ) );
				}
			}
		}

		$valid['editor'] = array();
		foreach( $input['editor'] as $role_allowed_to_edit ) {
			if( get_role( $role_allowed_to_edit ) ) {
				array_push( $valid['editor'], sanitize_key( $role_allowed_to_edit ) );
			}
		}

		$valid['reader'] = array();
		foreach( $input['reader'] as $role_allowed_to_read ) {
			if( get_role( $role_allowed_to_read ) ) {
				array_push( $valid['reader'], sanitize_key( $role_allowed_to_read ) );
			}
		}

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

		$valid['custom-css'] = $input['custom-css'];

		return $valid;

	}

	/**
	 * Assign capabilities to default user roles.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function assign_capabilities() {
		
		$permissions = get_option( 'wp-help-manager-permissions' );

		// @todo check if admin has administrator role, if not, remove caps
		// maybe easier will be to add custom role for plugin super user and then be checking it
		// rewrite permissions for pages/functions

		// Admin capabilities
		if( $permissions['admin'] ) {
			foreach( $permissions['admin'] as $user_id ) {
				$user = new WP_User( $user_id );
				if ( in_array( 'administrator', (array) $user->roles ) ) {
					$user->add_cap( 'read_document' );
					$user->add_cap( 'read_private_documents' );
					$user->add_cap( 'edit_document' );
					$user->add_cap( 'edit_documents' );
					$user->add_cap( 'edit_others_documents' );
					$user->add_cap( 'publish_documents' );
					$user->add_cap( 'delete_document' );
					$user->add_cap( 'access_help_documents_settings' );
				}
			}
		}

		// Editor capabilities
		if( $permissions['editor'] ) {
			foreach( $permissions['editor'] as $editor_role ) {
				$role = get_role( $editor_role );
				$role->add_cap( 'read_document' );
				$role->add_cap( 'read_private_documents' );
				$role->add_cap( 'edit_document' );
				$role->add_cap( 'edit_documents' );
				$role->add_cap( 'edit_others_documents' );
				$role->add_cap( 'publish_documents' );
				$role->add_cap( 'delete_document' );
			}
		}

		// Reader capabilities
		if( $permissions['reader'] ) {
			foreach( $permissions['reader'] as $reader_role ) {
				$role = get_role( $reader_role );
				$role->add_cap( 'read_document' );
			}
		}

	}

	/**
	 * Add option to set default document to publish meta box.
	 *
	 * @since 1.0.0
	 * @access   public
	 */
	public function add_option_default_document() {
		if ( get_post_type() === 'wp-help-docs' ) {
			include_once( 'partials/submitbox.php' );
		}
	}

	/**
	 * Set/unset default document on document save.
	 *
	 * @since 1.0.0
	 * @access   public
	 */
	public function save_document( $post_id ) {
		if ( isset( $_POST['wphm-save-nonce'] ) && wp_verify_nonce( $_POST['wphm-save-nonce'], 'wphm-save-' . $post_id ) ) {
			if ( isset( $_POST['wphm_make_default_document'] ) ) {
				$this->set_default_document( $post_id );
			} elseif ( $this->is_default_document( $post_id ) ) {
				$this->unset_default_document();
			}
		}
		return $post_id;
	}

	/**
	 * Get post ID of a default document.
	 *
	 * @since 1.0.0
	 * @access   public
	 */
	public function get_default_document() {
		$default_document = absint( get_option( 'wphm_default_document', 0 ) );
		if( ! $default_document ) {
			$oldest_docs = get_posts( array(
				'post_type' 		=> 'wp-help-docs',
				'posts_per_page' 	=> 1,
				'post_status'		=> array( 'publish', 'private' ),
				'orderby'          	=> 'menu_order',
				'order'            	=> 'ASC',
				'fields' 			=> 'ids'
			) );
			$default_document = $oldest_docs[0];
		}
		return $default_document;
	}

	/**
	 * Check if post is set as a default document.
	 *
	 * @since 1.0.0
	 * @access   public
	 */
	public function is_default_document( $post = null ) {
		$post = get_post( $post );
		return $post->ID === $this->get_default_document();
	}

	/**
	 * Set post as default document.
	 *
	 * @since 1.0.0
	 * @access   public
	 */
	public function set_default_document( $post ) {
		$post = get_post( $post );
		$id = absint( $post ? $post->ID : 0 );
		return update_option( 'wphm_default_document', $id );
	}

	/**
	 * Unset default document.
	 *
	 * @since 1.0.0
	 * @access   public
	 */
	public function unset_default_document() {
		return update_option( 'wphm_default_document', 0 );
	}

	/**
	 * Includes template for displaying published documents.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function display_documents_page() {

		// Get current of default document ID
		if ( isset( $_GET['document'] ) ) {
			$document_id = absint( $_GET['document'] );
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

			// Trashed document
			if ( $_GET['wphm-notice'] === 'trashed' && isset( $_GET['wphm-id'] ) ) {
				$trashed_id = intval( $_GET['wphm-id'] );
				?>
				<div class="notice notice-success my-dismiss-notice is-dismissible wphm-notice" data-close="<?php echo remove_query_arg( array( 'wphm-notice', 'wphm-id' ) ); ?>">
					<p><?php esc_html_e( 'Document moved to the Trash.', 'wp-help-manager' ); ?> <span class="wphm-action-text wphm-action-untrash" data-id="<?php echo esc_attr( $trashed_id ); ?>" data-nonce="<?php echo wp_create_nonce( 'untrash-document' ); ?>"><?php esc_html_e( 'Undo', 'wp-help-manager' ); ?></span></p>
				</div>
				<?php
			}

			// Untrashed document
			if ( $_GET['wphm-notice'] === 'untrashed' && isset( $_GET['wphm-id'] ) ) {
				$untrashed_id = intval( $_GET['wphm-id'] );
				?>
				<div class="notice notice-success my-dismiss-notice is-dismissible wphm-notice" data-close="<?php echo remove_query_arg( array( 'wphm-notice', 'wphm-id' ) ); ?>">
					<p><?php esc_html_e( 'Document restored from the Trash.', 'wp-help-manager' ); ?></p>
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
		$html = preg_replace( '#<li [^>]+>#', '$0<span><img class="sort-handle" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAATklEQVQ4jWNgwA/sCcjjBQ0MDAz/GRgYOijRDMNkGcIA1YwXMJFrMgwwEmsTHr2jLiAAaB+NuAwoR7L9PwMDQz05hsMMIUszDFCUG4kCAJk9EHttc8pQAAAAAElFTkSuQmCC">', $html );
		$html = preg_replace( '#</a>#', '$0</span>', $html );
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
	 * Check for admin permissions.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function current_user_is_admin() {
		$user_id = get_current_user_id();
		$permissions = get_option( 'wp-help-manager-permissions' );
		$is_admin = false;
		if( in_array( $user_id, $permissions['admin'] ) )
			$is_admin = true;
		return $is_admin;
	}

	/**
	 * Check for editor permissions.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function current_user_is_editor() {
		$user = wp_get_current_user();
		$permissions = get_option( 'wp-help-manager-permissions' );
		$is_editor = false;
		foreach( $user->roles as $role ) {
			if( in_array( $role, $permissions['editor'] ) )
				$is_editor = true;
		}
		return $is_editor;
	}

	/**
	 * Check for reader permissions.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function current_user_is_reader() {
		$user = wp_get_current_user();
		$permissions = get_option( 'wp-help-manager-permissions' );
		$is_reader = false;
		foreach( $user->roles as $role ) {
			if( in_array( $role, $permissions['reader'] ) )
				$is_reader = true;
		}
		return $is_reader;
	}

	/**
	 * Get allowed user roles.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_allowed_user_roles() {
		$permissions = get_option( 'wp-help-manager-permissions' );
		$allowed_rules = array_unique( array_merge( $permissions['admin'], $permissions['editor'], $permissions['reader'] ) );
		return $allowed_rules;
	}

	/**
	 * Trash document.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function ajax_trash_document() {
		if( check_ajax_referer( 'trash-document', 'security' ) ) {
			$document_id = intval( $_POST['id'] );
			if( get_post_type( $document_id ) === 'wp-help-docs' ) {
				if( wp_trash_post( $document_id ) ) {
					wp_send_json_success( add_query_arg( array( 'wphm-notice' => 'trashed', 'wphm-id' => $document_id 
					), get_permalink( $this->get_default_document() ) ) );
				}
			}
		}
		wp_send_json_error();
	}

	/**
	 * Untrash document.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function ajax_untrash_document() {
		if( check_ajax_referer( 'untrash-document', 'security' ) ) {
			$document_id = intval( $_POST['id'] );
			if( get_post_type( $document_id ) === 'wp-help-docs' ) {
				if( wp_untrash_post( $document_id ) && wp_update_post( array( 'ID' => $document_id, 'post_status' => 'publish') ) ) {
					wp_send_json_success( add_query_arg( array( 'wphm-notice' => 'untrashed', 'wphm-id' => $document_id ), get_permalink( $document_id ) ) );
				}
			}
		}
		wp_send_json_error();
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
		$screen = get_current_screen();

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
				'%s <a href="https://wordpress.org/" target="_blank">WordPress</a> and <a href="https://www.wphelpmanager.com" target="_blank">WP Help Manager</a>.',
				esc_html__( 'Thank you for creating with', 'wp-help-manager' ),
				esc_html__( 'and', 'wp-help-manager' )
			);
			return $plugin_footer_text;
		} else {
			return $footer_text;
		}
	}

	/**
	 * Sets up the admin help tab.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function setup_help_tab() {
		$screen = get_current_screen();

		// Overview tab.
		// $screen->add_help_tab(
		// 	array(
		// 		'id'      => 'overview',
		// 		'title'   => __( 'Overview', 'wp-help-manager' ),
		// 		'content' =>
		// 			'<p><strong>' . __( 'Overview', 'wp-help-manager' ) . '</strong></p>' .
		// 			'<p>' . __( 'Lorem ipsum dolor sit amet.' ) . '</p>'
		// 	)
		// );

		// Help tab.
		// $screen->add_help_tab(
		// 	array(
		// 		'id'      => 'help',
		// 		'title'   => __( 'Help & Support', 'wp-help-manager' ),
		// 		'content' =>
		// 			'<p><strong>' . __( 'Help & Support', 'wp-help-manager' ) . '</strong></p>' .
		// 			'<p>' . __( 'We are fanatical about support, and want you to get the best out of your website with ACF. If you run into any difficulties, there are several places you can find help:', 'wp-help-manager' ) . '</p>' .
		// 			'<ul>' .
		// 				'<li>' . sprintf(
		// 					__( '<a href="%s" target="_blank">Documentation</a>. Our extensive documentation contains references and guides for most situations you may encounter.', 'wp-help-manager' ),
		// 					'https://www.advancedcustomfields.com/resources/'
		// 				) . '</li>' .
		// 				'<li>' . sprintf(
		// 					__( '<a href="%s" target="_blank">Discussions</a>. We have an active and friendly community on our Community Forums who may be able to help you figure out the ‘how-tos’ of the ACF world.', 'wp-help-manager' ),
		// 					'https://support.advancedcustomfields.com/'
		// 				) . '</li>' .
		// 				'<li>' . sprintf(
		// 					__( '<a href="%s" target="_blank">Help Desk</a>. The support professionals on our Help Desk will assist with your more in depth, technical challenges.', 'wp-help-manager' ),
		// 					'https://www.advancedcustomfields.com/support/'
		// 				) . '</li>' .
		// 			'</ul>',
		// 	)
		// );

		// Sidebar.
		// $screen->set_help_sidebar(
		// 	'<p><strong>' . __( 'WP Help Manager', 'wp-help-manager' ) . '</strong></p>' .
		// 	'<p><span class="dashicons dashicons-admin-plugins"></span> ' . sprintf( __( 'Version %s', 'wp-help-manager' ), $this->version ) . '</p>' .
		// 	'<p><span class="dashicons dashicons-wordpress"></span> <a href="https://wordpress.org/plugins/wp-help-manager/" target="_blank">' . __( 'View details', 'wp-help-manager' ) . '</a></p>' .
		// 	'<p><span class="dashicons dashicons-admin-home"></span> <a href="https://www.wphelpmanager.com/" target="_blank">' . __( 'Visit website', 'wp-help-manager' ) . '</a></p>' .
		// 	''
		// );
	}

	/**
	 * Add dashboard widget.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function dashboard_setup() {
		if( current_user_can( 'read_document' ) ) {
			wp_add_dashboard_widget( 'wphm-dashboard-docs', 'Publishing Help', array( $this, 'dashboard_widget' ) );
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
		$content = preg_replace( "/<table/Si", '<div class="table-wrapper"><table', $content );
		$content = preg_replace( "/<\/table>/Si", '</table></div>', $content );
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
	 * Get all user roles.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function get_all_user_roles() {
		global $wp_roles;
		$roles = $wp_roles->roles;
		return $roles;
	}

}
