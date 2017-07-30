<?php
/*
*	Example Plugin Player!
*	Player idea.
*	When the player is called it has to INIT the plugin in the HUB Project core and
*	present the plugin to the WordPress core ACTIONS && HOOKS.
*	Once the plugin is initialized it has to return the plugin INIT Class to the caller.
*/

$_PLAYER_ = function(){
	$hello_hubby = new HELLO_HUBBY;
	return $hello_hubby;
}

?>
