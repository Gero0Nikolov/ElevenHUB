<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;

if ( is_user_logged_in() ) {
	$user_id = get_current_user_id();
	$association_type = get_user_meta( $user_id, "account_association", true );
	if ( $association_type == "employee" ) { get_header( "employee" ); }
	else if ( $association_type == "company" ) { get_header( "company" ); }
	require_once get_template_directory() ."/views/single-story-preview.php";
} else {
	get_header();
	require_once get_template_directory() ."/views/single-story-public-preview.php";
	get_footer();
}
