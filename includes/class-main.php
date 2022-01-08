<?php

namespace Help_Manager;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://bohemiaplugins.com/
 * @since      1.0.0
 *
 * @package    Help_Manager
 * @subpackage Help_Manager/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Help_Manager
 * @subpackage Help_Manager/includes
 * @author     Bohemia Plugins <contact@bohemiaplugins.com>
 */
class Help_Manager {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Help_Manager_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'HELP_MANAGER_VERSION' ) ) {
			$this->version = HELP_MANAGER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'help-manager';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Help_Manager_Loader. Orchestrates the hooks of the plugin.
	 * - Help_Manager_i18n. Defines internationalization functionality.
	 * - Help_Manager_Admin. Defines all hooks for the admin area.
	 * - Help_Manager_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-i18n.php';

		/**
		 * The class responsible for defining custom post type.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-post-type.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-admin.php';

		$this->loader = new Help_Manager_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Help_Manager_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Help_Manager_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Help_Manager_Admin( $this->get_plugin_name(), $this->get_version() );
		$post_type = new Help_Manager_Post_Type();

		// Enqueue plugin scripts and styles
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'current_screen', $plugin_admin, 'remove_classic_editor_styles' );
		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_admin, 'enqueue_block_editor_assets', 20 );
		
		// Add admin bar CSS to both front-end and back-end
		$this->loader->add_action( 'admin_head', $plugin_admin, 'custom_admin_css' );
		
		// Add custom CSS to document page
		$this->loader->add_action( 'admin_head', $plugin_admin, 'custom_document_css' );

		// Add custom CSS to admin dashboard
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'dashboard_widget_css' );
		
		// Register custom post type
		$this->loader->add_action( 'init', $post_type, 'register_post_type' );

		// Change custom post type permalink
		$this->loader->add_filter( 'post_type_link', $post_type, 'post_link', 1, 2 );

		// Remove post type from sitemap
		$this->loader->add_filter( 'wp_sitemaps_post_types', $post_type, 'remove_post_type_from_sitemap', 10, 2 );
		$this->loader->add_filter( 'wpseo_sitemap_exclude_post_type', $post_type, 'remove_post_type_from_yoast_sitemap', 10, 2 );

		// Set order on document save
		$this->loader->add_filter( 'wp_insert_post_data', $plugin_admin, 'set_menu_order_for_new_document', 10, 2 );

		// Revoke past user capabilities
		$this->loader->add_action( 'admin_init', $plugin_admin, 'check_current_admin_capabilities' );

		// Save/update plugin settings
		$this->loader->add_action( 'admin_init', $plugin_admin, 'options_update' );

		// Handle import/export of help documents
		$this->loader->add_action( 'admin_init', $plugin_admin, 'plugin_tools' );
		$this->loader->add_action( 'export_wp', $plugin_admin, 'modify_export_query' );

		// Add menu items
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		// Modify plugin menu
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'modify_plugin_menu' );

		// Highlight menu item on document edit page
		$this->loader->add_filter( 'parent_file', $plugin_admin, 'highlight_menu_on_document_edit_page' );

		// Add admin bar menu
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'admin_bar_menu', 100 );

		// Add toolbar to the admin pages
		$this->loader->add_action( 'in_admin_header', $plugin_admin, 'add_toolbar_menu' );

		// Change admin footer text
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'change_left_admin_footer_text' );
		$this->loader->add_filter( 'update_footer', $plugin_admin, 'change_right_admin_footer_text', 11 );

		// Ad admin notices
		$this->loader->add_filter( 'admin_notices', $plugin_admin, 'add_admin_notices' );

		// Add dashboard widget
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'dashboard_setup' );


		// Ajax reoder documents
		$this->loader->add_action( 'wp_ajax_wphm_docs_reorder', $plugin_admin, 'ajax_reorder' );

		// Responsive tables
		$this->loader->add_filter( 'the_content', $plugin_admin, 'responsive_tables' );

		// Automaticlly add IDs to headings
		$this->loader->add_filter( 'the_content', $plugin_admin, 'auto_id_headings' );

		// Redirect in case of wrong document ID
		$this->loader->add_action( 'admin_init', $plugin_admin, 'redirect_to_default_document' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Help_Manager_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
