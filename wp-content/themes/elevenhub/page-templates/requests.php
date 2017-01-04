<?php
/**

*	Template Name: Requests template

*	@package eleven hub

*/
if ( is_user_logged_in() ) {
	$user_id = get_current_user_id();
	$association_type = get_user_meta( $user_id, "account_association", true );

	if ( $association_type == "employee" ) {
		get_header( "employee" );
		require_once get_template_directory() ."/views/employee-requests.php";
	} else if ( $association_type == "company" ) {
		get_header( "company" );
		require_once get_template_directory() ."/views/company-requests.php";
	}
} else {
	/* Load public view */
	wp_redirect( get_site_url() );
}
?>
