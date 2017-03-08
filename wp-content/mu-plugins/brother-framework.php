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

		//Register call for the upload_profile_media method
		add_action( 'admin_post_upload_profile_media', array( $this, 'upload_profile_media' ) );
		add_action( 'admin_post_nopriv_upload_profile_media', array( $this, 'upload_profile_media' ) );

		//Register call for the upload_user_media_files method
		add_action( 'admin_post_upload_user_media_files', array( $this, 'upload_user_media_files' ) );
		add_action( 'admin_post_nopriv_upload_user_media_files', array( $this, 'upload_user_media_files' ) );
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
	*	Function name: upload_user_media_files
	*	Function arguments: NONE
	*	Function purpose:
	*	This function is used to upload all TYPES of files on server & link them in the HUB DB.
	*/
	function upload_user_media_files() {
		$uploader_id = get_current_user_id();
		$company_id = $_POST[ "company_id" ];
		$total = count( $_FILES[ "upload" ] );

		if (
			( isset( $company_id ) && !empty( $company_id ) ) &&
			( isset( $_FILES[ "upload" ] ) && !empty( $_FILES[ "upload" ] ) && $total > 0 )
	 	) {
			for ( $count = 0; $count < $total; $count++ ) {
				if ( !empty( $_FILES[ "upload" ][ "name" ][ $count ] ) && isset( $_FILES[ "upload" ][ "name" ][ $count ] ) ) {
					$_FILE =  array();
					$_FILE[ "name" ] = $_FILES[ "upload" ][ "name" ][ $count ];
					$_FILE[ "type" ] = $_FILES[ "upload" ][ "type" ][ $count ];
					$_FILE[ "tmp_name" ] = $_FILES[ "upload" ][ "tmp_name" ][ $count ];
					$_FILE[ "error" ] = $_FILES[ "upload" ][ "error" ][ $count ];
					$_FILE[ "size" ] = $_FILES[ "upload" ][ "size" ][ $count ];

					$upload_result = $this->upload_user_file( $_FILE, array( "owner_id" => $company_id, "uploader_id" => $uploader_id ) );
					$upload_result = !$upload_result ? "&upload_result=false" : ( $upload_result == "Not enough space" ? "&upload_result=nes" : "" );
				}
			}
		}

		wp_redirect( get_permalink( 98 ) ."?company_id=". $company_id . $upload_result );
	}

	/*
	*	Function name: upload_user_file
	*	Function arguments: $file [ $_FILES ] (required)
	*	Function purpose: This function is used to upload media files in the HUB.
	*/
	function upload_user_file( $file, $atts = array() ) {
		if ( $file[ "size" ] > 0 ) {
			$updated_available_space = 0;
			if ( !empty( $atts[ "owner_id" ] ) ) {
				// Check if user had enough space
				$user_available_space = $this->get_available_media_space( $atts[ "owner_id" ] );
				$updated_available_space = $user_available_space - $file[ "size" ];
			}

			// Upload file
			require_once( ABSPATH . 'wp-admin/includes/admin.php' );

			$file_return = wp_handle_upload( $file, array('test_form' => false ) );

			if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
				return false;
			} else if ( $updated_available_space < 0 ) {
				return "Not enough space";
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
					if ( !empty( $atts[ "owner_id" ] ) ) {
						if ( empty( get_post_meta( $attachment_id, "owner_id", false ) ) ) {
							add_post_meta( $attachment_id, "owner_id", $atts[ "owner_id" ] );
							add_post_meta( $attachment_id, "uploader_id", $atts[ "uploader_id" ] );

							update_user_meta( $atts[ "owner_id" ], "media_space", $updated_available_space );
						}
					}

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
	*	Function name: create_user_notificationsmeta
	*	Function arguments: NONE
	*	Function purpose: This function is used to generate the WP_PREFIX_user_notificationsmeta table.
	*/
	function create_user_notificationsmeta() {
		global $wpdb;

		$user_notificationsmeta_table = $wpdb->prefix ."user_notificationsmeta";

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$user_notificationsmeta_table'" ) != $user_notificationsmeta_table ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql_ = "
			CREATE TABLE $user_notificationsmeta_table (
				id INT NOT NULL AUTO_INCREMENT,
				notification_id INT,
				meta_key LONGTEXT,
				meta_value LONGTEXT,
				PRIMARY KEY(id)
			) $charset_collate;
			";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql_ );
		}
	}

	/*
	*	Function name: create_user_requests
	*	Function arguments: NONE
	*	Function purpose: This function is used to generate the WP_PREFIX_user_requests table.
	*/
	function create_user_requests() {
		global $wpdb;

		$user_requests_table = $wpdb->prefix ."user_requests";

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$user_requests_table'" ) != $user_requests_table ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql_ = "
			CREATE TABLE $user_requests_table (
				id INT NOT NULL AUTO_INCREMENT,
				requester_id INT,
				requester_cv LONGTEXT,
				requester_portfolio LONGTEXT,
				request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				request_response VARCHAR(255),
				company_id INT,
				request_type VARCHAR(255),
				PRIMARY KEY(id)
			) $charset_collate;
			";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql_ );
		}
	}

	/*
	*	Function name: create_user_likes
	*	Function arguments: NONE
	*	Function purpose: This function is used to create the WP_PREFIX_user_likes table.
	*/
	function create_user_likes() {
		global $wpdb;

		$user_likes_table = $wpdb->prefix ."user_likes";

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$user_likes_table'" ) != $user_likes_table ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql_ = "
			CREATE TABLE $user_likes_table (
				id INT NOT NULL AUTO_INCREMENT,
				story_id INT,
				user_id INT,
				action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY(id)
			) $charset_collate;
			";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql_ );
		}
	}

	/*
	*	Function name: create_story_views
	*	Function arguments: NONE
	*	Function purpose: This function is used to create the WP_PREFIX_story_views table.
	*/
	function create_story_views() {
		global $wpdb;

		$story_views_table = $wpdb->prefix ."story_views";

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$story_views_table'" ) != $story_views_table ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql_ = "
			CREATE TABLE $story_views_table (
				id INT NOT NULL AUTO_INCREMENT,
				story_id INT,
				user_id INT,
				views INT,
				action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
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
	*	This function return the followers (as a list of user OBJECTS) for the specific user provided by his ID.
	*	Or if the $user_id is empty, the function will get the current logged user id.
	*/
	function get_user_followers( $user_id = "" ) {
		$user_id = empty( $user_id ) ? get_current_user_id() : $user_id ;

		$followers_holder = array();

		global $wpdb;
		$table_ = $wpdb->prefix ."user_relations";
		$sql_ = "SELECT * FROM $table_ WHERE user_followed_id=$user_id AND user_follower_id IS NOT NULL";
		$followers = $wpdb->get_results( $sql_, OBJECT );

		foreach ( $followers as $follower ) {
			$follower_holder = array();
			$follower_holder[ "row_id" ] = $follower->id;
			$follower_holder[ "user_follower_body" ][ "user_id" ] = $follower->user_follower_id;
			$follower_holder[ "user_follower_body" ][ "user_url" ] = get_author_posts_url( $follower->user_follower_id );
			$follower_holder[ "user_follower_body" ][ "user_avatar_url" ] = $this->get_user_avatar_url( $follower->user_follower_id );
			$follower_holder[ "user_follower_body" ][ "user_first_name" ] = get_user_meta( $follower->user_follower_id, "first_name", true );
			$follower_holder[ "user_follower_body" ][ "user_last_name" ] = get_user_meta( $follower->user_follower_id, "last_name", true );
			$follower_holder[ "user_follower_body" ][ "user_shortname" ] = get_user_meta( $follower->user_follower_id, "user_shortname", true );
			array_push( $followers_holder, (object)$follower_holder );
		}

		return $followers_holder;
	}

	/*
	*	Function name: get_user_follows
	*	Function arguments: $user_id [ INT ] (optional)
	*	Function purpose:
	*	This function return the follows (as a list of user OBJECTS) for the specific user provided by his ID.
	*	Or if the $user_id is empty, the function will get the current logged user id.
	*/
	function get_user_follows( $user_id = "" ) {
		$user_id = empty( $user_id ) ? get_current_user_id() : $user_id ;

		$follows_holder = array();

		global $wpdb;
		$table_ = $wpdb->prefix ."user_relations";
		$sql_ = "SELECT * FROM $table_ WHERE user_follower_id=$user_id AND user_followed_id IS NOT NULL";
		$follows = $wpdb->get_results( $sql_, OBJECT );

		foreach ( $follows as $follow ) {
			$follow_holder = array();
			$follow_holder[ "row_id" ] = $follow->id;
			$follow_holder[ "user_follow_body" ][ "user_id" ] = $follow->user_follow_id;
			$follow_holder[ "user_follow_body" ][ "user_url" ] = get_author_posts_url( $follow->user_followed_id );
			$follow_holder[ "user_follow_body" ][ "user_avatar_url" ] = $this->get_user_avatar_url( $follow->user_followed_id );
			$follow_holder[ "user_follow_body" ][ "user_first_name" ] = get_user_meta( $follow->user_followed_id, "first_name", true );
			$follow_holder[ "user_follow_body" ][ "user_last_name" ] = get_user_meta( $follow->user_followed_id, "last_name", true );
			$follow_holder[ "user_follow_body" ][ "user_shortname" ] = get_user_meta( $follow->user_followed_id, "user_shortname", true );
			array_push( $follows_holder, (object)$follow_holder );
		}

		return $follows_holder;
	}

	/*
	*	Function name: get_user_employees
	*	Function argumnets: $user_id [ INT ] (optional)
	*	Function purpose:
	*	This function return the employees (as a list of user OBJECTS) for the specific user provided by his ID.
	*/
	function get_user_employees( $user_id = "" ) {
		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }

		$employees_holder = array();

		global $wpdb;
		$table_ = $wpdb->prefix ."user_relations";
		$sql_ = "SELECT * FROM $table_ WHERE user_employer_id=$user_id";
		$employees = $wpdb->get_results( $sql_, OBJECT );

		foreach ( $employees as $employee ) {
			$employee_holder = array();
			$employee_holder[ "row_id" ] = $employee->id;
			$employee_holder[ "user_employee_body" ][ "user_id" ] = $employee->user_followed_id;
			$employee_holder[ "user_employee_body" ][ "user_url"] = get_author_posts_url( $employee->user_followed_id );
			$employee_holder[ "user_employee_body" ][ "user_avatar_url" ] = $this->get_user_avatar_url( $employee->user_followed_id );
			$employee_holder[ "user_employee_body" ][ "user_first_name" ] = get_user_meta( $employee->user_followed_id, "first_name", true );
			$employee_holder[ "user_employee_body" ][ "user_last_name" ] = get_user_meta( $employee->user_followed_id, "last_name", true );
			$employee_holder[ "user_employee_body" ][ "user_shortname" ] = get_user_meta( $employee->user_followed_id, "user_shortname", true );
			array_push( $employees_holder, (object)$employee_holder );
		}

		return $employees_holder;
	}

	/*
	*	Function name: get_search_results
	*	Function arguments: $data [ MIXED_OBJECT ] (required) (contains the searched user:
																			First Name,
																			Last Name,
																			Universal Name: That is First OR Last name used for the specific search,
																			Group relations:
																				Employees,
																				Followers,
																				Follows
																			)
	*	Function purpose:
	*	This function is used to search for specific users or user group (Group relations).
	*	It receives a MIXED_OBJECT from the front-end part which contains: $first_name [ STRING ], $last_name [ STRING ], $universal_name [ STRING ] && $relations [ ARRAY_STRING ].
	*	It can be used for search in the _GLOBAL_ users of the HUB or just for a specific _GROUP_CASE_.
	*/
	function get_search_results( $data ) {
		$data->first_name = sanitize_text_field( $data->first_name );
		$data->last_name = sanitize_text_field( $data->last_name );
		$data->universal_name = sanitize_text_field( $data->universal_name );

		global $wpdb;
		$table_user_relations = $wpdb->prefix ."user_relations";
		$table_user_meta = $wpdb->prefix ."usermeta";

		if ( ( !empty( $data->first_name ) && !empty( $data->last_name ) ) || !empty( $data->universal_name ) ) {
			$sql_ = "";

			$sql_extension = "OR ";
			if ( !empty( $data->relations ) ) { $sql_extension = "AND "; }
			if ( empty( $data->relations ) ) { $data->relations = array( "employees", "followers", "follows" ); }

			foreach ( $data->relations as $relation ) {
				if ( !empty( $data->universal_name ) ) { if ( $sql_extension != "OR " && $sql_extension != "AND " ) { $sql_extension .= " OR "; } }
				switch ( $relation ) {
					case "employees":
						$sql_extension .= "usermeta.user_id IN ( SELECT user_followed_id FROM $table_user_relations WHERE user_employer_id = $data->user_id )";
						break;

					case "followers":
						$sql_extension .= "usermeta.user_id IN ( SELECT user_follower_id FROM $table_user_relations WHERE user_followed_id = $data->user_id )";
						break;

					case "follows":
						$sql_extension .= "usermeta.user_id IN ( SELECT user_followed_id FROM $table_user_relations WHERE user_follower_id = $data->user_id )";
						break;

					default:
						break;
				}
			}

			if ( empty( $data->universal_name ) ) {
				$sql_ = "
				SELECT user_id FROM $table_user_meta as usermeta
				WHERE
				usermeta.meta_value LIKE '$data->first_name%'
				$sql_extension
				UNION
				SELECT user_id FROM $table_user_meta as usermeta
				WHERE
				usermeta.meta_value LIKE '$data->last_name%'
				$sql_extension
				";
			} else {
				$sql_ = "
				SELECT DISTINCT user_id FROM $table_user_meta as usermeta
				WHERE
				( usermeta.meta_value LIKE '$data->universal_name%' OR usermeta.meta_value LIKE '$data->universal_name%' )
				$sql_extension";
			}

			$results_ = $wpdb->get_results( $sql_, OBJECT );
			$users_container = array();

			foreach ( $results_ as $result_ ) {
				$user_container = array();
				$user_container[ "user_id" ] = $result_->user_id;
				$user_container[ "user_body" ][ "first_name" ] = get_user_meta( $result_->user_id, "first_name", true );
				$user_container[ "user_body" ][ "last_name" ] = get_user_meta( $result_->user_id, "last_name", true );
				$user_container[ "user_body" ][ "short_name" ] = get_user_meta( $result_->user_id, "user_shortname", true );
				$user_container[ "user_body" ][ "avatar_url" ] = $this->get_user_avatar_url( $result_->user_id );
				$user_container[ "user_body" ][ "banner_url" ] = $this->get_user_banner_url( $result_->user_id );
				$user_container[ "user_body" ][ "profile_url" ] = get_author_posts_url( $result_->user_id );
				array_push( $users_container, (object)$user_container );
			}

			return $users_container;
		}
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
	*	Function name: is_employee
	*	Function arguments: $v_user_id [ INT ] (required), $user_id [ INT ] (optional)
	*	Function purpose: This function tells if the currently logged user is employee of the specified company via the $v_user_id argument.
	*/
	function is_employee( $v_user_id, $user_id = "" ) {
		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }

		if ( $v_user_id == $user_id ) { return true; }
		else {
			global $wpdb;

			$table_ = $wpdb->prefix ."user_relations";
			$sql_ = "SELECT * FROM $table_ WHERE user_followed_id=$user_id AND user_employer_id=$v_user_id";

			return !empty( $wpdb->get_results( $sql_, OBJECT ) ) ? true : false;
		}
	}

	/*
	*	Function name: is_company_public
	*	Function arguments: $user_id [ INT ] (required) (the ID of the needed company)
	*	Function purpose: This function tells if the company is public or not.
	*/
	function is_company_public( $user_id ) {
		return get_user_meta( $user_id, "company_type", true ) == "public" ? true : false;
	}

	/*
	*	Function name: get_user_association
	*	Function arguments: $user_id [ INT ] (optional)
	*	Function purpose: This function gives you the association (Company || Employee) of the specified by $user_id, User.
	*/
	function get_user_association( $user_id = "" ) {
		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }
		$association_type = get_user_meta( $user_id, "account_association", true );
		return $association_type;
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
	*	Function name: hire_or_fire_relation
	*	Function arguments: $data [ MIXED_OBJECT ] (required) (holds the $user_id which will be hired/fired && the $company_id: EMPLOYER;)
	*	Function purpose: This function is used to generate Hire or Fire relation between User && Company.
	*/
	function hire_or_fire_relation( $data ) {
		$user_id = $data->user_id;
		$company_id = $data->company_id;

		$result = false;

		if ( !empty( $user_id ) && isset( $user_id ) && !empty( $company_id ) && isset( $company_id ) ) {
			global $wpdb;
			$table_ = $wpdb->prefix ."user_relations";

			if ( $this->is_employee( $company_id, $user_id ) ) {
				$wpdb->delete( $table_, array( "user_followed_id" => $user_id, "user_employer_id" => $company_id ) );
				$result = "fired";
			} else {
				$wpdb->insert( $table_, array( "user_followed_id" => $user_id, "user_employer_id" => $company_id ) );
				$result = "hired";
			}
		}

		return $result;
	}

	/*
	*	Function name: get_user_relations
	*	Function arguments: $user_id [ INT ] (optional), $is_company [ BOOLEAN ] (optional) (specifies if the needed relations are for company profile)
	*	Function purpose:
	*	This function is used to return the relations of the specified by $user_id User.
	*	The function returns JSON array to the front end which contains two Objects (followers && followed || employees) in it.
	*/
	function get_user_relations( $user_id = "", $is_company = false ) {
		if ( !is_object( $user_id ) ) { if ( empty( $user_id ) ) { $user_id = get_current_user_id(); } }
		else {
			$is_company = empty( $user_id->is_company ) ? false : $user_id->is_company;
			$user_id = $user_id->user_id;
		}

		global $wpdb;

		$table_ = $wpdb->prefix ."user_relations";
		$sql_ = !$is_company ? "SELECT * FROM $table_ WHERE ( user_followed_id=$user_id OR user_follower_id=$user_id ) AND user_follower_id IS NOT NULL" : "SELECT * FROM $table_ WHERE user_followed_id=$user_id OR user_employer_id=$user_id";
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
				$followers_[ $count_followers ][ "user_follower_body" ][ "user_shortname" ] = get_user_meta( $result_->user_follower_id, "user_shortname", true );

				$count_followers += 1;
			} else { // Follows or Employees array
				if ( !$is_company ) {
					$follows_[ $count_follows ][ "row_id" ] = $result_->id;
					$follows_[ $count_follows ][ "user_followed_body" ][ "user_id" ] = $result_->user_followed_id;
					$follows_[ $count_follows ][ "user_followed_body" ][ "user_url" ] = get_author_posts_url( $result_->user_followed_id );
					$follows_[ $count_follows ][ "user_followed_body" ][ "user_avatar_url" ] = $this->get_user_avatar_url( $result_->user_followed_id );
					$follows_[ $count_follows ][ "user_followed_body" ][ "user_first_name" ] = get_user_meta( $result_->user_followed_id, "first_name", true );
					$follows_[ $count_follows ][ "user_followed_body" ][ "user_last_name" ] = get_user_meta( $result_->user_followed_id, "last_name", true );
					$follows_[ $count_follows ][ "user_followed_body" ][ "user_shortname" ] = get_user_meta( $result_->user_followed_id, "user_shortname", true );

					$count_follows += 1;
				} else {
					$employees_[ $count_employees ][ "row_id" ] = $result_->id;
					$employees_[ $count_employees ][ "user_employee_body" ][ "user_id" ] = $result_->user_followed_id;
					$employees_[ $count_employees ][ "user_employee_body" ][ "user_url" ] = get_author_posts_url( $result_->user_followed_id );
					$employees_[ $count_employees ][ "user_employee_body" ][ "user_avatar_url" ] = $this->get_user_avatar_url( $result_->user_followed_id );
					$employees_[ $count_employees ][ "user_employee_body" ][ "user_first_name" ] = get_user_meta( $result_->user_followed_id, "first_name", true );
					$employees_[ $count_employees ][ "user_employee_body" ][ "user_last_name" ] = get_user_meta( $result_->user_followed_id, "last_name", true );
					$employees_[ $count_employees ][ "user_employee_body" ][ "user_shortname" ] = get_user_meta( $result_->user_shortname, "user_shortname", true );

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

		return $wpdb->insert_id;
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
			$notifications_[ $count ][ "notification_body" ][ "notification_link" ] = $this->convert_notification_url( get_field( "notification_url", $notification_->notification_id ), $notification_->user_notifier_id, $notification_->user_notified_id, $notification_->id, $notification_->notification_id );
			$notifications_[ $count ][ "notification_body" ][ "notification_text" ] = $this->convert_notification_text( get_field( "notification_text", $notification_->notification_id ), $notification_->user_notifier_id, $notification_->user_notified_id, $notification_->id, $notification_->notification_id );
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
			$notifications_[ $count ][ "notification_body" ][ "notification_link" ] = $this->convert_notification_url( get_field( "notification_url", $notification_->notification_id ), $notification_->user_notifier_id, $notification_->user_notified_id, $notification_->id, $notification_->notification_id );
			$notifications_[ $count ][ "notification_body" ][ "notification_text" ] = $this->convert_notification_text( get_field( "notification_text", $notification_->notification_id ), $notification_->user_notifier_id, $notification_->user_notified_id, $notification_->id, $notification_->notification_id );
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
	function convert_notification_url( $url, $notifier_id, $notified_id, $notification_id, $notification_template_id ) {
		$notification_converter_path = get_field( "notification_converter", $notification_template_id );
		$notification_converter_path = isset( $notification_converter_path ) && !empty( $notification_converter_path ) && $notification_converter_path !== false ? $notification_converter_path : get_template_directory() ."/notification-converters/". $notification_template_id .".php";

		if ( file_exists( $notification_converter_path ) ) {
			include $notification_converter_path;
			return $_CONVERTER_URL_( $url, $notifier_id, $notified_id, $notification_id, $this->get_notification_metas( $notification_id ) );
		} else { return $url; }
	}

	/*
	*	Function name: convert_notification_text
	*	Function arguments: $text [ STRING ] (required), $notifier_id [ INT ] (required) (the ID of the notifier)
	*	Function purpose:
	*	This function is used to convert generic notification text to human readeble text.
	*/
	function convert_notification_text( $text, $notifier_id, $notified_id, $notificaiton_id, $notification_template_id ) {
		$notification_converter_path = get_field( "notification_converter", $notification_template_id );
		$notification_converter_path = !empty( $notification_converter_path ) && isset( $notification_converter_path ) && $notification_converter_path ? $notification_converter_path : get_template_directory() ."/notification-converters/". $notification_template_id .".php";

		if ( file_exists( $notification_converter_path ) ) {
			include $notification_converter_path;
			return $_CONVERTER_TXT_( $text, $notifier_id, $notified_id, $notification_id, $this->get_notification_metas( $notification_id ) );
		} else { return $url; }
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
		if ( !empty( $arguments ) ) {
			foreach ( $arguments as $argument ) {
				if ( !empty( $argument ) && isset( $argument ) ) {
					$arg_key = isset( explode( "=", $argument )[0] ) ? explode( "=", $argument )[0] : NULL;
					$arg_val = isset( explode( "=", $argument )[1] ) ? explode( "=", $argument )[1] : NULL;

					switch ( $arg_key ) {
						case "read_notification":
							$this->read_notification( $arg_val );
							break;

						default:
							break;
					}
				}
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
			if ( !empty( $data->first_name ) && isset( $data->first_name ) && $this->is_alphabetical( $data->first_name ) ) { update_user_meta( $user_id, "first_name", ucfirst( strtolower( $data->first_name ) ) ); }
			if ( !empty( $data->last_name ) && isset( $data->last_name ) && $this->is_alphabetical( $data->last_name ) ) { update_user_meta( $user_id, "last_name", ucfirst( strtolower( $data->last_name ) ) ); }
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

	/*
	*	Function name: update_company_meta
	*	Function arguments: $data [ MIXED_OBJECT ] (required) (used to provide the META information about the Company profile)
	*	Function purpose: This function is used to update the company meta information.
	*/
	function update_company_meta( $data ) {
		$user_id = !empty( $data->user_id ) ? $data->user_id : get_current_user_id();
		$user_ = get_user_by( "ID", $user_id );

		if ( $user_ && wp_check_password( $data->current_password, $user_->data->user_pass, $user_id ) ) {
			if ( !empty( $data->first_name ) && isset( $data->first_name ) && $this->is_alphabetical( $data->first_name ) ) { update_user_meta( $user_id, "first_name", ucfirst( strtolower( $data->first_name ) ) ); }
			if ( !empty( $data->last_name ) && isset( $data->last_nae ) && $this->is_alphabetical( $data->last_name ) ) { update_user_meta( $user_id, "last_name", ucfirst( strtolower( $data->last_name ) ) ); }
			if ( !empty( $data->new_password ) && isset( $data->new_password ) ) { wp_set_password( $data->new_password, $user_id ); }
			if ( empty( get_user_meta( $user_id, "user_shortname", false ) ) ) { add_user_meta( $user_id, "user_shortname", $data->short_name ); }
			else { update_user_meta( $user_id, "user_shortname", $data->short_name ); }

			// Company meta data
			if ( empty( get_user_meta( $user_id, "company_type", false ) ) ) { add_user_meta( $user_id, "company_type", $data->company_type ); }
			else { update_user_meta( $user_id, "company_type", $data->company_type ); }

			if ( empty( get_user_meta( $user_id, "company_writing_permissions", false ) ) ) { add_user_meta( $user_id, "company_writing_permissions", $data->company_writing_permissions ); }
			else { update_user_meta( $user_id, "company_writing_permissions", $data->company_writing_permissions ); }

			if ( empty( get_user_meta( $user_id, "company_publications_communication_permissions", false ) ) ) { add_user_meta( $user_id, "company_publications_communication_permissions", $data->company_publications_communication_permissions ); }
			else { update_user_meta( $user_id, "company_publications_communication_permissions", $data->company_publications_communication_permissions ); }

			if ( empty( get_user_meta( $user_id, "company_media_uploads_permissions", false ) ) ) { add_user_meta( $user_id, "company_media_uploads_permissions", $data->company_media_uploads_permissions ); }
			else { update_user_meta( $user_id, "company_media_uploads_permissions", $data->company_media_uploads_permissions ); }

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

	/*
	*	Function name: get_company_meta
	*	Function arguments: $company_id [ INT ] (optional)
	*	Function purpose: This function is used to return the whole $_COMPANY_META from the HUB DB in one single MIXED_OBJECT.
	*/
	function get_company_meta( $company_id = "" ) {
		if ( empty( $company_id ) ) { $company_id = get_current_user_id(); }

		$meta_ = array();
		$meta_[ "first_name" ] = get_user_meta( $company_id, "first_name", true );
		$meta_[ "last_name" ] = get_user_meta( $company_id, "last_name", true );
		$meta_[ "short_name" ] = get_user_meta( $company_id, "user_shortname", true );
		$meta_[ "company_type" ] = get_user_meta( $company_id, "company_type", true );
		$meta_[ "writing_permissions" ] = get_user_meta( $company_id, "company_writing_permissions", true );
		$meta_[ "publications_communication_permissions" ] = get_user_meta( $company_id, "company_publications_communication_permissions", true );
		$meta_[ "media_uploads_permissions" ] = get_user_meta( $company_id, "company_media_uploads_permissions", true );
		$meta_ = (object)$meta_;

		return $meta_;
	}

	/*
	*	Function name: generate_notification_meta
	*	Function arguments: $notification_id [ INT ] (required), $meta_key [ STRING ] (required), $meta_value [ STRING ]
	*	Function purpose: This function is used to save custom meta information for the specified Notification.
	*/
	function generate_notification_meta( $notification_id, $meta_key, $meta_value ) {
		global $wpdb;
		$table_ = $wpdb->prefix ."user_notificationsmeta";

		$sql_ = "SELECT * FROM $table_ WHERE notification_id='$notification_id' AND meta_key='$meta_key' AND meta_value='$meta_value'";
		$result_ = $wpdb->get_results( $sql_, OBJECT );

		if ( count( $result_ ) == 0 ) { // Insert method
			$wpdb->insert(
				$table_,
				array(
					"notification_id" => $notification_id,
					"meta_key" => $meta_key,
					"meta_value" => $meta_value
				)
			);
		} else { // Update method
			$wpdb->update(
				$table_,
				array(
					"notification_id" => $notification_id,
					"meta_key" => $meta_key,
					"meta_value" => $meta_value
				),
				array (
					"notification_id" => $notification_id,
					"meta_key" => $meta_key,
					"meta_value" => $meta_value
				)
			);
		}
	}

	/*
	*	Function name: get_notification_meta
	*	Function arguments: $notification_id [ INT ] (required), $meta_key [ STRING ] (required)
	*	Function purpose: This function is used to get the custom meta information for the specified Notification.
	*/
	function get_notification_meta( $notification_id, $meta_key ) {
		global $wpdb;
		$table_ = $wpdb->prefix ."user_notificationsmeta";

		$sql_ = "SELECT meta_value FROM $table_ WHERE notification_id='$notification_id' AND meta_key='$meta_key'";
		$result_ = $wpdb->get_results( $sql_, OBJECT );

		return $result_[0]->meta_value;
	}

	/*
	*	Function name: get_notification_metas
	*	Function arguments: $notification_id [ INT ] (required)
	*	Function purpose: This function is used to return all available metas for the specified Notification by $notification_id.
	*/
	function get_notification_metas( $notification_id ) {
		global $wpdb;
		$table_ = $wpdb->prefix ."user_notificationsmeta";

		$sql_ = "SELECT meta_key, meta_value FROM $table_ WHERE notification_id='$notification_id'";
		$results_ = $wpdb->get_results( $sql_, OBJECT );

		$metas_ = array();
		foreach ( $results_ as $_META ) {
			$metas_[ $_META->meta_key ] = $_META->meta_value;
		}

		return (object) $metas_;
	}

	/*
	*	Function name: draft_user_post
	*	Function arguments: $data [ MIXED_OBJECT ] (required)
	*	Function purpose:
	*	This function is used to generate draft for user posts.
	*	The $data argument contains $post_id (which is 0 before the first call of the function), $post_attachment_id (the ID of the post banner), $post_title && $post_content.
	*/
	function draft_user_post( $data ) {
		$attachment_id = is_numeric( $data->post_attachment_id ) == true ? $data->post_attachment_id : "";
		$response = "";

		$data->post_content = $this->convert_iframe_videos( $data->post_content );

		$post_arr = array(
			"ID" => $data->post_id,
			"post_title" => sanitize_text_field( $data->post_title ),
			"post_name" => sanitize_title_with_dashes( $data->post_title ),
			"post_content" => $data->post_content
		);
		$post_id = wp_insert_post( $post_arr );
		if ( is_wp_error( $post_id ) ) { $response = $post_id->get_error_message(); } else { $response = $post_id; }
		if ( !empty( $attachment_id ) && !is_wp_error( $post_id ) ) { $post_thumbnail_meta_id = set_post_thumbnail( $post_id, $attachment_id ); }
		if ( !is_wp_error( $post_id ) ) { update_field( "related_company_id", !empty( $data->company_id ) ? $data->company_id : "", $post_id ); }

		return $response;
	}

	/*
	*	Function name: publish_user_story
	*	Function arguments: $data [ MIXED_OBJECT ] (required)
	*	Function purpose:
	*	This function is used to publish specified user post.
	*/
	function publish_user_story( $data ) {
		if ( !empty( $data->post_id ) ) {
			if ( !empty( $data->company_id ) ) {
				wp_publish_post( $data->post_id );
				return true;
			} else { return "ERROR: Company ID is not set."; }
		} else { return "ERROR: Post ID is not set."; }
	}

	/*
	*	Function name: delete_user_story
	*	Function argument: $data [ MIXED_OBJECT ]
	*	Function purpose: This function is used to delete specified Post by the $post_id.
	*/
	function delete_user_story( $data ) {
		if ( !empty( $data->post_id ) ) {
			if ( !empty( $data->company_id ) ) {
				wp_delete_post( $data->post_id, false );
				return $data->post_id;
			} else { return "ERROR: Company ID is not set."; }
		} else { return "ERROR: Post ID is not set."; }
	}

	/*
	*	Function name: get_company_stories
	*	Function arguments: $data [ MIXED_OBJECT ]
	*	Function purpose:
	*	This function is used to pull user stories in a specified company.
	*	The result can be returnes as JSON object ( when $data->is_ajax=true; ) or as HTML Markup ( when $data->is_ajax=false; )
	*/
	function get_company_stories( $data ) {
		if ( isset( $data->is_ajax ) && $data->is_ajax == true ) { $drafts_container = array(); }
		if ( !isset( $data->requester_id ) || empty( $data->requester_id ) ) { $data->requester_id = get_current_user_id(); }

		if ( !empty( $data->company_id ) ) {
			$args = array(
				"posts_per_page" => $data->stories,
				"order_by" => "date",
				"order" => "DESC",
				"meta_key" => "related_company_id",
				"meta_value" => $data->company_id,
				"post_type" => "post",
				"post_status" => $data->status,
				"author" => "",
				"offset" => isset( $data->offset ) && !empty( $data->offset ) ? $data->offset : 0
			);
			$company_posts = get_posts( $args );

			if ( isset( $data->is_ajax ) && $data->is_ajax == true ) { $stories_container = array(); }

			foreach ( $company_posts as $post_ ) {
				if ( isset( $data->is_ajax ) && $data->is_ajax == true ) {
					$story_container = array();
					$story_container[ "ID" ] = $post_->ID;
					$story_container[ "title" ] = $post_->post_title;
					$story_container[ "content" ] = $this->convert_iframe_videos( $post_->post_content, false );
					$story_container[ "excerpt" ] = wp_trim_words( $this->convert_iframe_videos( $post_->post_content, false ), 50, "..." );
					$story_container[ "banner" ][ "ID" ] = get_post_thumbnail_id( $post_->ID );
					$story_container[ "banner" ][ "url" ] = $this->get_post_banner_url( $post_->ID );
					$story_container[ "date" ] = $post_->post_date;
					$story_container[ "author" ][ "first_name" ] = get_user_meta( $post_->post_author, "first_name", true );
					$story_container[ "author" ][ "last_name" ] = get_user_meta( $post_->post_author, "last_name", true );
					$story_container[ "author" ][ "short_name" ] = get_user_meta( $post_->post_author, "user_shortname", true );
					$story_container[ "author" ][ "avatar_url" ] = $this->get_user_avatar_url( $post_->post_author );
					$story_container[ "author" ][ "banner_url" ] = $this->get_user_banner_url( $post_->post_author );
					$story_container[ "author" ][ "author_url" ] = get_author_posts_url( $post_->post_author );
					$story_container[ "company_id" ] = $data->company_id;
					$story_container[ "meta" ][ "likes_count" ] = count( $this->get_story_likes( $post_->ID ) );
					$story_container[ "meta" ][ "comments_count" ] = get_comments( array( "post_id" => $post_->ID, "count" => true ) );
					$story_container[ "meta" ][ "is_liked" ] = $this->has_liked( get_current_user_id(), $post_->ID );
					$story_container[ "meta" ][ "is_author" ] = $data->requester_id == $post_->post_author && $this->is_employee( $data->company_id, $data->requester_id ) ? true : ( $data->requester_id == $data->company_id ? true : false );
					array_push( $stories_container, (object)$story_container );
				} else {
					?>

					<div id='story-<?php echo $post_->ID; ?>' class='story-container new-story animated fadeInUp'>
						<?php if ( ( $data->requester_id == $post_->post_author && $this->is_employee( $data->company_id, $data->requester_id ) ) || $data->requester_id == $data->company_id ) { ?>
						<div id='story-controls' class='story-controls'>
							<button id='edit-controller' class='fa fa-pencil control'></button>
							<button id='delete-controller' class='fa fa-trash-o control'></button>
						</div>
						<?php } ?>
						<div id='story-banner' class='story-banner' style='background-image: url(<?php echo $this->get_post_banner_url( $post_->ID ); ?>);'>
							<div class='overlay'><span class='message'>Read me!</span></div>
						</div>
						<div class='story-meta'>
							<a href='<?php echo get_author_posts_url( $post_->post_author ); ?>' class='story-author-anchor'>
								<div id='author-avatar' class='story-author-avatar' style='background-image: url(<?php echo $this->get_user_avatar_url( $post_->post_author ); ?>);'></div>
							</a>
							<div class='story-interactions'>
								<button id='story-like-controller' class='like-button fa <?php echo !$this->has_liked( get_current_user_id(), $post_->ID ) ? "fa-heart-o" : "fa-heart"; echo !$this->is_employee( $data->company_id, $data->requester_id ) ? " inactive" : "" ; ?> hvr-bounce-out' story-id='<?php echo $post_->ID; ?>'><i class='numbers'><?php echo count( $this->get_story_likes( $post_->ID ) ); ?></i></button>
								<button id='story-comments-controller' class='comment-button fa fa-comment hvr-bounce-out' story-id='<?php echo $post_->ID; ?>'><i class='numbers'><?php echo get_comments( array( "post_id" => $post_->ID, "count" => true ) ); ?></i></button>
							</div>
						</div>
						<div class='story'>
							<h1 class='story-title'><?php echo $post_->post_title ?></h1>
							<div class='story-excerpt'><?php echo wp_trim_words( $this->convert_iframe_videos( $post_->post_content, false ), 50, "..." ); ?></div>
						</div>
					</div>

					<?php
				}
			}

			if ( isset( $data->is_ajax ) && $data->is_ajax == true ) { return $stories_container; }
		} else { return "ERROR: Company ID is not set."; }
	}

	/*
	*	Function name: get_user_drafts
	*	Function arguments: $data [ MIXED_OBJECT ] (required)
	*	Function purpose:
	*	This function is used to retrieve user posts from the specified Company_Group.
	*/
	function get_user_stories( $data ) {
		$user_id = !empty( $data->user_id ) ? $data->user_id : get_current_user_id();
		$stories_container = array();

		if ( !empty( $data->company_id ) ) {
			$company_id = $data->company_id;

			$args = array(
				"posts_per_page" => -1,
				"order_by" => "date",
				"order" => "DESC",
				"meta_key" => "related_company_id",
				"meta_value" => $company_id,
				"post_type" => "post",
				"post_status" => $data->post_status,
				"author" => $user_id
			);
			$user_posts = get_posts( $args );
			foreach ( $user_posts as $post_ ) {
				$story_container = array();
				$story_container[ "ID" ] = $post_->ID;
				$story_container[ "title" ] = $post_->post_title;
				$story_container[ "content" ] = $this->convert_iframe_videos( $post_->post_content, false );
				$story_container[ "banner" ][ "ID" ] = get_post_thumbnail_id( $post_->ID );
				$story_container[ "banner" ][ "url" ] = $this->get_post_banner_url( $post_->ID );
				$story_container[ "date" ] = $post_->post_date;
				$story_container[ "author" ][ "ID" ] = $post_->post_author;
				$story_container[ "author" ][ "first_name" ] = get_user_meta( $post_->post_author, "first_name", true );
				$story_container[ "author" ][ "last_name" ] = get_user_meta( $post_->post_author, "last_name", true );
				$story_container[ "author" ][ "short_name" ] = get_user_meta( $post_->post_author, "user_shortname", true );
				$story_container[ "author" ][ "avatar_url" ] = $this->get_user_avatar_url( $post_->post_author );
				$story_container[ "author" ][ "banner_url" ] = $this->get_user_banner_url( $post_->post_author );
				$story_container[ "author" ][ "author_url" ] = get_author_posts_url( $post_->post_author );
				$story_container[ "company_id" ] = get_post_meta( $post_->ID, "related_company_id", true );
				$story_container[ "meta" ][ "likes" ] = $this->get_story_likes( $post_->ID );
				$story_container[ "meta" ][ "comments_count" ] = get_comments( array( "post_id" => $post_->ID, "count" => true ) );
				array_push( $stories_container, (object)$story_container );
			}
		} else {
			$stories_container = "ERROR: Company ID is not set.";
		}

		return $stories_container;
	}

	/*
	*	Function name: get_user_story
	*	Function arguments: $data [ MIXED_OBJECT ] (required)
	*	Function purpose:
	*	This function is used to return the full information about the specified by the $post_id Post.
	*/
	function get_user_story( $data ) {
		$post_id = !empty( $data->post_id ) ? $data->post_id : NULL;
		$company_id = !empty( $data->company_id ) ? $data->company_id : NULL;

		$story_container = array();

		if ( empty( $post_id ) || empty( $company_id ) ) {
			$story_container = "ERROR: Post ID or Company ID is empty.";
		} else {
			$post_ = get_post( $post_id );
			$story_container[ "ID" ] = $post_id;
			$story_container[ "title" ] = $post_->post_title;
			$story_container[ "content" ] = $this->convert_iframe_videos( $post_->post_content, false );
			$story_container[ "excerpt" ] = wp_trim_words( $this->convert_iframe_videos( $post_->post_content, false ), 50, "..." );
			$story_container[ "banner" ][ "ID" ] = get_post_thumbnail_id( $post_id );
			$story_container[ "banner" ][ "url" ] = $this->get_post_banner_url( $post_id );
			$story_container[ "date" ] = $post_->post_date;
			$story_container[ "author" ][ "ID" ] = $post_->post_author;
			$story_container[ "author" ][ "first_name" ] = get_user_meta( $post_->post_author, "first_name", true );
			$story_container[ "author" ][ "last_name" ] = get_user_meta( $post_->post_author, "last_name", true );
			$story_container[ "author" ][ "short_name" ] = get_user_meta( $post_->post_author, "user_shortname", true );
			$story_container[ "author" ][ "avatar_url" ] = $this->get_user_avatar_url( $post_->post_author );
			$story_container[ "author" ][ "banner_url" ] = $this->get_user_banner_url( $post_->post_author );
			$story_container[ "author" ][ "author_url" ] = get_author_posts_url( $post_->post_author );
			$story_container[ "company_id" ] = $company_id;
			$story_container[ "meta" ][ "likes" ] = $this->get_story_likes( $post_id );
			$story_container[ "meta" ][ "comments_count" ] = get_comments( array( "post_id" => $post_id, "count" => true ) );
			$story_container[ "meta" ][ "is_liked" ] = $this->has_liked( get_current_user_id(), $post_id );
			$story_container[ "meta" ][ "is_requester_employee" ] = $this->is_employee( $company_id, $user_id );
			$story_container = (object)$story_container;
		}

		return $story_container;
	}

	/*
	*	Function name: count_user_stories
	*	Function arguments: $user_id [ INT ] (required)
	*	Function purpose: This function is used to count the stories of the specified User by $user_id.
	*/
	function count_user_stories( $user_id = "" ) {
		return count_user_posts( empty( $user_id ) ? get_current_user_id() : $user_id );
	}

	/*
	*	Function name: get_story_likes
	*	Function arguments: $story_id [ INT ] (required)
	*	Function purpose: This function is used to collect full information about the likes for the specified by $story_id Company Post.
	*/
	function get_story_likes( $story_id ) {
		global $wpdb;

		$user_likes_table = $wpdb->prefix ."user_likes";

		$sql_ = "
		SELECT id, story_id, user_id, action_date
		FROM $user_likes_table
		WHERE story_id = $story_id
		";

		$results_ = $wpdb->get_results( $sql_, OBJECT );

		return $results_;
	}

	/*
	*	Function name: like_unlike_story
	*	Function arguments: $data [ MIXED_OBJECT ] (required)
	*	Function purpose:
	*	This function is used to send LIKE || UNLINE post requests.
	*	The $data object contains $story_id && $user->id.
	*	The function returns an OBJECT with action: LIKE || UNLIKE && likes_count: CURRENT_POST_LIKES
	*/
	function like_unlike_story( $data ) {
		if ( !empty( $data->story_id ) ) {
			if ( empty( $data->user_id ) ) { $data->user_id = get_current_user_id(); }

			if ( $this->is_employee( get_post_meta( $data->story_id, "related_company_id", true ), $data->user_id ) ) {
				global $wpdb;

				$user_likes_table = $wpdb->prefix ."user_likes";
				$response = array();

				if ( !$this->has_liked( $data->user_id, $data->story_id ) ) { // LIKE the specified story
					$wpdb->insert(
						$user_likes_table,
						array(
							"story_id" => $data->story_id,
							"user_id" => $data->user_id
						)
					);
					$request_id = $wpdb->insert_id;
					$response[ "action" ] = "like";

					$author_id = get_post_field( "post_author", $data->story_id );
					$notification_id = $this->generate_notification( 322, $author_id, $data->user_id );
					$this->generate_notification_meta( $notification_id, "liked_story_id", $data->story_id );
				} else {
					$wpdb->delete( $user_likes_table, array( "user_id" => $data->user_id ) );
					$response[ "action" ] = "dislike";
				}

				$response[ "likes_count" ] = count( $this->get_story_likes( $data->story_id ) );
				$response = (object)$response;

				return $response;
			} else { return "ERROR: Story ID is not set."; }
		}
	}

	/*
	*	Function name: get_story_comments
	*	Function arguments: $data [ MIXED_OBJECT ] (required)
	*	Function purpose: This function returns an ARRAY of OBJECTs, which represent the Comments for the specified by $story_id POSTs.
	*/
	function get_story_comments( $data ) {
		if ( !isset( $data->user_id ) || empty( $data->user_id ) ) { $data->user_id = ""; }

		$args = array(
			"count" => false,
			"orberby" => "comment_date",
			"order" => "DESC",
			"post_id" => $data->story_id,
			"user_id" => $data->user_id
		);
		$comments_ = get_comments( $args );

		if ( is_array( $comments_ ) ) {
			$comments_container = array();
			foreach ( $comments_ as $comment_ ) {
				$comment_container = array();
				$comment_container[ "id" ] = $comment_->comment_ID;
				$comment_container[ "content" ] = $comment_->comment_content;
				$comment_container[ "data" ] = $comment_->comment_date;
				$comment_container[ "user" ][ "id" ] = $comment_->user_id;
				$comment_container[ "user" ][ "avatar" ] = $this->get_user_avatar_url( $comment_->user_id );
				$comment_container[ "user" ][ "banner" ] = $this->get_user_banner_url( $comment_->user_id );
				$comment_container[ "user" ][ "first_name" ] = get_user_meta( $comment_->user_id, "first_name", true );
				$comment_container[ "user" ][ "last_name" ] = get_user_meta( $comment_->user_id, "last_name", true );
				$comment_container[ "user" ][ "user_shortname" ] = get_user_meta( $comment_->user_id, "user_shortname", true );
				$comment_container[ "user" ][ "url" ] = get_author_posts_url( $comment_->user_id );
				$comment_container[ "user" ][ "is_author" ] = get_current_user_id() == $comment_->user_id && $this->is_employee( get_post_meta( $data->story_id, "related_company_id", true ), get_current_user_id() ) ? true : false;
				array_push( $comments_container, (object)$comment_container );
			}

			return $comments_container;
		} else { return ""; }
	}

	/*
	*	Function name: has_liked
	*	Function arguments: $user_id [ INT ] (optional), $story_id [ INT ] (required)
	*	Function purpose: This function is used to tell if the specified by $user_id user has liked the specified by $tory_id POST.
	*/
	function has_liked( $user_id = "", $story_id ) {
		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }

		global $wpdb;
		$user_likes_table = $wpdb->prefix ."user_likes";
		$sql_ = "SELECT * FROM $user_likes_table WHERE story_id=$story_id AND user_id=$user_id";
		return !empty( $wpdb->get_results( $sql_, OBJECT ) ) ? true : false;
	}

	/*
	*	Function name: publish_story_comment
	*	Function arguments: $data [ MIXED_OBJECT ] (required)
	*	Function purpose: This function is used to publish $comment_content for the specified $story_id.
	*/
	function publish_story_comment( $data ) {
		if ( !empty( $data->story_id ) ) {
			if ( !empty( $data->comment_content ) ) {
				if ( empty( $data->user_id ) ) { $data->user_id =  get_current_user_id(); }

				$update_result = 0;
				$comment_id = "";

				if ( !empty( $data->comment_id ) ) {
					$update_result = wp_update_comment( array(
						"comment_ID" => $data->comment_id,
						"comment_content" => $data->comment_content
					) );
				}
				else {
					$commentdata = array(
						"comment_post_ID" => $data->story_id,
						"comment_content" => $data->comment_content,
						"user_id" => $data->user_id
					);
					$comment_id = wp_new_comment( $commentdata );
				}

				if ( is_numeric( $comment_id ) || $update_result == 1 ) {
					$author_id = get_post_field( "post_author", $data->story_id );

					if ( $update_result == 0 ) {
						$notification_id = $this->generate_notification( 324, $author_id, $data->user_id );
						$this->generate_notification_meta( $notification_id, "commented_story_id", $data->story_id );
						$this->generate_notification_meta( $notification_id, "comment_id", !empty( $comment_id ) ? $comment_id : $data->comment_id );
					}

					return !empty( $comment_id ) ? $comment_id : $update_result;
				} else { return ""; }
			} else { return "ERROR: Story Content is not set."; }
		} else { return "ERROR: Story ID is not set."; }
	}

	/*
	*	Function name: delete_story_comment
	*	Function arguments: $comment_id [ INT ] (required)
	*	Function purpose: This function removes the specified by $comment_id, story comment.
	*/
	function delete_story_comment( $comment_id ) { return array( "result" => wp_delete_comment( $comment_id, true ), "comment_id" => $comment_id );	}

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
			$media_space = 1000000000;
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

	/*
	*	Function name: is_alphabetical
	*	Function arguments: $input [ STRING ] (required) (the string which shoud be checked)
	*	Function purpose: This function checks if the $input is created only from ALPHABETICAL CHARs.
	*/
	function is_alphabetical( $input ) {
		return ctype_alpha( $input ) ? true : false;
	}

	/*
	*	Function name: get_user_media
	*	Function arguments: $data [ MIXED_OBJECT ] (required) (this OBJECT mainly contains the $user_id ($company_id) and the request type AJAX or NOT)
	*	Function purpose: This function is used to generate containers with the MEDIA_FILES of the specific user.
	*/
	function get_user_media( $data ) {
		$user_id = $data->user_id;
		$is_ajax = $data->is_ajax;
		$offset = !empty( $data->offset ) && isset( $data->offset ) ? $data->offset : 0;

		if ( empty( $user_id ) ) { $user_id = get_current_user_id(); }

		$args = array(
			"posts_per_page" => 20,
			"offset" => $offset,
			"post_type" => "attachment",
			"orderby" => "ID",
			"order" => "DESC",
			"meta_key" => "owner_id",
			"meta_value" => $user_id
		);
		$medias_ = get_posts( $args );

		if ( $is_ajax ) { $medias_holder = array(); }

		if ( count( $medias_ ) > 0 ) {
			foreach ( $medias_ as $media_ ) {
				$background_url =
					explode( "/", $media_->post_mime_type )[1] == "zip" ? get_template_directory_uri() ."/assets/images/zip-icon.png" :
						( explode( "/", $media_->post_mime_type )[0] == "image" || explode( "/", $media_->post_mime_type )[0] == "video" ? wp_get_attachment_url( $media_->ID ) : get_template_directory_uri() ."/assets/images/file-icon.png" );

				if ( !$is_ajax ) {
					if ( explode( "/", $media_->post_mime_type )[0] == "image" ) {
				?>
					<div id='media-<?php echo $media_->ID; ?>' class='media-container animated bounceIn' style='background-image: url(<?php echo $background_url; ?>);' media-type='<?php echo $media_->post_mime_type; ?>'>
						<button id='marker'></button>
					</div>
				<?php
					} elseif ( explode( "/", $media_->post_mime_type )[0] == "video" ) {
					?>
					<div id='media-<?php echo $media_->ID; ?>' class='media-container animated bounceIn' media-type='<?php echo $media_->post_mime_type; ?>'>
						<button id='marker'></button>
						<video autoplay="true" muted="true" loop="true">
							<source src="<?php echo $background_url; ?>" type="<?php echo $media_->post_mime_type; ?>">
						</video>
						<div class="overlay"></div>
					</div>
					<?php
					}
				} elseif ( $is_ajax ) {
					$media_holder = array();
					$media_holder[ "ID" ] = $media_->ID;
					$media_holder[ "URL" ] = $background_url;
					$media_holder[ "TYPE" ] = $media_->post_mime_type;
					array_push( $medias_holder, (object)$media_holder );
				}
			}

			if ( $is_ajax ) { return json_encode( $medias_holder ); }
		} else {
			if ( !$is_ajax ) { echo "<h1 class='no-information-message'>You don't have any media.</h1>"; }
		 	else { return json_encode( "You don't have any media." ); }
		}
	}

	/*
	*	Function name: delete_user_media
	*	Function arguments: $data [ MIXED_OBJECT ] (required) (this OBJECT mainly contains the $user_id ($company_id) and the $attachment_id)
	*	Function purpose: This function is used to delete MEDIA_FILE from the HUB DB & HDD.
	*/
	function delete_user_media( $data ) {
		$user_id = !empty( $data->user_id ) ? $data->user_id : get_current_user_id();
		$attachment_id = $data->attachment_id;
		$result = false;

		if ( !empty( $attachment_id ) && isset( $attachment_id ) ) {
			$file_size = filesize( $this->get_attachment_path( $attachment_id ) );
			$current_available_space = $this->get_available_media_space( $user_id );
			$updated_available_space = $current_available_space + $file_size;

			if ( wp_delete_attachment( $attachment_id, true ) ) { update_user_meta( $user_id, "media_space", $updated_available_space ); $result = true; }
		}

		return $result;
	}

	/*
	*	Function name: get_attachment_path
	*	Function arguments: $attachment_id [ INT ] (required) (the ID of the desired ATTACHEMNT)
	*	Function purpose:
	*	This function is used as a replacement of get_attached_file() build in WordPress function.
	*	Reason for this is because somethimes the get_attached_file() function sometimes returns mixed PATHs: SERVER_PATH & URL;
	*	This function checks and clears the path IF NEEDED.
	*/
	function get_attachment_path( $attachment_id ) {
		$path_ = get_attached_file( $attachment_id );

		if ( strpos( $path_, "http" ) || strpos( $path_, "https" ) ) {
			$extract_url = explode( "/", explode( "://", $path_ )[1] );

			$year_path = $extract_url[ 3 ];
			$month_path = $extract_url[ 4 ];
			$file_name = $extract_url[ 5 ];

			$path_ = get_home_path() ."wp-content/uploads/". $year_path ."/". $month_path ."/". $file_name;
		}

		return $path_;
	}

	/*
	*	Function name: get_attachment_url
	*	Function arguments: $attachment_id [ INT ] (required) (the ID of the desired ATTACHMENT)
	*	Function purpose:
	*	This function is used to return the URL to the Attachment pointed by the $attachment_id parameter.
	*/
	function get_attachment_url( $attachment_id ) {
		return wp_get_attachment_url( $attachment_id );
	}

	/*
	*	Function name: get_hubbers
	*	Function arguments: $data [ MIXED_OBJECT / ARRAY ] (optional)
	*	Function purpose:
	*	This function is used to retrieve users from the HUB project.
	*	It can be used to return users from specified Company_Group or the all users which are registered in the HUB project.
	*	Example can be found at the original_HUB_project/hubbers.
	*/
	function get_hubbers( $data = array() ) {
		$args = array(
			"meta_key" => "account_association",
			"meta_value" => "employee",
			"meta_compare" => "=",
			"orderby" => !empty( $data->orderby ) ? $data->orderby : "ID",
			"order" => !empty( $data->order ) ? $data->order : "DESC",
			"offset" => !empty( $data->offset ) ? $data->offset : 0,
			"number" => !empty( $data->number ) ? $data->number : 20,
			"fields" => "ID"
		);
		$user_ids = get_users( $args );

		if ( isset( $data->is_ajax ) && $data->is_ajax ) { $hubbers_holder = array(); }

		if ( empty( $user_ids ) ) { if ( empty( $data->is_ajax ) || !$data->is_ajax ) { echo "<h1 class='no-information-message'>There aren't any users.</h1>"; } else { return json_encode( "There aren't any users." ); } }
		else {
			foreach ( $user_ids as $user_id ) {
				$user_first_name = get_user_meta( $user_id, "first_name", true );
				$user_last_name = get_user_meta( $user_id, "last_name", true );
				$user_short_name = get_user_meta( $user_id, "user_shortname", true );
				$user_avatar = $this->get_user_avatar_url( $user_id );
				$user_banner = $this->get_user_banner_url( $user_id );

				if ( !isset( $data->is_ajax ) || !$data->is_ajax ) {
					?>

					<a href="<?php echo get_author_posts_url( $user_id ); ?>" id='user-anchor-<?php echo $user_id; ?>' class='user-anchor'>
						<div id='user-<?php echo $user_id; ?>' class='list-item animated fadeIn' style='background-image: url(<?php echo $user_banner; ?>);'>
							<div class='overlay'>
								<div id='user-avatar-<?php echo $user_id; ?>' class='avatar' style='background-image: url(<?php echo $user_avatar; ?>);'>
								</div>
								<h1 id='user-brand-<?php echo $user_id; ?>' class='user-brand'><?php echo !empty( $user_short_name ) ? $user_short_name : $user_first_name ." ". $user_last_name; ?></h1>
							</div>
						</div>
					</a>

					<?php
				} else {
					$user_holder = array();
					$user_holder[ "ID" ] = $user_id;
					$user_holder[ "AVATAR_URL" ] = $user_avatar;
					$user_holder[ "BANNER_URL" ] = $user_banner;
					$user_holder[ "USER_URL" ] = get_author_posts_url( $user_id );
					$user_holder[ "FIRST_NAME" ] = $user_first_name;
					$user_holder[ "LAST_NAME" ] = $user_last_name;
					$user_holder[ "SHORT_NAME" ] = $user_short_name;
					array_push( $hubbers_holder, (object)$user_holder );
				}
			}

			if ( !isset( $data->is_ajax ) || !$data->is_ajax ) {
				if ( count( $user_ids ) == ( isset( $data->number ) && empty( $data->number ) ? $data->number : 20 ) ) {
					?>
					<button id="more-users-controller" class="blue-skeleton-bold-button display-block mh-auto mt-1em">Load more</button>
				 	<?php
				}
			} else if ( isset( $data->is_ajax ) && $data->is_ajax ) { return json_encode( $hubbers_holder ); }
		}
	}

	/*
	*	Function name: get_companies
	*	Function arguments: $data [ MIXED_OBJECT ] (optional) (it holds information about the ORDERING{ orderby, order }, OFFSET, NUMBERs, IS_AJAX)
	*	Function purpose: This function is used to return containers with link to the all companies from the HUB which are public.
	*/
	function get_companies( $data = array() ) {
		$args = array(
			"meta_query" => array(
				"relation" => "AND",
				array(
					"key" => "account_association",
					"value" => "company",
					"compare" => "="
				),
				array(
					"key" => "company_type",
					"value" => "public",
					"compare" => "="
				)
			),
			"orderby" => !empty( $data->orderby ) ? $data->orderby : "ID",
			"order" => !empty( $data->order ) ? $data->order : "DESC",
			"offset" => !empty( $data->offset ) ? $data->offset : 0,
			"number" => !empty( $data->number ) ? $data->number : 20,
			"fields" => "ID"
		);
		$user_ids = get_users( $args );

		if ( isset( $data->is_ajax ) && $data->is_ajax ) { $companies_holder = array(); }

		if ( empty( $user_ids ) ) { if ( empty( $data->is_ajax ) || !$data->is_ajax ) { echo "<h1 class='no-information-message'>There aren't any public companies.</h1>"; } else { return json_encode( "There aren't any public companies." ); } }
		else {
			foreach ( $user_ids as $user_id ) {
				$user_first_name = get_user_meta( $user_id, "first_name", true );
				$user_last_name = get_user_meta( $user_id, "last_name", true );
				$user_short_name = get_user_meta( $user_id, "user_shortname", true );
				$user_avatar = $this->get_user_avatar_url( $user_id );
				$user_banner = $this->get_user_banner_url( $user_id );

				if ( empty( $data->is_ajax ) || !$data->is_ajax ) {
					?>
					<a href="<?php echo get_author_posts_url( $user_id ); ?>" id='company-anchor-<?php echo $user_id; ?>' class='company-anchor'>
						<div id='company-<?php echo $user_id; ?>' class='list-item animated fadeIn' style='background-image: url(<?php echo $user_banner; ?>);'>
							<div class='overlay'>
								<div id='company-avatar-<?php echo $user_id; ?>' class='avatar' style='background-image: url(<?php echo $user_avatar; ?>);'>
								</div>
								<h1 id='company-brand-<?php echo $user_id; ?>' class='company-brand'><?php echo !empty( $user_short_name ) ? $user_short_name : $user_first_name ." ". $user_last_name; ?></h1>
							</div>
						</div>
					</a>
					<?php
				} else {
					$company_holder = array();
					$company_holder[ "ID" ] = $user_id;
					$company_holder[ "AVATAR_URL" ] = $user_avatar;
					$company_holder[ "BANNER_URL" ] = $user_banner;
					$company_holder[ "COMPANY_URL" ] = get_author_posts_url( $user_id );
					$company_holder[ "FIRST_NAME" ] = $user_first_name;
					$company_holder[ "LAST_NAME" ] = $user_last_name;
					$company_holder[ "SHORT_NAME" ] = $user_short_name;
					array_push( $companies_holder, (object)$company_holder );
				}
			}

			if ( !isset( $data->is_ajax ) || !$data->is_ajax ) {
				if ( count( $user_ids ) == ( isset( $data->number ) && !empty( $data->number ) ? $data->number : 20 ) ) {
					?>
					<button id="more-companies-controller" class="blue-skeleton-bold-button display-block mh-auto mt-1em">Load more</button>
					<?php
				}
			} else if ( isset( $data->is_ajax ) && $data->is_ajax ) { return json_encode( $companies_holder ); }
		}
	}

	/*
	*	Function name: send_invite_request
	*	Function arguments: $data [ MIXED_OBJECT ] (required) this object holds the $user_id and the $company_id
	*	Function purpose: This function is used to generate Company Invite Request (CIR) to the specified by $user_id user from the HUB.
	*/
	function send_invite_request( $data ) {
		$user_id = $data->user_id;
		$company_id = !empty( $data->company_id ) ? $data->company_id : get_current_user_id();
		$result = "";

		if ( !empty( $user_id ) && isset( $user_id ) ) {
			global $wpdb;
			$table_ = $wpdb->prefix ."user_requests";

			$sql_ = "SELECT * FROM $table_ WHERE requester_id='$company_id' AND company_id='$user_id' AND request_type='invite'";
			$result_ = $wpdb->get_results( $sql_, OBJECT );

			if ( count( $result_ ) > 0 ) { // Update method
				$request_id = $result_[0]->id;
				$wpdb->update(
					$table_,
					array(
						"requester_id" => $company_id,
						"request_response" => NULL,
						"company_id" => $user_id,
						"request_type" => "invite"
					),
					array (
						"id" => $request_id
					)
				);
			} else { // Insert method
				$wpdb->insert(
					$table_,
					array(
						"requester_id" => $company_id,
						"company_id" => $user_id,
						"request_type" => "invite"
					)
				);
				$request_id = $wpdb->insert_id;
			}

			$notification_id = $this->generate_notification( 228, $user_id, $company_id );
			$this->generate_notification_meta( $notification_id, "company_inviterequest_id", $request_id );
		} else { $result = "ERROR: User ID is empty."; }

		return $result;
	}

	/*
	*	Function name: send_join_request
	*	Function arguments: $data [ MIXED_OBJECT ] (required) (contains the $company_id, $user_id, $user_cv_link, $user_portfolio_link)
	*	Function purpose: This function is used to send a JOIN request to the chosed by the user company.
	*/
	function send_join_request( $data ) {
		$company_id = $data->company_id;
		$user_id = !empty( $data->user_id ) ? $data->user_id : get_current_user_id();
		$user_cv_link = !empty( $data->user_cv_link ) ? $data->user_cv_link : "";
		$user_portfolio_link = !empty( $data->user_portfolio_link ) ? $data->user_portfolio_link : "";

		$result = "";

		if ( !empty( $company_id ) ) {
			if ( empty( $user_cv_link ) ) { $result = "You CV is required to apply!"; }
			else {
				if ( !filter_var( $user_cv_link, FILTER_VALIDATE_URL ) ) { $result = "The link to your CV is not valid!"; }
				else {
					if ( !empty( $user_portfolio_link ) && !filter_var( $user_portfolio_link, FILTER_VALIDATE_URL ) ) { $result = "The link to your portfolio is not valid!"; }
					else {
						global $wpdb;
						$table_ = $wpdb->prefix ."user_requests";

						$sql_ = "SELECT * FROM $table_ WHERE requester_id='$user_id' AND company_id='$company_id' AND request_type='join'";
						$result_ = $wpdb->get_results( $sql_, OBJECT );

						if ( count( $result_ ) > 0 ) { // Update method
							$request_id = $result_[0]->id;
							$wpdb->update(
								$table_,
								array(
									"requester_id" => $user_id,
									"requester_cv" => esc_url( $user_cv_link ),
									"requester_portfolio" => esc_url( $user_portfolio_link ),
									"company_id" => $company_id,
									"request_type" => "join"
								),
								array (
									"id" => $request_id
								)
							);
						} else { // Insert method
							$wpdb->insert(
								$table_,
								array (
									"requester_id" => $user_id,
									"requester_cv" => esc_url( $user_cv_link ),
									"requester_portfolio" => esc_url( $user_portfolio_link ),
									"company_id" => $company_id,
									"request_type" => "join"
								)
							);
							$request_id = $wpdb->insert_id;
						}


						$notification_id = $this->generate_notification( 223, $company_id, $user_id );
						$this->generate_notification_meta( $notification_id, "company_joinrequest_id", $request_id );

						$result = "requested";
					}
				}
			}
		} else { $result = "ERROR: Company ID is empty."; }

		return $result;
	}

	/*
	*	Function name: update_join_request
	*	Function argumnets: $data [ MIXED_OBJECT ] (required) (holds the $request_id && the $request_response given by the Company owner)
	*	Function purpose: This function is used to update with answer (accept / decline) already created Company Join Request.
	*/
	function update_join_request( $data ) {
		$result = false;

		if ( !empty( $data->request_id ) && isset( $data->request_id ) && !empty( $data->response ) && isset( $data->response ) ) {
			global $wpdb;
			$table_ = $wpdb->prefix ."user_requests";
			$wpdb->update(
				$table_,
				array( "request_response" => $data->response ),
				array( "id" => $data->request_id )
			);

			$sql_ = "SELECT * FROM $table_ WHERE id='$data->request_id'";
			$result_ = $wpdb->get_results( $sql_, OBJECT )[0];

			if ( $data->response == "accept" ) {
				if ( $data->request_type == "join" ) {
					$this->hire_or_fire_relation( (object)array( "user_id" => $result_->requester_id, "company_id" => $result_->company_id ) );
					$this->generate_notification( 225, $result_->requester_id, $result_->company_id );
				} else {
					$this->hire_or_fire_relation( (object)array( "user_id" => $result_->company_id, "company_id" => $result_->requester_id ) );
					$this->generate_notification( 231, $result_->requester_id, $result_->company_id );
				}
			} else {
				if ( $data->request_type == "join" ) { $this->generate_notification( 226, $result_->requester_id, $result_->company_id ); }
				else { $this->generate_notification( 232, $result_->requester_id, $result_->company_id ); }
			}
		}

		return $result;
	}

	/*
	*	Function name: get_requests
	*	Function arguments: $data [ MIXED_OBJECT ] (required) (usually holds the $company_id && the $is_ajax pointer: TRUE || FALSE;)
	*	Function purpose: This function is used to get all requests send to the specified company.
	*/
	function get_requests( $data ) {
		$company_id = !empty( $data->company_id ) ? $data->company_id : get_current_user_id();
		$requests_holder = array();
		$listed_users = array();

		global $wpdb;
		$table_ = $wpdb->prefix ."user_requests";

		$sql_ = "SELECT DISTINCT * FROM $table_ WHERE company_id='$company_id' ORDER BY request_date DESC";
		$results_ = $wpdb->get_results( $sql_, OBJECT );

		foreach ( $results_ as $request_ ) {
			if ( !in_array( $request_->requester_id, $listed_users ) ) {
				$user_first_name = get_user_meta( $request_->requester_id, "first_name", true );
				$user_last_name = get_user_meta( $request_->requester_id, "last_name", true );
				$user_short_name = get_user_meta( $request_->requester_id, "user_shortname", true );

				if ( !isset( $data->is_ajax ) || !$data->is_ajax ) {
					?>

					<a href="<?php echo get_permalink( 85 ); ?>?request_id=<?php echo $request_->id; ?>" class="request-anchor">
						<div id="request-<?php echo $request_->id; ?>" class="list-item">
							<div class="avatar" style="background-image: url(<?php echo $this->get_user_avatar_url( $request_->requester_id ); ?>);'"></div>
							<h1 class="names"><?php echo !empty( $user_short_name ) ? $user_short_name : $user_first_name ." ". $user_last_name; ?></h1>
							<div class="list-item-meta">
								<?php if ( !empty( $request_->request_response ) ) { ?> <p class="meta icon <?php echo $request_->request_response == "accept" ? "green" : "red"; ?>"><i class="fa <?php echo $request_->request_response == "accept" ? "fa-check" : "fa-close" ?>"></i></p> <?php } ?>
								<?php if ( !empty( $request_->requester_cv ) ) { ?> <p class="meta icon blue">CV</p> <?php } ?>
								<?php if ( !empty( $request_->requester_portfolio ) ) { ?> <p class="meta icon green">PF</p> <?php } ?>
								<p class="meta"><?php echo date( "d-m-Y", strtotime( $request_->request_date ) ); ?></p>
							</div>
						</div>
					</a>

					<?php
				} elseif ( $data->is_ajax ) {
					$request_holder = array();
					$request_holder[ "ID" ] = $request_->id;
					$request_holder[ "REQUESTER_ID" ] = $request_->requester_id;
					$request_holder[ "REQUESTER_CV" ] = $request_->requester_cv;
					$request_holder[ "REQUESTER_PORTFOLIO" ] = $request_->requester_portfolio;
					$request_holder[ "REQUEST_DATE" ] = $request_->request_date;
					$request_holder[ "REQUEST_RESPONSE" ] = $request_->request_response;
					$request_holder[ "COMPANY_ID" ] = $request_->company_id;
					array_push( $requests_holder, (object)$request_holder );
				}

				array_push( $listed_users, $request_->requester_id );
			}
		}

		if ( isset( $data->is_ajax ) && $data->is_ajax ) { return json_encode( $requests_holder ); }
	}

	/*
	*	Function name: get_requests
	*	Function arguments: $request_id [ INT ] (required) (the ID of the desired request in the HUB DB)
	*	Function purpose: This function is used to return Object with the information about the specified request by the $request_id variable.
	*/
	function get_request( $request_id ) {
		$result_ = false;
		if ( !empty( $request_id ) && isset( $request_id ) ) {
			global $wpdb;
			$table_ = $wpdb->prefix ."user_requests";

			$sql_ = "SELECT * FROM $table_ WHERE id='$request_id'";
			$result_ = $wpdb->get_results( $sql_, OBJECT );
			if ( !empty( $result_[0] ) ) { $result_ = $result_[0]; }
		}
		return $result_;
	}

	/*
	*	Function name: leave_company
	*	Function arguments: $data [ MIXED_OBJECT ] (required) (the ID of the User && the Company are here)
	*	Function purpose: This function is used to remove an employee from a company.
	*/
	function leave_company( $data ) {
		$user_id = !empty( $data->user_id ) ? $data->user_id : get_current_user_id();
		$user_password = $data->password;
		$company_id = $data->company_id;

		$result = "";

		if ( empty( $company_id ) || !isset( $company_id ) ) { $result = "ERROR: Company ID is unknown."; }
		if ( empty( $user_password ) || !isset( $user_password ) ) { $result = "Enter your password!"; }
		else {
			$user_ = get_user_by( "ID", $user_id );
			if ( $user_ && wp_check_password( $user_password, $user_->data->user_pass, $user_id ) ) {
				$leave_company = $this->hire_or_fire_relation( (object)array( "user_id" => $user_id, "company_id" => $company_id ) );
				if ( $leave_company == "fired" ) {
					$notification_id = $this->generate_notification( 233, $company_id, $user_id );
					$result = "left";
				}
			} else {
				$result = "Your password is wrong!";
			}
		}

		return $result;
	}

	/*
	*	Function name: get_post_banner_url
	*	Function arguments: $post_id [ INT ] (required)
	*	Function purpose: This function is used to retrieve the banner URL of the specified Post by the $post_id variable.
	*/
	function get_post_banner_url( $post_id ) { return wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), "full" )[0]; }

	/*
	*	Function name: set_story_view
	*	Function arguments: $data [ MIXED_OBJECT ] (required)
	*	Function purpose: This function is used to add or update views from specified $user_id to specific $story_id.
	*/
	function set_story_view( $data ) {
		if ( !isset( $data->user_id ) && empty( $data->user_id ) ) { $data->user_id = get_current_user_id(); }

		$response = "";

		if ( isset( $data->story_id ) && !empty( $data->story_id ) ) {
			global $wpdb;
			$table_ = $wpdb->prefix ."story_views";

			$sql_ = "SELECT * FROM $table_ WHERE story_id=$data->story_id AND user_id=$data->user_id";
			$results_ = $wpdb->get_results( $sql_, OBJECT );

			if ( count( $results_ ) > 0 ) { // Update method
				$wpdb->update(
					$table_,
					array(
						"views" => ++$results_[ 0 ]->views
					),
					array(
						"id" => $results_[ 0 ]->id
					)
				);
			} else {
				$wpdb->insert(
					$table_,
					array(
						"story_id" => $data->story_id,
						"user_id" => $data->user_id,
						"views" => 1
					)
				);
			}
		} else { $response = "ERROR: Story ID is not set."; }

		return $response;
	}

	/*
	*	Function name: get_user_board
	*	Function arguments: $data [ MIXED_OBJECT ] (required)
	*	Function purpose: This function is used to pull the stories connected with the specified $user_id based on User: Compositions, Views && Likes
	*/
	function get_user_board( $data ) {
		if ( !isset( $data->user_id ) || empty( $data->user_id ) ) { $data->user_id = get_current_user_id(); }
		if ( !isset( $data->offset ) || empty( $data->offset ) ) { $data->offset = 0; }

		if ( isset( $data->user_compositions ) && $data->user_compositions == true ) {
			$args = array(
				"posts_per_page" => 5,
				"offset" => $data->offset,
				"order_by" => "date",
				"order" => "DESC",
				"post_type" => "post",
				"author" => $data->user_id,
				"post_status" => "publish"
			);
			$compositions_ = get_posts( $args );

			if ( isset( $data->is_ajax ) && $data->is_ajax == true ) { $stories_container = array(); }

			foreach ( $compositions_ as $post_ ) {
				$company_id = get_post_meta( $post_->ID, "related_company_id", true );

				if ( $this->is_company_public( $company_id ) ) {
					$post_banner = $this->get_post_banner_url( $post_->ID );
					$post_excerpt = wp_trim_words( $post_->post_content, 50, "..." );
					$post_likes = count( $this->get_story_likes( $post_->ID ) );
					$post_url = get_permalink( $post_->ID );
					$company_avatar = $this->get_user_avatar_url( $company_id );
					$company_url = get_author_posts_url( $company_id );

					if ( isset( $data->is_ajax ) && $data->is_ajax == true ) {
						$story_container = array();
						$story_container[ "ID" ] = $post_->ID;
						$story_container[ "title" ] = $post_->post_title;
						$story_container[ "content" ] = $post_->post_content;
						$story_container[ "excerpt" ] = $post_excerpt;
						$story_container[ "banner" ] = $post_banner;
						$story_container[ "url" ] = $post_url;
						$story_container[ "author" ][ "ID" ] = $post_->post_author;
						$story_container[ "author" ][ "avatar_url" ] = $this->get_user_avatar_url( $post_->post_author );
						$story_container[ "author" ][ "banner_url" ] = $this->get_user_banner_url( $post_->post_author );
						$story_container[ "author" ][ "author_url" ] = get_author_posts_url( $post_->post_author );
						$story_container[ "author" ][ "first_name" ] = get_user_meta( $post_->post_author, "first_name", true );
						$story_container[ "author" ][ "last_name" ] = get_user_meta( $post_->post_author, "last_name", true );
						$story_container[ "author" ][ "short_name" ] = get_user_meta( $post_->post_author, "user_shortname", true );
						$story_container[ "company" ][ "ID" ] = $company_id;
						$story_container[ "company" ][ "avatar_url" ] = $company_avatar;
						$story_container[ "company" ][ "banner_url" ] = $this->get_user_banner_url( $company_id );
						$story_container[ "company" ][ "company_url" ] = $company_url;
						$story_container[ "company" ][ "first_name" ] = get_user_meta( $company_id, "first_name", true );
						$story_container[ "company" ][ "last_name" ] = get_user_meta( $company_id, "last_name", true );
						$story_container[ "company" ][ "short_name" ] = get_user_meta( $company_id, "user_shortname", true );
						$story_container[ "meta" ][ "likes" ] = $post_likes;
						array_push( $stories_container, (object)$story_container );
					} else {
						?>

						<a href="<?php echo $post_url; ?>" class="post-anchor">
							<div id="story-<?php echo $post_->ID; ?>" class="story-container animated fadeInUp">
								<div class="story-banner" style="background-image: url(<?php echo $post_banner; ?>);"></div>
								<h1 class="story-title"><?php echo $post_->post_title; ?></h1>
								<div class="story-content"><?php echo $post_excerpt; ?></div>
								<div class="story-meta">
									<span class="meta story-likes fa fa-heart"><i class="numbers"><?php echo $post_likes; ?></i></span>
									<span class="meta" title="Company"><i class="icon fa fa-at"></i><div class="avatar" style="background-image: url(<?php echo $company_avatar; ?>);"></div></span>
								</div>
							</div>
						</a>

						<?php
					}
				}
			}
		} else {
			global $wpdb;

			$user_likes_table = $wpdb->prefix ."user_likes";
			$story_views_table = $wpdb->prefix ."story_views";

			$post_ids = array();
			$data->offset = $data->offset / 2;

			$sql_ = "
			SELECT story_id FROM $user_likes_table
			WHERE user_id = $data->user_id
			ORDER BY story_id DESC
			LIMIT 5
			OFFSET ". $data->offset;
			$results_ = $wpdb->get_results( $sql_, OBJECT );

			if ( count( $results_ ) > 0 ) {
				foreach ( $results_ as $post_data ) { array_push( $post_ids, $post_data->story_id ); }
				$sql_extension = "story_id NOT IN ( ". implode( ",", $post_ids ) ." )";
			} else {
				$sql_extension = "story_id NOT IN ( SELECT story_id FROM $user_likes_table WHERE user_id = $data->user_id )";
			}

			$sql_ ="
			SELECT story_id FROM $story_views_table
			WHERE user_id = $data->user_id AND $sql_extension
			ORDER BY story_id DESC
			LIMIT 5
			OFFSET ". $data->offset;
			$results_ = $wpdb->get_results( $sql_, OBJECT );

			if ( count( $results_ ) ) { foreach ( $results_ as $post_data ) { array_push( $post_ids, $post_data->story_id ); } }

			if ( isset( $data->is_ajax ) && $data->is_ajax == true ) { $stories_container = array(); }

			if ( !empty( $post_ids ) ) {
				$args = array(
					"posts_per_page" => -1,
					"post__in" => $post_ids,
					"post_type" => "post",
					"post_status" => "publish"
				);
				$posts_ = get_posts( $args );

				foreach ( $posts_ as $post_ ) {
					$company_id = get_post_meta( $post_->ID, "related_company_id", true );

					if ( $this->is_company_public( $company_id ) ) {
						$post_banner = $this->get_post_banner_url( $post_->ID );
						$post_excerpt = wp_trim_words( $post_->post_content, 50, "..." );
						$post_likes = count( $this->get_story_likes( $post_->ID ) );
						$post_url = get_permalink( $post_->ID );
						$company_avatar = $this->get_user_avatar_url( $company_id );
						$company_url = get_author_posts_url( $company_id );
						$author_avatar = $this->get_user_avatar_url( $post_->post_author );

						if ( isset( $data->is_ajax ) && $data->is_ajax == true ) {
							$story_container = array();
							$story_container[ "ID" ] = $post_->ID;
							$story_container[ "title" ] = $post_->post_title;
							$story_container[ "content" ] = $post_->post_content;
							$story_container[ "excerpt" ] = $post_excerpt;
							$story_container[ "banner" ] = $post_banner;
							$story_container[ "url" ] = $post_url;
							$story_container[ "author" ][ "ID" ] = $post_->post_author;
							$story_container[ "author" ][ "avatar_url" ] = $author_avatar;
							$story_container[ "author" ][ "banner_url" ] = $this->get_user_banner_url( $post_->post_author );
							$story_container[ "author" ][ "author_url" ] = get_author_posts_url( $post_->post_author );
							$story_container[ "author" ][ "first_name" ] = get_user_meta( $post_->post_author, "first_name", true );
							$story_container[ "author" ][ "last_name" ] = get_user_meta( $post_->post_author, "last_name", true );
							$story_container[ "author" ][ "short_name" ] = get_user_meta( $post_->post_author, "user_shortname", true );
							$story_container[ "company" ][ "ID" ] = $company_id;
							$story_container[ "company" ][ "avatar_url" ] = $company_avatar;
							$story_container[ "company" ][ "banner_url" ] = $this->get_user_banner_url( $company_id );
							$story_container[ "company" ][ "company_url" ] = $company_url;
							$story_container[ "company" ][ "first_name" ] = get_user_meta( $company_id, "first_name", true );
							$story_container[ "company" ][ "last_name" ] = get_user_meta( $company_id, "last_name", true );
							$story_container[ "company" ][ "short_name" ] = get_user_meta( $company_id, "user_shortname", true );
							$story_container[ "meta" ][ "likes" ] = $post_likes;
							array_push( $stories_container, (object)$story_container );
						} else {
							?>

							<a href="<?php echo $post_url ?>" class="post-anchor">
								<div id="story-<?php echo $post_->ID; ?>" class="story-container animated fadeInUp">
									<div class="story-banner" style="background-image: url(<?php echo $post_banner; ?>);"></div>
									<h1 class="story-title"><?php echo $post_->post_title; ?></h1>
									<div class="story-content"><?php echo $post_excerpt; ?></div>
									<div class="story-meta">
										<span class="meta story-likes fa fa-heart"><i class="numbers"><?php echo $post_likes; ?></i></span>
										<span class="meta" title="Author"><i class="icon fa fa-pencil"></i><div class="avatar" style="background-image: url(<?php echo $author_avatar ?>);"></div></span>
										<span class="meta" title="Company"><i class="icon fa fa-at"></i><div class="avatar" style="background-image: url(<?php echo $company_avatar; ?>);"></div></span>
									</div>
								</div>
							</a>

							<?php
						}
					}
				}
			}
		}

		if ( isset( $data->is_ajax ) && $data->is_ajax == true ) { return $stories_container; }
	}

	/*
	*	Function name: convert_iframe_videos
	*	Function arguments: $content [ STRING ] (reqired), $sanitize [ BOOLEAN ] (optional)
	*	Function purpose: This function is used to convert the <iframe> tag into [ev] tag for the DB (if $sanitize == true) and back to <iframe> (if $sanitize == false).
	*/
	function convert_iframe_videos( $content, $sanitize = true ) {
		if ( $sanitize == true ) {
			$content = str_replace( "<iframe", "[ev", $content );
			$content = str_replace( "</iframe", "[/ev", $content );
		} else {
			$content = str_replace( "[ev", "<iframe", $content );
			$content = str_replace( "[/ev", "</iframe", $content );
		}

		return $content;
	}

	/*
	*	Function name: register_notification
	*	Function arguments: $notification_title [ STIRNG ] (required), $notification_slug [ STRING ] (required), $notification_name [ STRING ] (required), $notification_url [ STRING ] (required), $notificaiton_text [ STRING ] (required), $notification_icon_code [ STRING: FONTAWESOME fa-NAME ] (required), $notification_color_code [ STRING: HEX color code ] (required)
	*	Function purpose: This function is used to create custom notificaiton template.
	*/
	function register_notification( $notification_title, $notification_slug, $notification_name, $notification_url, $notification_text, $notification_icon_code, $notification_color_code, $notification_parser ) {
		$notification_title = sanitize_text_field( $notification_title );
		$notification_slug = sanitize_text_field( $notification_slug );
		$notification_name = sanitize_text_field( $notification_name );
		$notification_url = sanitize_text_field( $notification_url );
		$notification_text = sanitize_text_field( $notification_text );
		$notificaiton_icon_code = sanitize_text_field( $notification_icon_code );
		$notification_color_code = sanitize_text_field( $notification_color_code );
		$notification_parser = sanitize_text_field( $notification_parser );

		$args = array(
			"posts_per_page" => 1,
			"post_type" => "notifications",
			"name" => $notification_slug
		);

		$page_ = get_posts( $args );

		if ( isset( $page_ ) && !empty( $page_ ) ) { return $page_[ 0 ]; }
		else {
			$postarr = array(
				"ID" => 0,
				"author" => 4,
				"post_title" => $notification_title,
				"post_name" => $notification_slug,
				"post_type" => "notifications",
				"post_status" => "publish",
				"meta_input" => array(
					"notification_name" => $notification_name,
					"notification_url" => $notification_url,
					"notification_text" => $notification_text,
					"notification_icon_code" => $notification_icon_code,
					"notification_icon_background_code" => $notification_color_code,
					"notification_converter" => $notification_parser
				)
			);
			$new_page = wp_insert_post( $postarr );

			return $new_page;
		}
	}

	/*
	*	Function name: get_notification_template
	*	Function arguments: $notification_slug [ STRING ] (reuqired)
	*	Function purpose: This function is used to return custom notificaiton template.
	*/
	function get_notification_template( $notification_slug ) {
		$notification_slug = sanitize_text_field( $notification_slug );

		$args = array(
			"posts_per_page" => 1,
			"post_type" => "notifications",
			"name" => $notification_slug
		);

		$page_ = get_posts( $args );

		if ( isset( $page_ ) && !empty( $page_ ) ) { return $page_[ 0 ]; }
		else { return false; }
	}
}

//Initialize the DB into the framework
$db_brother = new BROTHER;
if ( !$db_brother->is_table_exists( "user_relations" ) ) { $db_brother->create_user_relations(); }
if ( !$db_brother->is_table_exists( "user_notifications" ) ) { $db_brother->create_user_notifications(); }
if ( !$db_brother->is_table_exists( "user_notificationsmeta" ) ) { $db_brother->create_user_notificationsmeta(); }
if ( !$db_brother->is_table_exists( "user_requests" ) ) { $db_brother->create_user_requests(); }
if ( !$db_brother->is_table_exists( "user_likes" ) ) { $db_brother->create_user_likes(); }
if ( !$db_brother->is_table_exists( "story_views" ) ) { $db_brother->create_story_views(); }
?>
