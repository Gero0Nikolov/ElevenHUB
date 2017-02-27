<?php
/**
 * The header for Company view.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;

/*** Work with the URL ***/
$brother_->catch_url_arguments();


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
	var companyID = "<?php echo get_queried_object_id(); ?>";
</script>
<style>
	html, body { margin: 0 !important; padding: 0 !important; }
</style>
</head>

<body <?php body_class( $mobile_class ); ?>>
<div id="page" class="site">
	<nav id="site-navigation" class="main-navigation" role="navigation">
		<div class="left-aligned width-20p">
			<a href="<?php echo get_author_posts_url( $user_id ); ?>" class='no-border header-avatar-holder'>
				<div id="user-avatar" class="avatar" style="background-image: url('<?php echo $brother_->get_user_avatar_url( $user_id ); ?>');"></div>
			</a>
			<span class="bull-separator">•</span>
			<button id="notifications-controller" class="notifications-controller fa fa-bell">
				<span class="notifications-counter"></span>
			</button>
		</div>
		<div class="right-aligned width-80p">
			<?php if ( !wp_is_mobile() ) { wp_nav_menu( array( 'menu' => '4', 'menu_id' => '4' ) ); } else { ?>
			<button id="menu-controller" class="skeleton-icon-button fa fa-bars"></button>
			<?php } ?>
		</div>
	</nav><!-- #site-navigation -->
	<div id="notifications-holder" class="animated">
	</div>
	<?php if ( wp_is_mobile() ) { ?>
	<div id="mobile-menu-holder" class="animated">
		<?php wp_nav_menu( array( 'menu' => '4', 'menu_id' => '4', 'items_wrap' => '
		<ul id="%1$s" class="%2$s">
			%3$s
			<li class="menu-item menu-item-type-custom menu-item-object-custom"><a rel="logout" href="#!">Logout</a></li>
		</ul>
		' ) ); ?>
	</div>
	<?php } ?>

	<div id="content" class="site-content">
