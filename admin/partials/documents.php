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

?>

<!-- Main wrapper -->
<div class="wrap wphm-wrap">

    <h1 class="wp-heading-inline wphm-page-title"><?php esc_html_e( $headline ); ?></h1>
    <?php if( $this->current_user_is_editor() ) { ?>
        <a href="<?php echo esc_attr( esc_url( admin_url( 'post-new.php?post_type=wp-help-docs' ) ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'wp-help-manager' ) ?></a>
    <?php } ?>

    <!-- Row -->
    <div class="wphm-docs-row">

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
        if( $search_string ) {
        ?>

            <!-- Search results -->
            <div class="wphm-content wphm-search-results">

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
        } else if( isset( $document_id ) ) {
            $document = new WP_Query( array( 
                'post_type'     => 'wp-help-docs', 
                'p'             => $document_id, 
                'post_status'   => array( 'publish', 'private' )
            ) );
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

                    <?php 
                    if( $document->have_posts() ) {
                        $document->the_post(); 
                        global $id;
                        ?>

                        <!-- Action buttons -->
                        <div class="wphm-content-actions">
                            <span class="wphm-action-button wphm-action-clipboard" data-clipboard-text="<?php echo esc_attr( esc_url( get_permalink( $document_id ) ) ); ?>">
                                <span class="dashicons dashicons-admin-links"></span>
                                <span><?php esc_html_e( 'Copy link', 'wp-help-manager' );?></span>
                            </span>
                            <span onclick="window.print();return false;" class="wphm-action-button">
                                <span class="dashicons dashicons-printer"></span>
                                <span><?php esc_html_e( 'Print', 'wp-help-manager' );?></span>
                            </span>
                            <?php if( $this->current_user_is_editor() ) { ?>
                            <a href="<?php echo esc_attr( get_edit_post_link( $document_id ) ); ?>" class="wphm-action-button wphm-action-button-right">
                                <span class="dashicons dashicons-edit"></span>
                                <span><?php esc_html_e( 'Edit', 'wp-help-manager' );?></span>
                            </a>
                            <span class="wphm-action-button wphm-action-trash" data-id="<?php echo esc_attr( $document_id ); ?>" data-nonce="<?php echo wp_create_nonce( 'trash-document' ); ?>">
                                <span class="dashicons dashicons-trash"></span>
                                <span><?php esc_html_e( 'Trash', 'wp-help-manager' );?></span>
                            </span>
                            <?php } ?>
                        </div>

                        <h1 class="wp-heading-inline"><?php the_title(); ?></h1>
                        <!-- <?php edit_post_link( esc_html__( 'Edit', 'wp-help-manager' ), '', '', null, 'page-title-action' ); ?> -->

                        <?php
                        if( $document_settings['child_navigation'] ) {
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
                        if( $document_settings['post_navigation'] ) {
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

                    <?php } else { ?>

                        <h1 class="wp-heading-inline"><?php esc_html_e( 'Document not found', 'wp-help-manager' ); ?></h1>
                        <p><?php esc_html_e( 'The requested help document could not be found.', 'wp-help-manager' ); ?></p>

                    <?php } ?>

                    <?php wp_reset_query(); ?>

                </div>
            </div>

        <?php } ?>

    </div>


</div>