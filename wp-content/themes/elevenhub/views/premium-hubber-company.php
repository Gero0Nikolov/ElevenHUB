<?php
/**
 * View for Premium Hubber - Company
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$page_id = get_the_ID();

$phubber_title = get_field( "title", $page_id );
$phubber_label = get_field( "subscription_label", $page_id );
$phubber_content = get_field( "company_tools", $page_id );
$phubber_price = get_field( "company_price", $page_id );
$phubber_button = get_field( "get_it_button", $page_id );
?>

<div id="phubber-page" class="phubber-page">
	<h1 class="header"><?php echo $phubber_title; ?></h1>
	<h2 class="sub-header"><?php echo $phubber_label; ?></h2>
	<div class="content"><?php echo $phubber_content; ?></div>
	<button id="get-phubber" class="green-bold-button"><?php echo $phubber_button ." ". $phubber_price; ?></button>
</div>
