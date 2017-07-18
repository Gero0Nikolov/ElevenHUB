<?php
/**

*	Template Name: Automatic Registration template

*	@package eleven hub

*/

$page_id = get_the_ID();
$automatic_registrations = get_field( "allow_automatic_registrations", $page_id );

if ( $automatic_registrations == "true" ) {
	$email = isset( $_GET[ "email" ] ) && !empty( $_GET[ "email" ] ) ? sanitize_text_field( urldecode( $_GET[ "email" ] ) ) : "";
	$first_name = isset( $_GET[ "fname" ] ) && !empty( $_GET[ "fname" ] ) ? sanitize_text_field( urldecode( $_GET[ "fname" ] ) ) : "";
	$last_name = isset( $_GET[ "lname" ] ) && !empty( $_GET[ "lname" ] ) ? sanitize_text_field( urldecode( $_GET[ "lname" ] ) ) : "";

	if ( !empty( $email ) && is_email( $email ) && !email_exists( $email ) && !empty( $first_name ) && !empty( $last_name ) && is_alphabetical( array( $first_name, $last_name ) ) ) {
		$wp_username = strtolower( $first_name ."_". $last_name );
		$password = substr( md5( microtime() ), rand( 0, 26 ), 5 );

		$wp_registration_result = wp_create_user( $wp_username, $password, $email );

		if ( is_wp_error( $wp_registration_result ) ) {
			if ( !empty( $wp_registration_result->errors[ "existing_user_login" ] ) && !email_exists( $email ) ) {
				while ( !empty( $wp_registration_result->errors[ "existing_user_login" ] ) ) { $wp_registration_result = wp_create_user( $wp_username . mt_rand( 100000, 999999 ), $password, $email ); }
			} else { wp_redirect( get_site_url() ); }
		}

		// Update the new user
		$args = array(
			"ID" => $wp_registration_result,
			"first_name" => $first_name,
			"last_name" => $last_name,
			"role" => "subscriber"
		);
		$wp_update_result = wp_update_user( $args );

		$user_id = $wp_registration_result;

		// Add needed user meta
		add_user_meta( $user_id, "account_tutorial", "0", false );

		// Get registration controller settings
		$registration_controller = get_page_by_path( "registration-controller" );
		$free_premium = get_field( "free_premium", $registration_controller->ID );

		if ( !empty( $free_premium ) && $free_premium == "yes" ) {
			$today_date_int = strtotime( date( "Y-m-d" ) );
			update_user_meta( $user_id, "premium_start", $today_date_int );
			update_user_meta( $user_id, "premium_end", strtotime( "+7 day", $today_date_int ) );
		}

		// Prepare Hello mail
		$subject = "Welcome to 11hub!";
		$content = "Welcome onboard!<br/><br/>We hope to see you <a href='". get_site_url() ."' target='_blank' style='color: #3498db; text-decoration: underline;'>hubbing soon</a>!<br/><br/>Cheers!<br/><br/>Your login details are:<br/>Email: $email<br/>Password: $password<br/>";
		wp_mail( $email, $subject, $content, array( "From: Gero Nikolov <vtm.sunrise@gmail.com>", "Content-type: text/html" ) );

		// Autologin user
		$creds = array();
		$creds[ "user_login" ] = $email;
		$creds[ "user_password" ] = $password;
		$creds[ "remember" ] = false;
		wp_signon( $creds, false );

		wp_redirect( get_site_url() );
	} else { wp_redirect( get_site_url() ); }
}
?>
