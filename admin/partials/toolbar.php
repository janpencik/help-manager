<?php 

/**
 * Toolbar navigation.
 *
 * @link       https://bohemiaplugins.com/
 * @since      1.0.0
 *
 * @package    Wp_Help_Manager
 * @subpackage Wp_Help_Manager/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$screen_id = $screen->id;

// Get admin settings
$admin_settings = get_option( $this->plugin_name . '-admin' );
$headline = ( isset( $admin_settings ) && isset( $admin_settings['headline'] ) && $admin_settings['headline'] ) 
    ? esc_html( $admin_settings['headline'] )
    : __( 'Publishing Help', 'wp-help-manager' );
$menu_icon = ( isset( $admin_settings ) && isset( $admin_settings['menu_icon'] ) && $admin_settings['menu_icon'] ) 
    ? esc_html( $admin_settings['menu_icon'] ) 
    : 'dashicons-editor-help';
?>

<!-- Toolbar -->
<div class="wphm-toolbar">
    
    <!-- Brand -->
    <a class="wphm-brand" href="admin.php?page=wp-help-manager-documents">
        <span class="dashicons <?php esc_html_e( $menu_icon ); ?>"></span>
        <span class="text"><?php esc_html_e( $headline ); ?></span>
    </a>

    <!-- Navigation -->
    <ul class="filter-links">
        <!-- <li class="wphm-mobile-only"> -->
        <li>
            <a href="<?php echo esc_attr( esc_url( admin_url( 'admin.php?page=wp-help-manager-documents' ) ) ); ?>" <?php if( $screen_id === 'toplevel_page_wp-help-manager-documents' ) { echo 'class="current" aria-current="page"'; } ?>>
                <?php esc_html_e( 'Documents', 'wp-help-manager' ); ?>
            </a>
        </li>
        <?php if( $this->current_user_is_editor() ) { ?>
        <li>
            <a href="<?php echo esc_attr( esc_url( admin_url( 'edit.php?post_type=wp-help-docs' ) ) ); ?>" <?php if( $screen_id === 'edit-wp-help-docs' ) { echo 'class="current" aria-current="page"'; } ?>>
                <?php esc_html_e( 'Manage', 'wp-help-manager' ); ?>
            </a>
        </li>
        <!-- <li>
            <a href="<?php echo esc_attr( esc_url( admin_url( 'post-new.php?post_type=wp-help-docs' ) ) ); ?>" <?php if( $screen_id === 'wp-help-docs' ) { echo 'class="current" aria-current="page"'; } ?>>
                <?php esc_html_e( 'Add new', 'wp-help-manager' ); ?>
            </a>
        </li> -->
        <?php } ?>
        <?php if( $this->current_user_is_admin() ) { ?>
        <li>
            <a href="<?php echo esc_attr( esc_url( admin_url( 'admin.php?page=wp-help-manager-settings' ) ) ); ?>" <?php if( $screen_id === 'publishing-help_page_wp-help-manager-settings' ) { echo 'class="current" aria-current="page"'; } ?>>
                <?php esc_html_e( 'Settings', 'wp-help-manager' ); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo esc_attr( esc_url( admin_url( 'admin.php?page=wp-help-manager-tools' ) ) ); ?>" <?php if( $screen_id === 'publishing-help_page_wp-help-manager-tools' ) { echo 'class="current" aria-current="page"'; } ?>>
                <?php esc_html_e( 'Tools', 'wp-help-manager' ); ?>
            </a>
        </li>
        <?php } ?>
    </ul>

    <!-- Search -->
    <div class="wphm-search inner">
        <form action="">
            <div class="search-box">
                <input type="hidden" name="page" value="wp-help-manager-documents"> 
                <input type="search" id="post-search-input" name="s" value="<?php echo $search_string; ?>" placeholder="<?php esc_attr_e( 'Search in documents', 'wp-help-manager' );?>">
                <input type="submit" id="search-submit" class="button" value="<?php esc_attr_e( 'Search', 'wp-help-manager' ); ?>">
            </div>
        </form>
    </div>

</div>