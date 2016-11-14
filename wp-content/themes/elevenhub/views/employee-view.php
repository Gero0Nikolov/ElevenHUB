<?php 
/**
 * View for Employee page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

get_header( "employee" );

$user_id = get_current_user_id();
if ( !is_author( $user_id ) ) { /* LOAD PERSONAL VIEW */ }
else { /* LOAD VISITED USER VIEW */ }
?>