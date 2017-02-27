<?php
/*
*	Template name: Main Calendar
*/

$brother_ = new BROTHER;

$user_association = $brother_->get_user_association();
if ( $user_association == "employee" ) {
	get_header( "employee" );
	require_once( plugin_dir_path( __FILE__ ) ."views/employee.php" );
} elseif ( $user_association == "company" ) {
	get_header( "company" );
	require_once( plugin_dir_path( __FILE__ ) ."views/company.php" );
}

?>
