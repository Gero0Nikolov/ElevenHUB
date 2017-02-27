<?php
/*
*	Plugin name: Calendar by 11hub
*	Description: This plugin is designed specificly for the HUB Core. It gives the advanced Calendar tool to the users of the core.
*	Version: 1.0
*	Author: GeroNikolov
*	Author URI: https://geronikolov.com
*	License: PS (CS)
*/

class HUB_CALENDAR {
	function __construct() {
		// Add the Calendar menu option for the Employee && the Company views
		add_filter( "wp_nav_menu_items", array( $this, "add_calendar_menu_item" ), 10, 2 );

		// Add Events CPT
		add_action( "init", array( $this, "events_cpt" ) );

		// Add Add Event page
		add_action( "init", array( $this, "register_add_event_page" ) );

		// Add Events archive page tempalte
		add_filter( "archive_template", array( $this, "events_archive_page" ) );

		// Add scripts and styles for the Front-end part only when Calendar is used
		add_action( 'wp_enqueue_scripts', array( $this, 'add_front_JS' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_front_CSS' ) );
	}

	// Register Front JS
	function add_front_JS() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( '11h-calendar-body', plugins_url( '/assets/scripts/body.js' , __FILE__ ), array(), '1.0' );
		wp_enqueue_script( '11h-calendar-front-js', plugins_url( '/assets/scripts/front.js' , __FILE__ ), array(), '1.0' );
	}

	// Register Front CSS
	function add_front_CSS() {
		wp_enqueue_style( '11h-calendar-front-css', plugins_url( '/assets/css/front.css', __FILE__ ), array(), '1.0', 'screen' );
	}

	/*
	*	Function name: add_calendar_menu_item
	*	Function arguments: $items [ STRING ] the items of the currently displayed menu; $menu [ MIXED_OBJECT ] the currently displayed menu sdkClass;
	*	Function purpose: This function prepend the Calendar menu option for the Employee && the Company header views.
	*/
	function add_calendar_menu_item( $items, $menu ) {
		if ( $menu->menu_id == 3 || $menu->menu_id == 4 ) {
			$items = "<li id='11h-calendar-anchor' class='menu-item calendar-anchor'><a href='". get_post_type_archive_link( "events" ) ."'>Calendar</a></li>". $items;
		}
		return $items;
	}

	/*
	*	Function name: events_cpt
	*	Function arguments: NONE
	*	Function purpose: This function registers a new CPT called Events used by the Calendar to generate new events.
	*/
	function events_cpt() {
		register_post_type(
			"events",
			array(
				"labels" => array(
					"name" => __( "Events" ),
					"singular_name" => __( "Event" )
				),
				"supports" => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields' ),
				"hierarchical" => false,
				"public" => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'show_in_nav_menus' => true,
				'show_in_admin_bar' => true,
				'menu_position' => 5,
				'can_export' => true,
				'has_archive' => true,
				'exclude_from_search' => false,
				'publicly_queryable' => true,
				'capability_type' => 'page',
				'rewrite' => array( "slug" => "events", "with_front" => false )
			)
		);
	}

	/*
	*	Function name: register_add_event_page
	*	Function arguments: NONE
	*	Function purpose: This function is used to create Add Event - 11h Calendar page, used to create new events.
	*/
	function register_add_event_page() {
		$brother_ = new BROTHER;
		$page_id = $brother_->register_plugin_page( "11h-calendar", "Add Event - 11h Calendar", "11h-add-event-calendar" );
	}

	/*
	*	Function name: events_archive_page
	*	Function arguments: $template [ STRING ]
	*	Function purpose: This function loads the specific view for the events archive page.
	*/
	function events_archive_page( $template ) {
		global $wp_query;
		if ( is_post_type_archive( "events" ) ) {
			$template = plugin_dir_path( __FILE__ ) ."templates/archive-events.php";
		}
		return $template;
	}

	function get_upcoming_events( $user_id = "", $offset = 0 ) {
		$args = array(
			"posts_per_page" => 10,
			"post_type" => "events",
			"orderby" => "date",
			"order" => "ASC",
			"author" => $user_id,
			"post_status" => "publish",
			"offset" => $offset,
			"meta_query" => array(
				"relation" => "AND",
				array(
					"key" => "start_date",
					"value" => strtotime( "now" ),
					"compare" => ">="
				),
				array(
					"key" => "end_date",
					"value" => strtotime( "now" ),
					"compare" => "<"
				)
			)
		);
		$events_ = get_posts( $args );

		return $events_;
	}

	function get_past_events( $user_id = "", $offset = 0 ) {
		$args = array(
			"posts_per_page" => 10,
			"post_type" => "events",
			"orderby" => "date",
			"order" => "ASC",
			"author" => $user_id,
			"post_status" => "publish",
			"offset" => $offset,
			"meta_key" => "end_date",
			"meta_value" => strtotime( "now" ),
			"meta_compare" => ">="
		);
		$events_ = get_posts( $args );

		return $events_;
	}
}

$_CALENDAR_11_HUB = new HUB_CALENDAR;
?>
