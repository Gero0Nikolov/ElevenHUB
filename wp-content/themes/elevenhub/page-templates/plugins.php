<?php
/**

*	Template Name: Plugins page template

*	@package eleven hub

*/
if ( is_user_logged_in() ) {
	$user_id = get_current_user_id();
	$association_type = get_user_meta( $user_id, "account_association", true );

	if ( $association_type == "employee" ) { get_header( "employee" ); }
	else if ( $association_type == "company" ) { get_header( "company" ); }

	// Load the tab controls
	require_once get_template_directory() ."/views/plugins-tab-menu.php";

	// Load the proper view for the proper tab
	$tab = isset( $_GET[ "tab" ] ) && !empty( $_GET[ "tab" ] ) ? trim( strtolower( $_GET[ "tab" ] ) ) : "";
	var_dump( $tab );
} else {
	/* Load public view */
	wp_redirect( get_site_url() );
}
?>
