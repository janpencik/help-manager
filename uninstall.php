<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://bohemiaplugins.com/
 * @since      1.0.0
 *
 * @package    Wp_Help_Manager
 */

namespace Wp_Help_Manager;
use WP_User;

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove admin capabilities from admin users
$admin_users = get_users( array(
	'role__in' 	=> 'access_wphm_settings'
) );
foreach( $admin_users as $admin_user ) {
	$user = new WP_User( $admin_user );
	$user->remove_cap( 'edit_document' );
	$user->remove_cap( 'read_document' );
	$user->remove_cap( 'delete_document' );
	$user->remove_cap( 'edit_documents' );
	$user->remove_cap( 'edit_others_documents' );
	$user->remove_cap( 'delete_documents' );
	$user->remove_cap( 'publish_documents' );
	$user->remove_cap( 'read_private_documents' );
	$user->remove_cap( 'read_documents' );
	$user->remove_cap( 'delete_private_documents' );
	$user->remove_cap( 'delete_others_documents' );
	$user->remove_cap( 'delete_published_documents' );
	$user->remove_cap( 'edit_private_documents' );
	$user->remove_cap( 'edit_published_documents' );
	$user->remove_cap( 'create_documents' );
	$user->remove_cap( 'access_wphm_settings' );
}

// Remove editor and reader capabilities from user roles
global $wp_roles;
$roles = $wp_roles->roles;
foreach( $roles as $role_slug => $role ) {
	$role = get_role( $role_slug );
	$role->remove_cap( 'edit_document' );
	$role->remove_cap( 'read_document' );
	$role->remove_cap( 'delete_document' );
	$role->remove_cap( 'edit_documents' );
	$role->remove_cap( 'edit_others_documents' );
	$role->remove_cap( 'delete_documents' );
	$role->remove_cap( 'publish_documents' );
	$role->remove_cap( 'read_private_documents' );
	$role->remove_cap( 'read_documents' );
	$role->remove_cap( 'delete_private_documents' );
	$role->remove_cap( 'delete_others_documents' );
	$role->remove_cap( 'delete_published_documents' );
	$role->remove_cap( 'edit_private_documents' );
	$role->remove_cap( 'edit_published_documents' );
	$role->remove_cap( 'create_documents' );
	$role->remove_cap( 'access_wphm_settings' );
}

// Get advanced settings to see if we should delete or preserve data
$options = get_option( 'wp-help-manager-advanced' );
if( $options !== false ) {
	$delete_options = isset( $options['delete_options'] ) ? boolval( $options['delete_options'] ) : true;
	$delete_documents = isset( $options['delete_documents'] ) ? boolval( $options['delete_documents'] ) : false;
} else {
	$delete_options = true;
	$delete_documents = false;
}

// Delete plugin options
if( $delete_options === true ) {
	delete_option( 'wp-help-manager-admin' );
	delete_option( 'wp-help-manager-document' );
	delete_option( 'wp-help-manager-permissions' );
	delete_option( 'wp-help-manager-custom-css' );
	delete_option( 'wp-help-manager-advanced' );
}

// Delete help documents
if( $delete_options === true ) {
	$documents = get_posts( 'numberposts=-1&post_type=wp-help-docs&post_status=any&fields=ids' );
	foreach( $documents as $document ) {
		wp_delete_post( $document->ID, true );
	}
}