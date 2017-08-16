<?php
/**

*	Template Name: Automatic Login template

*	@package eleven hub

*/

$page_id = get_the_ID();
$automatic_login = get_field( "allow_automatic_logins", $page_id );

if ( $automatic_login == "true" ) {
	$email = isset( $_GET[ "email" ] ) && !empty( $_GET[ "email" ] ) ? sanitize_text_field( urldecode( $_GET[ "email" ] ) ) : "";

	if ( !empty( $email ) ) {
		$brother_ = new BROTHER;
		$user_ = get_user_by( "email", $email );
		if ( $user_ !== false ) {
			$profile_association = $brother_->get_user_association( $user_->data->ID );
			if ( empty( $profile_association ) ) {
				$new_password = substr( md5( microtime() ), rand( 0, 26 ), 5 );

				// Set the new password
				wp_set_password( $new_password, $user_->data->ID );

				// Send the new password notification to the user
				$brother_->generate_email_notification( $user_->data->ID, "Howdy ". $user_->first_name ."!<br><br>Your password was changed by the automatic login screen!<br>Your new password is: <strong>". $new_password ."</strong><br><br>You can reset it from your settings!<br><br>Cheers!" );

				// Login user
				$creds['user_login'] = $email;
				$creds['user_password'] = $new_password;
				$creds['remember'] = false;
				$user_ = wp_signon( $creds );

				if ( !is_wp_error( $user_ ) ) {
					$today_date_int = strtotime( date( "Y-m-d" ) );
					update_user_meta( $user_id, "premium_start", $today_date_int );
					update_user_meta( $user_id, "premium_end", strtotime( "+7 day", $today_date_int ) );
				} else { wp_redirect( get_site_url() ); }
			} else { wp_redirect( get_site_url() ); }
		} else { wp_redirect( get_site_url() ); }
	} else { wp_redirect( get_site_url() ); }
}
?>
