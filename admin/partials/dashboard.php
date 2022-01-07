<?php 

/**
 * Dashboard widget.
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

?>

<style><?php echo file_get_contents( plugin_dir_path( __FILE__ ) . '../assets/css/dashboard.css' ); ?></style>
<div class="wphm-dashboard-docs-search">
    <form action="">
        <div class="search-box">
            <input type="hidden" name="page" value="help-manager-documents"> 
            <input type="search" id="post-search-input" name="s" value="" placeholder="<?php esc_attr_e( 'Search in help documents', 'help-manager' );?>">
            <input type="submit" id="search-submit" class="button" value="<?php esc_attr_e( 'Search', 'help-manager' ); ?>">
        </div>
    </form>
</div>
<div class="wphm-dashboard-docs-list">
    <ul>
        <?php echo $docs; ?>
    </ul>
</div>
<?php if( $this->current_user_is_editor() ) { ?>
<p class="community-events-footer wphm-dashboard-docs-actions">
    <a href="<?php echo esc_attr( esc_url( admin_url( 'post-new.php?post_type=help-docs' ) ) ); ?>">
        <?php esc_html_e( 'Add new', 'help-manager' ); ?>
    </a>
    |
    <a href="<?php echo esc_attr( esc_url( admin_url( 'edit.php?post_type=help-docs' ) ) ); ?>">
        <?php esc_html_e( 'Manage', 'help-manager' ); ?>
    </a>
    <?php if( $this->current_user_is_admin() ) { ?>
    |
    <a href="<?php echo esc_attr( esc_url( admin_url( 'admin.php?page=help-manager-settings' ) ) ); ?>">
        <?php esc_html_e( 'Settings', 'help-manager' ); ?>
    </a>
    <?php } ?>
</p>
<?php } ?>