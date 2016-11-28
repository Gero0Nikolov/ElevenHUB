<?php
/**

*	Template Name: My Profile Settings template

*	@package eleven hub

*/

if ( is_user_logged_in() ) {
	$user_id = get_current_user_id();
	$association_type = get_user_meta( $user_id, "account_association", true );
	if ( $association_type  == "employee" ) {
		// Load correct header
		get_header( "employee" );
		require_once get_template_directory() ."/views/employee-view-settings.php";
	}
	else if ( $association_type == "company" ) { /* LOAD COMPANY VIEW */ }
	else { wp_redirect( wp_redirect( get_permalink( 11 ), 301 ) ); }
} else {
	/* Load public view */
	wp_redirect( get_site_url() );
}

?>
