<?php
/**

*	Template Name: Company Media template

*	@package eleven hub

*/
if ( is_user_logged_in() ) {
	parse_str( $_SERVER[ "QUERY_STRING" ] ); // Convert the company_id from the URL to real $company_id

	$user_id = get_current_user_id();
	$association_type = get_user_meta( $user_id, "account_association", true );

	if ( $association_type == "employee" ) { get_header( "employee" ); }
	else if ( $association_type == "company" ) { get_header( "company" ); }

	// Load the media view
	require_once get_template_directory() ."/views/company-view-media.php";
} else {
	/* Load public view */
	wp_redirect( get_site_url() );
}
?>
