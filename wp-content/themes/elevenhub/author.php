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
	if ( $association_type  == "employee" ) {
		get_header( "employee" );
		if ( is_author( $user_id ) ) { require_once get_template_directory() ."/views/employee-view-personal.php"; }
		else {
			$v_user = get_queried_object();
			$v_user_id = $v_user->ID;
			$v_user_association_type = get_user_meta( $v_user_id, "account_association", true );

			if ( $v_user_association_type == "employee" ) { require_once get_template_directory() ."/views/employee-view-visited.php"; }
			else if ( $v_user_association_type == "company" ) { /* LOAD VIE FOR VISITED COMPANY */ }
			else { /* LOAD VIEW FOR NOT ASSOCIATED PROFILE */ }
		}
	}
	else if ( $association_type == "company" ) {
		get_header( "company" );
		if ( is_author( $user_id ) ) {
			require_once get_template_directory() ."/views/company-view-personal.php";

			// Check if company passes the tutorial
			$company_tutorial = get_user_meta( $user_id, "account_tutorial", true );
			if ( $company_tutorial == 0 ) {}
		}
		else {
			$v_user = get_queried_object();
			$v_user_id = $v_user->ID;
			$v_user_association_type = get_user_meta( $v_user_id, "account_association", true );

			if ( $v_user_association_type == "employee" ) { require_once get_template_directory() ."/views/employee-view-visited.php"; }
			else if ( $v_user_association_type == "company" ) { /* LOAD VIE FOR VISITED COMPANY */ }
			else { /* LOAD VIEW FOR NOT ASSOCIATED PROFILE */ }
		}
	}
	else { wp_redirect( wp_redirect( get_permalink( 11 ), 301 ) ); }

} else {
	/* Load public view */
	wp_redirect( get_site_url() );
}

?>
