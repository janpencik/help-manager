<?php 

/**
 * Toolbar navigation.
 *
 * @link       https://bohemiaplugins.com/
 * @since      1.0.0
 *
 * @package    Help_Manager
 * @subpackage Help_Manager/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Screen vars
$screen = get_current_screen();
$screen_id = isset( $screen->id ) ? $screen->id : false;
$screen_base = isset( $screen->base ) ? $screen->base : false;
$screen_action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : false;

// Highlight current page in toolbar
$current_screen = false;
if( $screen_id === 'toplevel_page_help-manager-documents' ) {
    $current_screen = 'view_documents';
} elseif( $screen_id === 'edit-help-docs' || $screen_id === 'help-docs' || ( $screen->base === 'post' && $screen->id === 'help-docs' && $screen_action === 'edit' ) ) {
    $current_screen = 'manage_documents';
} elseif( $screen_id === 'publishing-help_page_help-manager-settings' ) {
    $current_screen = 'settings';
} elseif( $screen_id === 'publishing-help_page_help-manager-tools' ) {
    $current_screen = 'tools';
}

// Get admin settings
$admin_settings = get_option( $this->plugin_name . '-admin' );

// Make headline WPML translatable
if( class_exists( 'SitePress' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
    $current_language = sanitize_key( ICL_LANGUAGE_CODE );
    $headline = ( isset( $admin_settings ) && isset( $admin_settings['headline_' . $current_language] ) && $admin_settings['headline_' . $current_language] !== '' ) ? esc_html( $admin_settings['headline_' . $current_language] ) : __( 'Publishing Help', 'help-manager' );
} else {
    $headline = ( isset( $admin_settings ) && isset( $admin_settings['headline'] ) && $admin_settings['headline'] !== '' ) ? esc_html( $admin_settings['headline'] ) : __( 'Publishing Help', 'help-manager' );
}

$menu_icon = ( isset( $admin_settings ) && isset( $admin_settings['menu_icon'] ) && $admin_settings['menu_icon'] ) 
    ? esc_html( $admin_settings['menu_icon'] ) 
    : 'dashicons-editor-help';
?>

<!-- Toolbar -->
<div class="wphm-toolbar">
    
    <!-- Brand -->
    <a class="wphm-brand" href="admin.php?page=help-manager-documents">
        <span class="dashicons <?php esc_html_e( $menu_icon ); ?>"></span>
        <span class="text"><?php esc_html_e( $headline ); ?></span>
    </a>

    <!-- Navigation -->
    <ul class="filter-links">
        <!-- <li class="wphm-mobile-only"> -->
        <li>
            <a href="<?php echo esc_attr( esc_url( admin_url( 'admin.php?page=help-manager-documents' ) ) ); ?>" <?php if( $current_screen === 'view_documents' ) { echo 'class="current" aria-current="page"'; } ?>>
                <?php esc_html_e( 'Documents', 'help-manager' ); ?>
            </a>
        </li>
        <?php if( $this->current_user_is_editor() ) { ?>
        <li>
            <a href="<?php echo esc_attr( esc_url( admin_url( 'edit.php?post_type=help-docs' ) ) ); ?>" <?php if( $current_screen === 'manage_documents' ) { echo 'class="current" aria-current="page"'; } ?>>
                <?php esc_html_e( 'Manage', 'help-manager' ); ?>
            </a>
        </li>
        <?php } ?>
        <?php if( $this->current_user_is_admin() ) { ?>
        <li>
            <a href="<?php echo esc_attr( esc_url( admin_url( 'admin.php?page=help-manager-settings' ) ) ); ?>" <?php if( $current_screen === 'settings' ) { echo 'class="current" aria-current="page"'; } ?>>
                <?php esc_html_e( 'Settings', 'help-manager' ); ?>
            </a>
        </li>
        <li>
            <a href="<?php echo esc_attr( esc_url( admin_url( 'admin.php?page=help-manager-tools' ) ) ); ?>" <?php if( $current_screen === 'tools' ) { echo 'class="current" aria-current="page"'; } ?>>
                <?php esc_html_e( 'Tools', 'help-manager' ); ?>
            </a>
        </li>
        <?php } ?>
    </ul>

    <!-- Search -->
    <div class="wphm-search inner">
        <form action="">
            <div class="search-box">
                <input type="hidden" name="page" value="help-manager-documents"> 
                <input type="search" id="post-search-input" name="s" value="<?php echo esc_attr( $search_string ); ?>" placeholder="<?php esc_attr_e( 'Search in documents', 'help-manager' );?>">
                <input type="submit" id="search-submit" class="button" value="<?php esc_attr_e( 'Search', 'help-manager' ); ?>">
            </div>
        </form>
    </div>

</div>