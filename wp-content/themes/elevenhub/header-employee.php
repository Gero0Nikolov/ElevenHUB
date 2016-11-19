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

$brother_ = new BROTHER;

$mobile_class = "";
if ( wp_is_mobile() ) { $mobile_class = "mobile"; }

$user_id = get_current_user_id();

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>

<script>
	var site_url = '<?php echo get_site_url(); ?>';
	var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
	var admin_post_url = '<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>';
</script>
</head>

<body <?php body_class( $mobile_class ); ?>>
<div id="page" class="site">
	<nav id="site-navigation" class="main-navigation" role="navigation">
		<div class="left-aligned">
			<a href="<?php echo get_author_posts_url( $user_id ); ?>" class='no-border header-avatar-holder'>
				<div id="user-avatar" class="avatar" style="background-image: url('<?php echo $brother_->get_user_avatar_url( $user_id ); ?>');"></div>
			</a>
		</div>
		<div class="right-aligned">
			<?php if ( !wp_is_mobile() ) { wp_nav_menu( array( 'menu_id' => '3' ) ); } else { ?>
			<button id="menu-controller" class="scelleton-icon-button fa fa-bars"></button>
			<?php } ?>
		</div>
	</nav><!-- #site-navigation -->
	<?php if ( wp_is_mobile() ) { ?>
	<div id="mobile-menu-holder" class="animated"><?php wp_nav_menu( array( 'menu_id' => '3' ) ); ?></div>
	<?php } ?>

	<div id="content" class="site-content">
