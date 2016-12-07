<?php
/**
 * View for Company page personal
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;


parse_str( $_SERVER[ "QUERY_STRING" ] ); // Convert company_id from the URL to real $company_id
$user_id = get_current_user_id();

$available_space = $brother_->convert_bytes( $brother_->get_available_media_space( $company_id ) );
var_dump( $available_space );
?>
