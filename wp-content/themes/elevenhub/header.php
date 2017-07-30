<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package elevenhub
 */

/*** REDIRECTS ***/
$user_id = get_current_user_id();
$user_association = get_user_meta( get_current_user_id(), "account_association", true );

if ( is_user_logged_in() && !is_page( 11 ) && empty( $user_association ) ) { wp_redirect( get_permalink( 11 ), 301 ); }
if ( !is_user_logged_in() && is_page( 11 ) ) { wp_redirect( get_site_url() ); }

if ( !empty( $user_association ) && !is_author( $user_id ) ) { wp_redirect( get_author_posts_url( $user_id ) ); }

$mobile_class = "";
if ( wp_is_mobile() ) { $mobile_class = "mobile"; }

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>

<script>
	var ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body <?php body_class( $mobile_class ); ?>>
<div id="page" class="site">
	<?php if ( !is_page_template( "page-templates/page-join.php" ) ) { ?>
	<nav id="site-navigation" class="main-navigation" role="navigation">
		<?php if ( !is_page_template( "page-templates/employee-company.php" ) ) { ?>
		<div class="left-aligned">
			<a href="<?php echo get_site_url(); ?>" class='no-border'>
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/11hub-logo.png" class="logo hvr-backward" />
			</a>
		</div>
		<div class="right-aligned">
			<button id="login-form-controller" class="red-bold-button">Login</button>
		</div>
		<?php } else { ?>
		<div class="left-aligned">
			<a href="<?php echo get_site_url(); ?>" class='no-border'>
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/11hub-logo.png" class="logo hvr-backward" />
			</a>
		</div>
		<div class="right-aligned">
			<button class='logout-button skeleton-bold-button' onclick="logOutUser();">Logout</button>
		</div>
		<?php } ?>
	</nav><!-- #site-navigation -->
	<?php } ?>

	<div id="content" class="site-content">
