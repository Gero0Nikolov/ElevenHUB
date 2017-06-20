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
	$post_id = get_the_ID();

	if ( get_post_type( $post_id ) == "post" ) {
		$company_id = get_post_meta( $post_id, "related_company_id", true );

		if ( get_user_meta( $company_id, "company_type", true ) == "public" ) {
			get_header();
			require_once get_template_directory() ."/views/single-story-public-preview.php";
			get_footer();
		} else { wp_redirect( get_site_url() ); }
	} else {
		get_header();
		require_once get_template_directory() ."/views/single-story-public-preview.php";
		get_footer();
	}
}
