<?php
/**
 * The template for displaying user & company pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

if ( is_user_logged_in() ) {
	$user_id = get_current_user_id();
	$association_type = get_user_meta( $user_id, "account_association", true );
	if ( $association_type  == "employee" ) { require_once "views/employee-view.php"; }
	else if ( $association_type == "company" ) { /* LOAD COMPANY VIEW */ }
}
else { 
	/* Load public view */
	wp_redirect( get_site_url() );
}

?>