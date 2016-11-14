<?php
/**
 * elevenhub functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package elevenhub
 */

if ( ! function_exists( 'elevenhub_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function elevenhub_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on elevenhub, use a find and replace
	 * to change 'elevenhub' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'elevenhub', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'elevenhub' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'elevenhub_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif;
add_action( 'after_setup_theme', 'elevenhub_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function elevenhub_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'elevenhub_content_width', 640 );
}
add_action( 'after_setup_theme', 'elevenhub_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function elevenhub_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'elevenhub' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'elevenhub' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'elevenhub_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function elevenhub_scripts() {
	wp_enqueue_style( 'elevenhub-animations', get_template_directory_uri() . '/animate.css' );
	wp_enqueue_style( 'elevenhub-font-awesome', get_template_directory_uri() . '/inc/font-awesome/css/font-awesome.css' );
	wp_enqueue_style( 'the_travel_kiwi-fonts', get_template_directory_uri() . '/fonts.css' );
	wp_enqueue_style( 'elevenhub-style', get_stylesheet_uri() );


	wp_enqueue_script( 'dloober-initial', get_template_directory_uri() . '/js/initial.js', array( 'jquery' ), '', false );
	wp_enqueue_script( 'elevenhub-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'elevenhub_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/** IS ALPHABETICAL **/
function is_alphabetical( $args ) {
	foreach ( $args as $input ) {
		if ( !( $response = ctype_alpha( $input ) ? true : false ) ) { break; }
	}
	return $response;
}

/** Disable access for non site administrators **/
function disable_access() { if ( !current_user_can('administrator') && !is_admin() ) { wp_redirect( get_site_url() ); } }
add_action( 'wp_login', 'disable_access' );

function remove_admin_bar() { if ( !current_user_can('administrator') && !is_admin() ) { show_admin_bar( false ); } }
add_action('after_setup_theme', 'remove_admin_bar');

/** AJAX **/

/*
* 	TO DO:
*	- Allow only site admins to see the WP Dashboard
* 	- Add user metas:
*		- Association: Employee / Company
* 	- On login user wp_signon() to login user
* 	- On logout use wp_logout() to logout user
*/

add_action( 'wp_ajax_nopriv_register_user', 'register_user' );
add_action( 'wp_ajax_register_user', 'register_user' );
function register_user() {
	$first_name = $_POST[ "first_name" ];
	$last_name = $_POST[ "last_name" ];
	$email = $_POST[ "email" ];
	$password = $_POST[ "password" ];

	$wp_username = strtolower( $first_name ."_". $last_name );

	if ( empty( $email ) || !is_email( $email ) ) { echo "Choose your email!"; die(); }
	if ( empty( $password ) ) { echo "Choose your password!"; die(); }
	if ( !is_alphabetical( array( $first_name, $last_name ) ) ) { echo "Enter your real names!"; die(); }

	$wp_registration_result = wp_create_user( $wp_username, $password, $email );

	if ( is_wp_error( $wp_registration_result ) || !is_alphabetical( array( $first_name, $last_name ) ) ) {
		if ( !empty( $wp_registration_result->errors[ "existing_user_login" ] ) && !email_exists( $email ) ) {
			while ( !empty( $wp_registration_result->errors[ "existing_user_login" ] ) ) { $wp_registration_result = wp_create_user( $wp_username . mt_rand( 100000, 999999 ), $password, $email ); }
	 	} else {
			echo $response = ( email_exists( $email ) ? "This email is already in use!" : ( !is_alphabetical( array( $first_name, $last_name ) ) ? "Use only alphabetical characters in your name!" : ( empty( $password ) ? "Choose your password!" : "Something wrong happens here!" ) ) );
			die();
		}
	} else {
		// Update the new user
		$args = array(
			"ID" => $wp_registration_result,
			"first_name" => $first_name,
			"last_name" => $last_name,
			"role" => "Subscriber"
		);
		$wp_update_result = wp_update_user( $args );

		// Prepare Hello mail
		$subject = "Welcome to 11hub!";
		$content = "Welcome onboard!\n\nWe hope to see you <a href='". get_site_url() ."' target='_blank' style='color: #3498db; text-decoration: underline;'>hubbing soon</a>!\n\nCheers!";
		wp_mail( $email, $subject, $content );
	}


	die();
}


add_action( 'wp_ajax_nopriv_login_user', 'login_user' );
add_action( 'wp_ajax_login_user', 'login_user' );
function login_user() {	
	$email = $_POST[ "email" ];
	$password = $_POST[ "password" ];

	$creds['user_login'] = $email;
	$creds['user_password'] = $password;
	$creds['remember'] = false;

	if ( empty( $email ) ) { echo "Enter your email!"; die(); }
	if ( empty( $password ) ) { echo "Enter your password!"; die(); }

	$user_ = wp_signon( $creds, false );
	if ( is_wp_error( $user_ ) ) { echo "Your email or password is wrong!";	}	

	die();
}

add_action( 'wp_ajax_nopriv_logout_user', 'logout_user' );
add_action( 'wp_ajax_logout_user', 'logout_user' );
function logout_user() {	
	wp_logout();
	die();
}

add_action( 'wp_ajax_nopriv_add_profile_association', 'add_profile_association' );
add_action( 'wp_ajax_add_profile_association', 'add_profile_association' );
function add_profile_association() {	
	$association_type = strtolower( trim( $_POST[ "type" ] ) );
	if ( !empty( $association_type ) ) { add_user_meta( get_current_user_id(), "account_association", $association_type, false ); }

	die();
}