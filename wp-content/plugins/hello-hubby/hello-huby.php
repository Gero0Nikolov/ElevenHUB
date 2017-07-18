<?php
/*
*	Plugin name: Hello Hubby for 11hub
*	Description: This plugin is designed specificly for the HUB Core. It will say Hello Hubby to you!
*	Version: 1.0
*	Author: GeroNikolov
*	Author URI: https://geronikolov.com
*	License: PS (CS)
*/

class HELLO_HUBBY {
	function __construct(){
		add_action( "init", array( $this, "say_hello" ) );
	}

	function say_hello() {
		echo "<script type='text/javascript'>console.log( 'Hello Hubby!' );</script>";
	}
}
?>
