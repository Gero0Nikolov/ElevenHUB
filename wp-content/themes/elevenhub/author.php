<?php
/**
 * The template for displaying user & company pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;

if ( is_user_logged_in() ) {
	$user_id = get_current_user_id();
	$association_type = get_user_meta( $user_id, "account_association", true );
	if ( $association_type  == "employee" ) {
		get_header( "employee" );
		if ( is_author( $user_id ) ) { require_once get_template_directory() ."/views/employee-view-personal.php"; }
		else {
			$v_user_id = get_queried_object_id();
			$v_user_association_type = get_user_meta( $v_user_id, "account_association", true );

			if ( $v_user_association_type == "employee" ) { require_once get_template_directory() ."/views/employee-view-visited.php"; }
			else if ( $v_user_association_type == "company" ) {
				if ( $brother_->is_employee( $v_user_id, $user_id ) ) { require_once get_template_directory() ."/views/company-view-visited-employee.php"; }
				else {
					if ( $brother_->is_company_public( $v_user_id ) ) { require_once get_template_directory() ."/views/company-view-visited-public.php"; }
					else { require_once get_template_directory() ."/views/company-view-visited-private.php"; }
				}
			}
			else { /* LOAD VIEW FOR NOT ASSOCIATED PROFILE */ }
		}
	}
	else if ( $association_type == "company" ) {
		get_header( "company" );
		if ( is_author( $user_id ) ) { require_once get_template_directory() ."/views/company-view-personal.php"; }
		else {
			$v_user = get_queried_object();
			$v_user_id = $v_user->ID;
			$v_user_association_type = get_user_meta( $v_user_id, "account_association", true );

			if ( $v_user_association_type == "employee" ) { require_once get_template_directory() ."/views/employee-view-visited.php"; }
			else if ( $v_user_association_type == "company" ) { /* LOAD VIEW FOR VISITED COMPANY */ }
			else { /* LOAD VIEW FOR NOT ASSOCIATED PROFILE */ }
		}
	}
	else { wp_redirect( wp_redirect( get_permalink( 11 ), 301 ) ); }

} else {
	/* Load public view */
	wp_redirect( get_site_url() );
}

?>
