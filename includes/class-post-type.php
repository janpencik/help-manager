<?php

namespace Wp_Help_Manager;

/**
 * Register custom post type for the plugin.
 *
 * @link       https://bohemiaplugins.com/
 * @since      1.0.0
 *
 * @package    Wp_Help_Manager
 * @subpackage Wp_Help_Manager/includes
 */

class Wp_Help_Manager_Post_Type {

    /**
    * The slug of the created post type.
    *
    * @since    1.0.0
    * @access   protected
    * @var      string    $post_type_slug    The slug of the created post type.
    */
    protected $post_type_slug;

    /**
    * The slug of the created taxonomy.
    *
    * @since    1.0.0
    * @access   protected
    * @var      string    $post_type_slug    The slug of the created post type.
    */
    protected $taxonomy_slug;

   /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name       The name of the plugin.
	 * @param    string    $post_type_slug    The slug of the created post type.
	 */
    public function __construct() {

        $this->post_type_slug = 'wp-help-docs';
        $this->taxonomy_slug = 'wp-help-category';

    }

    /**
	 * Register custom post type.
	 *
	 * @since    1.0.0
     * @access   public
	 */
    public function register_post_type() {      

        $labels = array(
            'name'                  => _x( 'Help Documents', 'post type general name', 'wp-help-manager' ),
            'singular_name'         => _x( 'Help Document', 'post type singular name', 'wp-help-manager' ),
            'menu_name'             => __( 'Help Documents', 'wp-help-manager' ),
            'name_admin_bar'        => __( 'Help Documents', 'wp-help-manager' ),
            'add_new'               => __( 'Add New', 'wp-help-manager' ),
            'add_new_item'          => __( 'Add New Help Document', 'wp-help-manager' ),
            'new_item'              => __( 'New Help Document', 'wp-help-manager' ),
            'edit_item'             => __( 'Edit Help Document', 'wp-help-manager' ),
            'view_item'             => __( 'View Help Document', 'wp-help-manager' ),
            'all_items'             => __( 'Help Documents', 'wp-help-manager' ),
            'search_items'          => __( 'Search Help Documents', 'wp-help-manager' ),
            'parent_item_colon'     => __( 'Parent Help Documents:', 'wp-help-manager' ),
            'not_found'             => __( 'No Help Documents found.', 'wp-help-manager' ),
            'not_found_in_trash'    => __( 'No Help Documents found in Trash.', 'wp-help-manager' ),
            'archives'              => __( 'Help Documents archives', 'wp-help-manager' ),
            'insert_into_item'      => __( 'Insert into Help Document', 'wp-help-manager' ),
            'uploaded_to_this_item' => __( 'Uploaded to this Help Document', 'wp-help-manager' ),
            'filter_items_list'     => __( 'Filter Help Document list', 'wp-help-manager' ),
            'items_list_navigation' => __( 'Help Documents list navigation', 'wp-help-manager' ),
            'items_list'            => __( 'Help Documents list', 'wp-help-manager' )
        );
     
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'show_in_rest'       => true,
            'hierarchical'       => true,
            'menu_position'      => 2,
            'menu_icon'          => 'dashicons-editor-help',
            'supports'           => array( 'title', 'editor', 'revisions', 'page-attributes' ),
        );
     
        register_post_type( $this->post_type_slug, $args );
        
    }

    /**
	 * Unregister post type.
	 *
	 * @since    1.0.0
     * @access   public
	 */
    public function unregister_post_type() {
        
        unregister_post_type( $this->post_type_slug );

    }

    /**
	 * Modify post type permalink.
	 *
	 * @since    1.0.0
     * @access   public
	 */
    public function post_link( $link, $post ) {

		$post = get_post( $post );
		if ( $post->post_type == $this->post_type_slug ) {
			return admin_url( 'admin.php?page=wp-help-manager-documents' ) . '&document=' . absint( $post->ID );
        } else {
			return $link;
        }

	}

    /**
	 * Register custom taxonomy.
	 *
	 * @since    1.0.0
     * @access   public
	 */
    // function register_wp_help_manager_taxonomy() {

    //     $labels = array(
    //         'name'              => _x( 'Categories', 'taxonomy general name', 'wp-help-manager' ),
    //         'singular_name'     => _x( 'Category', 'taxonomy singular name', 'wp-help-manager' ),
    //         'search_items'      => __( 'Search Categories', 'wp-help-manager' ),
    //         'all_items'         => __( 'All Categories', 'wp-help-manager' ),
    //         'parent_item'       => __( 'Parent Category', 'wp-help-manager' ),
    //         'parent_item_colon' => __( 'Parent Category:', 'wp-help-manager' ),
    //         'edit_item'         => __( 'Edit Category', 'wp-help-manager' ),
    //         'update_item'       => __( 'Update Category', 'wp-help-manager' ),
    //         'add_new_item'      => __( 'Add New Category', 'wp-help-manager' ),
    //         'new_item_name'     => __( 'New Category Name', 'wp-help-manager' ),
    //         'menu_name'         => __( 'Category', 'wp-help-manager' ),
    //     );

    //     $args   = array(
    //         'hierarchical'      => true,
    //         'labels'            => $labels,
    //         'show_ui'           => true,
    //         'show_admin_column' => true,
    //         'show_in_rest'      => true,
    //         'query_var'         => true,
    //         'rewrite'           => false,
    //     );

    //     register_taxonomy( $this->taxonomy_slug, [ $this->post_type_slug ], $args );

    // }

    /**
	 * Unregister custom taxonomy.
	 *
	 * @since    1.0.0
     * @access   public
	 */
    // public function unregister_wp_help_manager_taxonomy() {
        
    //     unregister_post_type( $this->taxonomy_slug );

    // }

}
