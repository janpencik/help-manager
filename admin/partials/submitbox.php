<?php

/**
 * Checkbox added to post's submitbox.
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

global $post;

wp_nonce_field( 'wphm-save-' . $post->ID, 'wphm-save-nonce', false, true );

?>
<div class="misc-pub-section">
    <input type="checkbox" name="wphm_make_default_document" id="wphm_make_default_document" <?php checked( $post->ID == get_option( 'wphm_default_document' ) ); ?>>
    <label for="wphm_make_default_document"><?php _e( 'Default Help Document', 'help-manager' ); ?></label>
</div>