<?php

/**
 * Documents view.
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

// Get admin settings
$admin_settings = get_option( $this->plugin_name . '-admin' );
$headline = ( isset( $admin_settings ) && isset( $admin_settings['headline'] ) && $admin_settings['headline'] ) ? esc_html( $admin_settings['headline'] ) : __( 'Publishing Help', 'wp-help-manager' );

// Get document settings
$document_settings = get_option( $this->plugin_name . '-document' );

// Page type
if( $search_string ) {
    $page_type = 'search';
} else if( $document_id ) {
    $page_type = 'document';
} else {
    $page_type = 'empty';
}

?>

<!-- Main wrapper -->
<div class="wrap wphm-wrap wphm-<?php echo $page_type; ?>">

    <h1 class="wp-heading-inline wphm-page-title"><?php esc_html_e( 'Documents', 'wp-help-manager' ); ?></h1>
    <?php if( $this->current_user_is_editor() ) { ?>
        <a href="<?php echo esc_attr( esc_url( admin_url( 'post-new.php?post_type=wp-help-docs' ) ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'wp-help-manager' ) ?></a>
    <?php } ?>

    <!-- Row -->
    <div class="wphm-docs-row">

        <!-- Mobile search -->
        <div class="wphm-search wphm-search-mobile inner">
            <form action="">
                <div class="search-box">
                    <input type="hidden" name="page" value="wp-help-manager-documents"> 
                    <input type="search" id="post-search-input" name="s" value="<?php echo $search_string; ?>">
                    <input type="submit" id="search-submit" class="button" value="<?php esc_attr_e( 'Search Help Documents', 'wp-help-manager' ); ?>">
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="wphm-sidebar">
            
            <!-- Navigation -->
            <div class="wphm-sidebar-topics-box inner">

                <!-- Topics -->
                <div class="wphm-sidebar-topics">
                    <h3><a href="<?php echo esc_attr( esc_url( admin_url( 'admin.php?page=wp-help-manager-documents' ) ) ); ?>"><?php esc_html_e( 'Topics', 'wp-help-manager' ); ?></a></h3>
                    <?php
                    $docs = wp_list_pages( array(
                        'post_type'         => 'wp-help-docs',
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

        <?php 
        if( $page_type === 'search' ) {
        ?>

            <!-- Search results -->
            <div class="wphm-content wphm-search-results" id="wphm-content-main">

                <!-- Box -->
                <div class="inner">

                    <h1 class="wp-heading-inline"><?php echo esc_html__( 'Search results for:', 'wp-help-manager' ) . ' ' . $search_string; ?></h1>

                    <?php 
                    $search_results = new WP_Query( array(
                        'post_type'         => 'wp-help-docs',
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

                </div>

            </div>

        <?php
        } else if( $page_type === 'document' ) {
            $document = new WP_Query( array( 
                'post_type'     => 'wp-help-docs', 
                'p'             => $document_id,
            ) );

            if( $document->have_posts() ) {
            $document->the_post(); 
            global $id;
            ?>

            <!-- Document content -->
            <div class="wphm-content" id="wphm-content-main">

                <?php if( $document_id && ! $search_string ) { ?>
                    <style id="wphm-menu-highlight">
                        .wphm-sidebar .page-item-<?php echo $document_id; ?> > span a {
                            font-weight: 600;
                        }
                    </style>
                <?php } ?>
                
                <!-- Box -->
                <div class="inner">

                    <!-- Print button -->
                    <span title="<?php esc_attr_e( 'Print this document', 'wp-help-manager' ); ?>" onclick="window.print();return false;" class="wphm-print-button">
                        <span class="dashicons dashicons-printer"></span>
                    </span>

                    <div class="wphm-document-title">
                        
                        <!-- Title -->
                        <h1 class="wp-heading-inline"><?php the_title(); ?></h1>

                        <!-- Edit link -->
                        <?php if( $this->current_user_is_editor() ) { ?>
                        <a href="<?php echo esc_attr( get_edit_post_link( $document_id ) ); ?>" class="page-title-action"><?php esc_html_e( 'Edit', 'wp-help-manager' );?></a>
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
                                <?php if( $document_navigation->prev_post ) { ?>
                                <a class="nav-prev button" href="<?php echo esc_attr( esc_url( get_permalink( $document_navigation->prev_post->ID ) ) ); ?>" rel="prev">
                                    <span>&xlarr;</span> <?php echo esc_html( $document_navigation->prev_post->post_title ); ?>
                                </a>
                                <?php } ?>
                                <?php if( $document_navigation->next_post ) { ?>
                                <a class="nav-next button" href="<?php echo esc_attr( esc_url( get_permalink( $document_navigation->next_post->ID ) ) ); ?>" rel="next">
                                    <?php echo esc_html( $document_navigation->next_post->post_title ); ?> <span>&xrarr;</span>
                                </a>
                                <?php } ?>
                            </div>
                        </nav>

                        <?php } ?>
                    <?php } ?>
                </div>

                <!-- Back to top link -->
                <div class="wphm-document-footer">
                    <span class="wphm-back-to-top">
                        <span class="dashicons dashicons-arrow-up-alt2"></span>
                        <span><?php esc_html_e( 'Scroll to top', 'wp-help-manager' ); ?></span>
                    </span>
                </div>

            </div>

            <?php } elseif( $page_type === 'empty' ) { ?>

            <!-- Document content -->
            <div class="wphm-content wphm-content-not-found" id="wphm-content-main">

                <!-- Box -->
                <div class="inner">

                    <h1 class="wp-heading-inline"><?php esc_html_e( 'Document not found', 'wp-help-manager' ); ?></h1>
                    <p><?php esc_html_e( 'The requested document could not be found.', 'wp-help-manager' ); ?></p>

                </div>
            
            </div>

            <?php } ?>

            <?php wp_reset_query(); ?>

        <?php } else { ?>

        <!-- Document content -->
        <div class="wphm-content wphm-content-not-found" id="wphm-content-main">

            <!-- Box -->
            <div class="inner">

                <h1 class="wp-heading-inline"><?php esc_html_e( 'No documents', 'wp-help-manager' ); ?></h1>
                <p><?php esc_html_e( 'There are no published documents.', 'wp-help-manager' ); ?></p>
                <p><a href="<?php echo esc_attr( admin_url( 'post-new.php?post_type=wp-help-docs' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Add help document', 'wp-help-manager' ); ?></a></p>

            </div>
        
        </div>

        <?php } ?>

    </div>

</div>