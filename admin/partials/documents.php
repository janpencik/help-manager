<?php

/**
 * Documents view.
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

// Get admin settings
$admin_settings = get_option( $this->plugin_name . '-admin' );

// Get document settings
$document_settings = get_option( $this->plugin_name . '-document' );

?>

<!-- Main wrapper -->
<div class="wrap wphm-wrap">

    <h1 class="wp-heading-inline wphm-page-title"><?php esc_html_e( 'Documents', 'help-manager' ); ?></h1>
    <?php if( $this->current_user_is_editor() ) { ?>
        <a href="<?php echo esc_attr( esc_url( admin_url( 'post-new.php?post_type=help-docs' ) ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'help-manager' ) ?></a>
    <?php } ?>
    <hr class="wp-header-end">

    <!-- Row -->
    <div class="wphm-docs-row">

        <!-- Mobile search -->
        <div class="wphm-search wphm-search-mobile inner">
            <form action="">
                <div class="search-box">
                    <input type="hidden" name="page" value="help-manager-documents"> 
                    <input type="search" id="post-search-input" name="s" value="<?php echo $search_string; ?>">
                    <input type="submit" id="search-submit" class="button" value="<?php esc_attr_e( 'Search Help Documents', 'help-manager' ); ?>">
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="wphm-sidebar">
            
            <!-- Navigation -->
            <div class="wphm-sidebar-topics-box inner">

                <!-- Topics -->
                <div class="wphm-sidebar-topics">
                    <h3><a href="<?php echo esc_attr( esc_url( admin_url( 'admin.php?page=help-manager-documents' ) ) ); ?>"><?php esc_html_e( 'Topics', 'help-manager' ); ?></a></h3>
                    <?php
                    $docs = wp_list_pages( array(
                        'post_type'         => 'help-docs',
                        'post_status'       => array( 'publish', 'private' ),
                        'hierarchical'      => true,
                        'echo'              => false,
                        'title_li'          => '',
                    ) );
                    $docs_handles = trim( $this->list_pages_add_handle( $docs ) );
                    ?>
                    <ul <?php if( $this->current_user_is_editor() ) { ?>class="can-sort"<?php } ?> data-nonce="<?php echo wp_create_nonce( 'wphm-docs-reorder' ); ?>">
                        <?php echo $docs_handles; ?>
                    </ul>
                </div>

            </div>

        </div>

        <!-- Search results -->
        <div class="wphm-content <?php if( $search_string ) { echo 'wphm-search-results'; } ?>" id="wphm-content-main">

            <!-- Box -->
            <div class="inner">

                <?php 
                // Search results
                if( $search_string ) {
                ?>

                    <h1 class="wp-heading-inline"><?php echo esc_html__( 'Search results for:', 'help-manager' ) . ' ' . $search_string; ?></h1>

                    <?php 
                    $search_results = new WP_Query( array(
                        'post_type'         => 'help-docs',
                        'post_status'       => array( 'publish', 'private' ),
                        'posts_per_page'    => -1,
                        's'                 => $search_string
                    ) );
                    if( $search_results->have_posts() ) {
                        while( $search_results->have_posts() ) {
                            $search_results->the_post();
                            ?>

                            <h3><a href="<?php echo esc_attr( esc_url( get_the_permalink() ) ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
                            <p><?php echo esc_html( get_the_excerpt() ); ?></p>

                        <?php } ?>
                    <?php } ?>

                    <?php wp_reset_query(); ?>

                <?php
                // Document
                } elseif( $document_id ) {

                    $document = new WP_Query( array( 
                        'post_type'     => 'help-docs', 
                        'p'             => $document_id,
                    ) );

                    if( $document->have_posts() ) {
                    $document->the_post(); 
                    global $id;
                    ?>

                        <style id="wphm-menu-highlight">
                            .wphm-sidebar .page-item-<?php echo $document_id; ?> > span a {
                                font-weight: 600;
                            }
                        </style>

                        <!-- Print button -->
                        <span title="<?php esc_attr_e( 'Print this document', 'help-manager' ); ?>" onclick="window.print();return false;" class="wphm-print-button">
                            <span class="dashicons dashicons-printer"></span>
                        </span>

                        <div class="wphm-document-title">
                            
                            <!-- Title -->
                            <h1 class="wp-heading-inline"><?php the_title(); ?></h1>

                            <!-- Edit link -->
                            <?php if( $this->current_user_is_editor() ) { ?>
                            <a href="<?php echo esc_attr( get_edit_post_link( $document_id ) ); ?>" class="page-title-action"><?php esc_html_e( 'Edit', 'help-manager' );?></a>
                            <?php } ?>

                        </div>

                        <?php
                        if( isset( $document_settings ) && isset( $document_settings['child_navigation'] ) && $document_settings['child_navigation'] || ! $document_settings ) {
                            $children = $this->get_document_children( $id );
                            if( $children ) {
                            ?>
                            <div class="wphm-children">
                                <div class="inner">
                                    <ul>
                                        <?php echo $children; ?>
                                    </ul>
                                </div>
                            </div>
                            <?php } ?>
                        <?php } ?>

                        <div class="wphm-docs-content">
                            <?php the_content(); ?>
                        </div>

                        <?php 

                        // Get post navigation links
                        if( ( isset( $document_settings ) && isset( $document_settings['post_navigation'] ) && $document_settings['post_navigation'] ) || ! $document_settings ) {
                            $document_navigation = $this->get_post_navigation_links( $id );
                            if( $document_navigation->prev_post || $document_navigation->next_post ) {
                            ?>

                            <!-- Post navigation -->
                            <nav class="navigation post-navigation" role="navigation">
                                <div class="nav-links">
                                    <?php if( $document_navigation->prev_post ) {
                                    $prev_arrow = is_rtl() ? '&xrarr;' : '&xlarr;';
                                    ?>
                                    <a class="nav-prev button" href="<?php echo esc_attr( esc_url( get_permalink( $document_navigation->prev_post->ID ) ) ); ?>" rel="prev">
                                        <span class="icon"><?php echo $prev_arrow; ?></span>
                                        <span class="text"><?php echo esc_html( $document_navigation->prev_post->post_title ); ?></span>
                                        <div class="text-mobile"><?php esc_attr_e( 'Previous', 'help-manager' ); ?></div>
                                    </a>
                                    <?php } ?>
                                    <?php if( $document_navigation->next_post ) {
                                    $next_arrow = is_rtl() ? '&xlarr;' : '&xrarr;';
                                    ?>
                                    <a class="nav-next button" href="<?php echo esc_attr( esc_url( get_permalink( $document_navigation->next_post->ID ) ) ); ?>" rel="next">
                                        <span class="text"><?php echo esc_html( $document_navigation->next_post->post_title ); ?></span>
                                        <div class="text-mobile"><?php esc_attr_e( 'Next', 'help-manager' ); ?></div>
                                        <span class="icon"><?php echo $next_arrow; ?></span>
                                    </a>
                                    <?php } ?>
                                </div>
                            </nav>

                            <?php } ?>
                        <?php } ?>

                    <?php } else { ?>

                        <h1 class="wp-heading-inline"><?php esc_html_e( 'Document not found', 'help-manager' ); ?></h1>
                        <p><?php esc_html_e( 'The requested document could not be found.', 'help-manager' ); ?></p>

                    <?php } ?>

                    <?php wp_reset_query(); ?>

                <?php } elseif( $document_id === false ) { ?>

                    <h1 class="wp-heading-inline"><?php esc_html_e( 'Document not found', 'help-manager' ); ?></h1>
                    <p><?php esc_html_e( 'The requested document could not be found.', 'help-manager' ); ?></p>

                <?php } else { ?>

                    <h1 class="wp-heading-inline"><?php esc_html_e( 'No documents', 'help-manager' ); ?></h1>
                    <p><?php esc_html_e( 'There are no published documents.', 'help-manager' ); ?></p>
                    <p><a href="<?php echo esc_attr( admin_url( 'post-new.php?post_type=help-docs' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Add help document', 'help-manager' ); ?></a></p>
                
                <?php } ?>

            </div>
            
            <?php if( isset( $document_settings ) && isset( $document_settings['scroll_to_top'] ) && $document_settings['scroll_to_top'] || ! $document_settings ) { ?>
            <!-- Document footer -->
            <div class="wphm-document-footer">
                
                <!-- Back to top -->
                <span class="wphm-back-to-top">
                    <span class="dashicons dashicons-arrow-up-alt2"></span>
                    <span><?php esc_html_e( 'Scroll to top', 'help-manager' ); ?></span>
                </span>

            </div>
            <?php } ?>

        </div>

        <?php if( isset( $document_settings ) && isset( $document_settings['quick_navigation'] ) && $document_settings['quick_navigation'] || ! $document_settings ) { ?>
        <!-- Quick navigation -->
        <div class="wphm-quick-navigation">
            <div class="wphm-quick-navigation-fixed">
                <h3><?php esc_html_e( 'In this article', 'help-manager' ); ?></h3>
                <!-- Links are appended by js -->
                <ul></ul>
            </div>
        </div>
        <?php } ?>

    </div>

</div>