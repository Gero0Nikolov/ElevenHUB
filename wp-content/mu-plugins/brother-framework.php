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
	*	Function arguments: NONE
	*	Function purpose:
	*	This function is used to upload the media to the user profile via AJAX request.
	*/
	function upload_profile_media() {
		if ( $_FILES[ "avatar-picker" ][ "size" ] > 0 ) {
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

		if ( $_FILES[ "banner-picker" ][ "size" ] > 0 ) {
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

		if( $wpdb->get_var( "SHOW TABLES LIKE '$user_relations_table'" ) != $user_relations_table ) { // Create the AINOW_Users table only if it doesn't exists!
			$charset_collate = $wpdb->get_charset_collate();

			$sql_ = "
			CREATE TABLE $user_relations_table (
				id INT NOT NULL AUTO_INCREMENT,
				user_followed_id INT,
				user_follower_id INT,
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
	*	This function return the followers (as a list of user IDs) of specific user provided by his ID.
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
		}

		$flag = $data->recalculate_followers ? (object) array( "action_result" => $flag, "followers" => $this->get_user_followers( $v_user_id ) ) : $flag;

		return $flag;
	}
}

//Initialize the DB into the framework
$db_brother = new BROTHER;
if ( !$db_brother->is_table_exists( "user_relations" ) ) { $db_brother->create_user_relations(); }
?>
