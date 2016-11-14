<?php
/**
 * The header for Employee view.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package elevenhub
 */

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
</head>

<body <?php body_class( $mobile_class ); ?>>
<div id="page" class="site">
	<nav id="site-navigation" class="main-navigation" role="navigation">
		<div class="left-aligned">
			<a href="<?php echo get_site_url(); ?>" class='no-border'>
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/11hub-logo.png" class="logo hvr-backward" />
			</a>
		</div>
		<div class="right-aligned">
			<button class='logout-button scelleton-bold-button' onclick="logOutUser();">Logout</button>
		</div>
	</nav><!-- #site-navigation -->

	<div id="content" class="site-content">