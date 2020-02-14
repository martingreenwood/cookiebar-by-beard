<?php
if( !defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
	exit;

global $wpdb;

if ( ! current_user_can( 'manage_options' ) ) {

	delete_site_option( 'cbbb_option_name' );

	$postmeta_table = $wpdb->postmeta;
	$posts_table = $wpdb->posts;

	$postmeta_table = str_replace($wpdb->base_prefix, $wpdb->prefix, $postmeta_table);
	$postmeta_table = str_replace($wpdb->base_prefix, $wpdb->prefix, $postmeta_table);

	$wpdb->query("DELETE FROM " . $postmeta_table . " WHERE meta_key = '_cbbb_script_meta_key'");
	$wpdb->query("DELETE FROM " . $postmeta_table . " WHERE meta_key = '_cbbb_script_location_meta_key'");
	$wpdb->query("DELETE FROM " . $posts_table . " WHERE post_type = 'cbbb_cookie'");

}
