<?php
/**

*	Template Name: Join Page

*	@package eleven hub

*/

$page_id = get_the_ID();
$page_ = get_post( $page_id );
$logo_ = get_field( "logo", $page_id );

get_header();
?>

<div id="login-form-holder">
	<img class="logo-container" src="<?php echo $logo_; ?>" />
	<h1 class="page-title"><?php echo $page_->post_title; ?></h1>
	<h2 class="page-quote"><?php echo $page_->post_content; ?></h2>
	<div id="register">
		<input type='text' placeholder='First name' id='first-name' class='small-fat' onkeydown='keyPressedForms(event, 1);'>
		<input type='text' placeholder='Last name' id='last-name' class='small-fat' onkeydown='keyPressedForms(event, 1);'>
		<input type='email' placeholder='Email' id='email-registration' class='wide-fat' onkeydown='keyPressedForms(event, 1);'>
		<input type='password' placeholder='Password' id='password-registration' class='wide-fat' onkeydown='keyPressedForms(event, 1);'>
		<div id='login-captcha' class='g-recaptcha' data-sitekey='6LcbrioUAAAAAEgA9LAbeaK_TQHOKVWhd8QZeXrI'></div>
		<button id='register-controller' class='green-bold-button form-button' onclick='registerUser();' onkeydown='keyPressedForms(event, 1);'>Register</button>
		<a href="<?php echo get_site_url(); ?>/#login" class="simple-link">I have login</a>
	</div>
</div>
