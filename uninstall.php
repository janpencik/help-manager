<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://bohemiaplugins.com/
 * @since      1.0.0
 *
 * @package    Help_Manager
 */

namespace Help_Manager;
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
$options = get_option( 'help-manager-advanced' );
if( $options !== false ) {
	$delete_options = isset( $options['delete_options'] ) ? boolval( $options['delete_options'] ) : true;
	$delete_documents = isset( $options['delete_documents'] ) ? boolval( $options['delete_documents'] ) : false;
} else {
	$delete_options = true;
	$delete_documents = false;
}

// Delete plugin options
if( $delete_options === true ) {
	delete_option( 'help-manager-admin' );
	delete_option( 'help-manager-document' );
	delete_option( 'help-manager-permissions' );
	delete_option( 'help-manager-custom-css' );
	delete_option( 'help-manager-advanced' );
}

// Delete help documents
if( $delete_documents === true ) {
	$documents = get_posts( 'numberposts=-1&post_type=help-docs&post_status=any&fields=ids' );
	foreach( $documents as $document_id ) {
		wp_delete_post( $document_id, true );
	}
}