<?php
/**
*	Plugin Name: Brother Framework
* 	Description: This plugin add the Brother Framework to the HUB project quick and simple
* 	Author: Gero Nikolov
* 	Version: 1.0
*/

class BROTHER {
	function __construct() {
		//Register AJAX call for the generate_ajax_call method
		add_action( 'wp_ajax_generate_ajax_call', array( $this, 'generate_ajax_call' ) );
		add_action( 'wp_ajax_nopriv_generate_ajax_call', array( $this, 'generate_ajax_call' ) );

		//Register AJAX call for the upload_profile_media method
		add_action( 'admin_post_upload_profile_media', array( $this, 'upload_profile_media' ) );
		add_action( 'admin_post_nopriv_upload_profile_media', array( $this, 'upload_profile_media' ) );
	}

	/*
	*	Function name: upload_profile_media
	*	Function arguments: $only_img [ BOOLEAN ] (optional) (used to tell if the uploaded file should be only from TYPE: Image)
	*	Function purpose:
	*	This function is used to upload the media to the user profile via AJAX request.
	*/
	function upload_profile_media( $only_img = true ) {
		if ( $_FILES[ "avatar-picker" ][ "size" ] > 0 && getimagesize( $_FILES[ "avatar-picker" ][ "tmp_name" ] ) != 0 ) {
			$avatar_id = $this->upload_user_file( $_FILES[ "avatar-picker" ] );
			$avatar_ = $this->get_user_avatar();
			if ( empty( $avatar_->avatar_id ) ) {
				add_user_meta( get_current_user_id(), "user_avatar_id", $avatar_id, false );
				add_user_meta( get_current_user_id(), "user_avatar_url", wp_get_attachment_url( $avatar_id ), false );
			} else {
				wp_delete_attachment( $avatar_->avatar_id, true );
				update_user_meta( get_current_user_id(), "user_avatar_id", $avatar_id, false );
				update_user_meta( get_current_user_id(), "user_avatar_url", wp_get_attachment_url( $avatar_id ), false );
			}
		}

		if ( $_FILES[ "banner-picker" ][ "size" ] > 0 && getimagesize( $_FILES[ "banner-picker" ][ "tmp_name" ] ) != 0 ) {
			$banner_id = $this->upload_user_file( $_FILES[ "banner-picker" ] );
			$banner_ = $this->get_user_banner();
			if ( empty( $banner_->banner_id ) ) {
				add_user_meta( get_current_user_id(), "user_banner_id", $banner_id, false );
				add_user_meta( get_current_user_id(), "user_banner_url", wp_get_attachment_url( $banner_id ), false );
			} else {
				wp_delete_attachment( $banner_->banner_id, true );
				update_user_meta( get_current_user_id(), "user_banner_id", $banner_id, false );
				update_user_meta( get_current_user_id(), "user_banner_url", wp_get_attachment_url( $banner_id ), false );
			}
		}

		wp_redirect( get_author_posts_url( get_current_user_id() ) );
	}

	/*
	*	Function name: upload_user_file
	*	Function arguments: $file [ $_FILES ] (required)
	*	Function purpose: This function is used to upload media files in the HUB.
	*/
	function upload_user_file( $file ) {
		if ( $file[ "size" ] > 0 ) {
			require_once( ABSPATH . 'wp-admin/includes/admin.php' );

			$file_return = wp_handle_upload( $file, array('test_form' => false ) );

			if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
				return false;
			} else {
				$filename = $file_return['file'];
				$attachment = array(
				  'post_mime_type' => $file_return['type'],
				  'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				  'post_content' => '',
				  'post_status' => 'inherit',
				  'guid' => $file_return['url']
				);
				$attachment_id = wp_insert_attachment( $attachment, $file_return['url'] );

				require_once(ABSPATH . 'wp-admin/includes/image.php');

				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
				wp_update_attachment_metadata( $attachment_id, $attachment_data );

				if( 0 < intval( $attachment_id ) ) {
					return $attachment_id;
				}
			}
		}

		return false;
	}

	/*
	*	Function name: generate_ajax_call
	*	Function arguments: NONE
	*	Function purpose:
	*	This function is used to generate & execute external AJAX call made by the user on the Front-end.
	*	It uses the $_POST[ "data" ] to pass JSON object converted to PHP object as a data which is provided to the function.
	*/
	function generate_ajax_call() {
		$_DATA = !empty( $_POST[ "data" ] ) ? json_decode( stripslashes( $_POST[ "data" ] ) ) : NULL;

		if ( !empty( $_DATA ) ) {
			$result = call_user_func( array( $this, $_DATA->functionName ), $_DATA->arguments );
			echo json_encode( $result );
		} else { echo "Empty data"; }

		die();
	}

	/*
	*	Function name: is_table_exists
	*	Function arguments: $table_name [ STRING ]
	*	Function purpose:
	*	This function checks if the table with name $table_name exists in the current database.
	*	If the table exists the function will return TRUE if not the result will be FALSE.
	*/
	function is_table_exists( $table_name ) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table_name;
		return $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ? true : false;
	}

	/*
	*	Function name: get_user_avatar_url
	*	Function arguments: $user_id [ INT ] (optional)
	*	Function purpose: This function returns the URL to the avatar picture of the specified user
	*/
	function get_user_avatar_url( $user_id = "" ) {
		return !empty( $user_id ) ? get_user_meta( $user_id, "user_avatar_url", true ) : get_user_meta( get_current_user_id(), "user_avatar_url", true );
	}

	/*
	*	Function name: get_user_avatar
	*	Function arguments: $user_id [ INT ] (optional)
	*	Function purpose: This function returns an object with the ID, URL & PATH of the current user avatar
	*/
	function get_user_avatar( $user_id = "" ) {
		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }

		$user_avatar = (object) array(
			"avatar_id" => get_user_meta( $user_id, "user_avatar_id", true ),
			"avatar_url" => get_user_meta( $user_id, "user_avatar_url", true ),
			"avatar_path" => get_user_meta( $user_id, "user_avatar_path", true )
		);

		return $user_avatar;
	}

	/*
	*	Function name: get_user_banner_url
	*	Function arguments: $user_id [ INT ] (optional)
	*	Function purpose: This function returns the URL to the banner picture of the specified user
	*/
	function get_user_banner_url( $user_id = "" ) {
		return !empty( $user_id ) ? get_user_meta( $user_id, "user_banner_url", true ) : get_user_meta( get_current_user_id(), "user_banner_url", true );
	}

	/*
	*	Function name: get_user_banner
	*	Function arguments: $user_id [ INT ] (optional)
	*	Function purpose: This function returns an object with the ID, URL & PATH of the current user banner
	*/
	function get_user_banner( $user_id = "" ) {
		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }

		$user_banner = (object) array(
			"banner_id" => get_user_meta( $user_id, "user_banner_id", true ),
			"banner_url" => get_user_meta( $user_id, "user_banner_url", true ),
			"banner_path" => get_user_meta( $user_id, "user_banner_path", true )
		);

		return $user_banner;
	}

	/*
	*	Function name: create_user_relations
	*	Function arguments: NONE
	*	Function purpose:
	*	This function is used to create the PREFIX_user_relations table in the current database.
	*	It is called by the default Brother core after the framework initialization.
	*/
	function create_user_relations() {
		global $wpdb;

		$user_relations_table = $wpdb->prefix ."user_relations";

		if( $wpdb->get_var( "SHOW TABLES LIKE '$user_relations_table'" ) != $user_relations_table ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql_ = "
			CREATE TABLE $user_relations_table (
				id INT NOT NULL AUTO_INCREMENT,
				user_followed_id INT,
				user_follower_id INT,
				user_employer_id INT,
				PRIMARY KEY(id)
			) $charset_collate;
			";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql_ );
		}
	}

	/*
	*	Function name: create_user_notifications
	*	Function arguments: NONE
	*	Function purpose: This function create the PREFIX_user_notifications table on the server Database.
	*/
	function create_user_notifications() {
		global $wpdb;

		$user_notifications_table = $wpdb->prefix ."user_notifications";

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$user_notifications_table'" ) != $user_notifications_table ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql_ = "
			CREATE TABLE $user_notifications_table (
				id INT NOT NULL AUTO_INCREMENT,
				notification_id INT,
				notification_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				notification_viewed INT DEFAULT 0,
				user_notified_id INT,
				user_notifier_id INT,
				PRIMARY KEY(id)
			) $charset_collate;
			";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql_ );
		}
	}

	/*
	*	Function name: get_user_followers
	*	Function arguments: $user_id [ INT ] (optional)
	*	Function purpose:
	*	This function return the followers (as a list of user IDs) for the specific user provided by his ID.
	*	Or if the $user_id is empty, the function will get the current logged user id.
	*/
	function get_user_followers( $user_id = "" ) {
		$user_id = empty( $user_id ) ? get_current_user_id() : $user_id ;

		global $wpdb;

		$table_ = $wpdb->prefix ."user_relations";
		$sql_ = "SELECT user_follower_id FROM $table_ WHERE user_followed_id=$user_id";
		$user_followers = $wpdb->get_results( $sql_, OBJECT );

		return $user_followers;
	}

	/*
	*	Function name: get_user_employees
	*	Function argumnets: $user_id [ INT ] (optional)
	*	Function purpose:
	*	This function return the employees (as a list of user IDs) for the specific user provided by his ID.
	*/
	function get_user_employees( $user_id = "" ) {
		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }

		global $wpdb;

		$table_ = $wpdb->prefix ."user_relations";
		$sql_ = "SELECT user_followed_id FROM $table_ WHERE user_employer_id=$user_id";
		$user_followers = $wpdb->get_results( $sql_, OBJECT );

		return $user_followers;
	}

	/*
	*	Function name: is_follower
	*	Function arguments: $v_user_id [ INT ] (required) (comes from $VISITED_user_id), $user_id [ INT ] (optional) (the ID of the current logged user)
	*	Function purpose: This function tells if the currently logged user follows a specific user by the $v_user_id argument.
	*/
	function is_follower( $v_user_id, $user_id = "" ) {
		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }

		global $wpdb;

		$table_ = $wpdb->prefix ."user_relations";
		$sql_ = "SELECT * FROM $table_ WHERE user_followed_id=$v_user_id AND user_follower_id=$user_id";

		return !empty( $wpdb->get_results( $sql_, OBJECT ) ) ? true : false;
	}

	/*
	*	Function name: follow_or_unfollow_relation
	*	Function arguments: $data [ MIXED_OBJECT ] (required) (containes the $v_user_id, $user_id & $recalculate)
	*	Function purpose: This function is used to generate user relation from TYPE: FOLLOW or UNFOLLOW
	*/
	function follow_or_unfollow_relation( $data ) {
		$v_user_id = $data->v_user_id;
		$user_id = $data->user_id;
		$recalculate = $data->recalculate_followers;

		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }
		$flag = "";

		global $wpdb;

		$table_ = $wpdb->prefix ."user_relations";

		if ( $this->is_follower( $v_user_id ) ) {
			$wpdb->delete( $table_, array( "user_followed_id" => $v_user_id, "user_follower_id" => $user_id ) );
			$flag = "unfollowed";
		} else {
			$wpdb->insert( $table_, array( "user_followed_id" => $v_user_id, "user_follower_id" => $user_id ) );
			$flag = "followed";
			$this->generate_notification( 70, $v_user_id, $user_id );
		}

		$flag = $data->recalculate_followers ? (object) array( "action_result" => $flag, "followers" => $this->get_user_followers( $v_user_id ) ) : $flag;

		return $flag;
	}

	/*
	*	Function name: get_user_relations
	*	Function arguments: $user_id [ INT ] (optional), $is_company [ BOOLEAN ] (optional) (specifies if the needed relations are for company profile)
	*	Function purpose:
	*	This function is used to return the relations of the specified by $user_id User.
	*	The function returns JSON array to the front end which contains two Objects (followers && followed || employees) in it.
	*/
	function get_user_relations( $user_id = "", $is_company = false ) {
		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }

		global $wpdb;

		$table_ = $wpdb->prefix ."user_relations";
		$sql_ = !$is_company ? "SELECT * FROM $table_ WHERE user_followed_id=$user_id OR user_follower_id=$user_id" : "SELECT * FROM $table_ WHERE user_followed_id=$user_id OR user_employer_id=$user_id";
		$results_ = $wpdb->get_results( $sql_, OBJECT );

		$count_followers = 0;
		if ( !$is_company ) { $count_follows = 0; } else { $count_employees = 0; }

		$followers_ = array();
		if ( !$is_company ) { $follows_ = array(); } else { $employees_ = array(); }

		foreach ( $results_ as $result_ ) {
			if ( $result_->user_followed_id == $user_id ) { // Followers array
				$followers_[ $count_followers ][ "row_id" ] = $result_->id;
				$followers_[ $count_followers ][ "user_follower_body" ][ "user_id" ] = $result_->user_follower_id;
				$followers_[ $count_followers ][ "user_follower_body" ][ "user_url" ] = get_author_posts_url( $result_->user_follower_id );
				$followers_[ $count_followers ][ "user_follower_body" ][ "user_avatar_url" ] = $this->get_user_avatar_url( $result_->user_follower_id );
				$followers_[ $count_followers ][ "user_follower_body" ][ "user_first_name" ] = get_user_meta( $result_->user_follower_id, "first_name", true );
				$followers_[ $count_followers ][ "user_follower_body" ][ "user_last_name" ] = get_user_meta( $result_->user_follower_id, "last_name", true );

				$count_followers += 1;
			} else { // Follows or Employees array
				if ( !$is_company ) {
					$follows_[ $count_follows ][ "row_id" ] = $result_->id;
					$follows_[ $count_follows ][ "user_followed_body" ][ "user_id" ] = $result_->user_followed_id;
					$follows_[ $count_follows ][ "user_followed_body" ][ "user_url" ] = get_author_posts_url( $result_->user_followed_id );
					$follows_[ $count_follows ][ "user_followed_body" ][ "user_avatar_url" ] = $this->get_user_avatar_url( $result_->user_followed_id );
					$follows_[ $count_follows ][ "user_followed_body" ][ "user_first_name" ] = get_user_meta( $result_->user_followed_id, "first_name", true );
					$follows_[ $count_follows ][ "user_followed_body" ][ "user_last_name" ] = get_user_meta( $result_->user_followed_id, "last_name", true );

					$count_follows += 1;
				} else {
					$employees_[ $count_employees ][ "row_id" ] = $result_->id;
					$employees_[ $count_employees ][ "user_employee_body" ][ "user_id" ] = $result_->user_followed_id;
					$employees_[ $count_employees ][ "user_employee_body" ][ "user_url" ] = get_author_posts_url( $result_->user_followed_id );
					$employees_[ $count_employees ][ "user_employee_body" ][ "user_avatar_url" ] = $this->get_user_avatar_url( $result_->user_followed_id );
					$employees_[ $count_employees ][ "user_employee_body" ][ "user_first_name" ] = get_user_meta( $result_->user_followed_id, "first_name", true );
					$employees_[ $count_employees ][ "user_employee_body" ][ "user_last_name" ] = get_user_meta( $result_->user_followed_id, "last_name", true );

					$count_employees += 1;
				}
			}
		}

		$relations_ = array( "followers" => (object) $followers_ );
		if ( !$is_company ) { $relations_ = array_merge( $relations_, array( "follows" => (object) $follows_ ) ); }
		else { $relations_ = array_merge( $relations_, array( "employees" => (object) $employees_ ) ); }

		$relations_ = json_encode( $relations_ );

		return $relations_;
	}

	/*
	*	Function name: generate_notification
	*	Function arguments: $notification_id [ INT ] (required) (the ID of the Notification post in the Notification PT), $v_user_id [ INT ] (required) (the ID of the visited user), $user_id [ INT ] (optional) (the ID of the visitor)
	*	Function purpose: This function generates notification for specific user based on the Notification_ID.
	*/
	function generate_notification( $notification_id, $v_user_id, $user_id = "" ) {
		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }

		global $wpdb;

		$table_ = $wpdb->prefix ."user_notifications";
		$wpdb->insert( $table_, array( "notification_id" => $notification_id, "user_notified_id" => $v_user_id, "user_notifier_id" => $user_id ) );
	}

	/*
	*	Functin name: get_user_notifications
	*	Function arguments: $user_id [ INT ] (optional) (the ID of the desired user notifications).
	*	Function purpose:
	*	This function gets the last 100 user notifications ordered by the Notification_Date and returns JSON string to the front end.
	*	Best used with var UserNotifications.getUserNotifications() method from the brother.js framework.
	*/
	function get_user_notifications( $user_id = "" ) {
		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }

		global $wpdb;

		$table_ = $wpdb->prefix ."user_notifications";
		$sql_ = "SELECT * FROM $table_ WHERE user_notified_id=$user_id ORDER BY id ASC LIMIT 100";
		$results_ = $wpdb->get_results( $sql_, OBJECT );

		$count = 0;
		$notifications_ = array();
		foreach ( $results_ as $notification_ ) {
			$notifications_[ $count ][ "row_id" ] = $notification_->id;
			$notifications_[ $count ][ "notification_body" ][ "notifier_avatar_url" ] = $this->get_user_avatar_url( $notification_->user_notifier_id );
			$notifications_[ $count ][ "notification_body" ][ "notification_name" ] = get_field( "notification_name", $notification_->notification_id );
			$notifications_[ $count ][ "notification_body" ][ "notification_link" ] = $this->convert_notification_url( get_field( "notification_url", $notification_->notification_id ), $notification_->user_notifier_id );
			$notifications_[ $count ][ "notification_body" ][ "notification_text" ] = $this->convert_notification_text( get_field( "notification_text", $notification_->notification_id ), $notification_->user_notifier_id );
			$notifications_[ $count ][ "notification_body" ][ "notification_icon" ] = get_field( "notification_icon_code", $notification_->notification_id );
			$notifications_[ $count ][ "notification_body" ][ "notification_icon_background" ] = get_field( "notification_icon_background_code", $notification_->notification_id );
			$notifications_[ $count ][ "notification_date" ] = date( "d-m-Y", strtotime( $notification_->notification_date ) );
			$notifications_[ $count ][ "notification_viewed" ] = $notification_->notification_viewed;
			$count += 1;
		}

		$notifications_ = json_encode( (object) $notifications_ );

		return $notifications_;
	}

	/*
	*	Function name: get_user_unseen_notifications
	*	Function arguments: $data [ MIXED_OBJECT ] (required) (contains the $user_id & the already $listed_notifications IDs).
	*	Function purpose:
	*	This function is used to filter & return only the latest unseen notificaitons to the Front-end.
	*	Best used with var UserNotifications.getUserUnseenNotifications() method from the brother.js framework.
	*	Good usage example can be found in the scripts.js file at the buildAndPullUserNotifications() method which includes the listenForNewUserNotifications() method.
	*/
	function get_user_unseen_notifications( $data ) {
		$user_id = !empty( $data->user_id ) ? $data->user_id : get_current_user_id();
		$listed_notifications = "'". implode( "','", $data->listed_notifications ) ."'";

		global $wpdb;

		$table_ = $wpdb->prefix ."user_notifications";
		$sql_ = "SELECT * FROM $table_ WHERE user_notified_id=$user_id AND id NOT IN ($listed_notifications) ORDER BY id ASC";
		$results_ = $wpdb->get_results( $sql_, OBJECT );

		$count = 0;
		$notifications_ = array();
		foreach ( $results_ as $notification_ ) {
			$notifications_[ $count ][ "row_id" ] = $notification_->id;
			$notifications_[ $count ][ "notification_body" ][ "notifier_avatar_url" ] = $this->get_user_avatar_url( $notification_->user_notifier_id );
			$notifications_[ $count ][ "notification_body" ][ "notification_name" ] = get_field( "notification_name", $notification_->notification_id );
			$notifications_[ $count ][ "notification_body" ][ "notification_link" ] = $this->convert_notification_url( get_field( "notification_url", $notification_->notification_id ), $notification_->user_notifier_id );
			$notifications_[ $count ][ "notification_body" ][ "notification_text" ] = $this->convert_notification_text( get_field( "notification_text", $notification_->notification_id ), $notification_->user_notifier_id );
			$notifications_[ $count ][ "notification_body" ][ "notification_icon" ] = get_field( "notification_icon_code", $notification_->notification_id );
			$notifications_[ $count ][ "notification_body" ][ "notification_icon_background" ] = get_field( "notification_icon_background_code", $notification_->notification_id );
			$notifications_[ $count ][ "notification_date" ] = date( "d-m-Y", strtotime( $notification_->notification_date ) );
			$notifications_[ $count ][ "notification_viewed" ] = $notification_->notification_viewed;
			$count += 1;
		}

		$notifications_ = json_encode( (object) $notifications_ );

		return $notifications_;
	}

	/*
	*	Function name: convert_notification_url
	*	Function arguments: $url [ STRING ] (required), $notifier_id [ INT ] (required) (the ID of the notifier)
	*	Function purpose:
	*	This function is used to convert generic notification URL to normal HTTP working address.
	*/
	function convert_notification_url( $url, $notifier_id ) {
		$url = str_replace( "[notifier_archive_page]", get_author_posts_url( $notifier_id ), $url );

		return $url;
	}

	/*
	*	Function name: convert_notification_text
	*	Function arguments: $text [ STRING ] (required), $notifier_id [ INT ] (required) (the ID of the notifier)
	*	Function purpose:
	*	This function is used to convert generic notification text to human readeble text.
	*/
	function convert_notification_text( $text, $notifier_id ) {
		$text = str_replace( "[notifier_first_name]", get_user_meta( $notifier_id, "first_name", true ), $text );

		return $text;
	}

	/*
	*	Function name: read_notification
	*	Function arguments: $notification_row_id [ INT ] (required) (the ID of the row where the notification is stored)
	*	Function purpose: This function is used to mark notification as viewed in the DB.
	*/
	function read_notification( $notification_row_id ) {
		global $wpdb;
		$table_ = $wpdb->prefix ."user_notifications";
		$wpdb->update( $table_, array( "notification_viewed" => 1 ), array( "id" => $notification_row_id ) );
	}

	/*
	*	Function name: catch_url_arguments
	*	Function arguments: NONE
	*	Function purpose:
	*	This function is used to keep track on the all URLS in the HUB project.
	*	It allows you to check which arguments with what keys and values are sent to the SERVER via $_GET requests.
	*	For future updates just add your cases in the switch(){} bellow.
	*/
	function catch_url_arguments() {
		$arguments = explode( "&", $_SERVER[ "QUERY_STRING" ] );
		foreach ( $arguments as $argument ) {
			$arg_key = explode( "=", $argument )[0];
			$arg_val = explode( "=", $argument )[1];

			switch ( $arg_key ) {
				case "read_notification":
					$this->read_notification( $arg_val );
					break;

				default:
					break;
			}
		}
	}

	/*
	*	Function name: update_user_meta
	*	Function arguments: $data [ MIXED_OBJECT ] (object) (used to provide information about the $user_id, $data->first_name, $data->last_name, $data->new_password)
	*	Function purpose: This function is used to update the user meta information via passed by the front-end brother.js method var UserMeta.updateUserMeta MIXED_OBJECT.
	*/
	function update_user_meta( $data ) {
		$user_id = !empty( $data->user_id ) ? $data->user_id : get_current_user_id();
		$user_ = get_user_by( "ID", $user_id );

		if ( $user_ && wp_check_password( $data->current_password, $user_->data->user_pass, $user_id ) ) {
			if ( !empty( $data->first_name ) && isset( $data->first_name ) ) { update_user_meta( $user_id, "first_name", $data->first_name ); }
			if ( !empty( $data->last_name ) && isset( $data->last_name ) ) { update_user_meta( $user_id, "last_name", $data->last_name ); }
			if ( !empty( $data->new_password ) && isset( $data->new_password ) ) { wp_set_password( $data->new_password, $user_id ); }

			if ( !get_user_meta( $user_id, "user_biography", false ) ) {
				add_user_meta( $user_id, "user_biography", $data->biography, false );
			} else {
				update_user_meta( $user_id, "user_biography", $data->biography, false );
			}

			// Logout user
			wp_logout();

			// Login user
			$res_ = wp_signon( array(
				"user_login" => $user_->data->user_login,
				"user_password" => !empty( $data->new_password ) ? $data->new_password : $data->current_password
			), false );

			return "updated";
		} else { return "Wrong password!"; }
	}

	function update_post_featured_image( $data ) {
		return $data;
	}

	function draft_user_post( $data ) {
		return $data;
	}

	/*
	*	Function name: get_available_media_space
	*	Function arguments: $user_id [ INT ] (optional) (the ID of the user which media space should be returned)
	*	Function purpose: This function returns the available media space for specified user (company) by the $user_id argument.
	*/
	function get_available_media_space( $user_id = "" ) {
		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }

		$media_space = get_user_meta( $user_id, "media_space", true );
		if ( empty( $media_space ) || !isset( $media_space ) ) {
			add_user_meta( $user_id, "media_space", "1000000000", false ); // Set 1GB free disk space
		}

		return $media_space;
	}

	/*
	*	Function name: convert_bytes
	*	Function arguments: $bytes [ INT ] (required) (the bytes which should be converted to MBs)
	*	Function purpose: This function converts $bytes to MBs.
	*/
	function convert_bytes( $bytes ) {
	    return number_format( $bytes * 0.000001, $precision = ($bytes * 0.000001) < 0 ? $precision = 2 : 0 );
	}
}

//Initialize the DB into the framework
$db_brother = new BROTHER;
if ( !$db_brother->is_table_exists( "user_relations" ) ) { $db_brother->create_user_relations(); }
if ( !$db_brother->is_table_exists( "user_notifications" ) ) { $db_brother->create_user_notifications(); }
?>
