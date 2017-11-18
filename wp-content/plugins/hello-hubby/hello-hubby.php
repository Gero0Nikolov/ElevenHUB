<?php
/*
*	Plugin name: Hello Hubby
*	Description: This plugin is designed specificly for the HUB Core. It will say Hello Hubby to you!
*	Version: 1.0
*	Author: GeroNikolov
*	Author URI: https://geronikolov.com
*	License: PS (CS)
*	Visibility: Personal
*	UserID: 1
*	Call hooks: OPVe, OPVc
*/

class HELLO_HUBBY {
	function __construct(){
		add_action( "wp_head", array( $this, "say_hello" ) );
	}

	function say_hello() {
		if ( !wp_is_mobile() ) {
			$user_id = get_current_user_id();
			$short_name = get_user_meta( $user_id, "user_shortname", true );
			if ( empty( $short_name ) || !$short_name ) { $first_name = get_user_meta( $user_id, "first_name", true ); }

			$name_ = empty( $short_name ) || !$short_name ? $first_name : $short_name;

			echo "
			<script type='text/javascript'>
				jQuery( document ).ready( function(){
					view_ = \"\
					<span class='bull-separator'>&bull;</span>\
					<div class='hubby-container'>\
						<img class='hubby' src='". get_template_directory_uri() ."/assets/fonts/emojies-all/1f917.png' />\
						<div class='hubby-box'>Hello, $name_</div>\
					</div>\
					\";
					jQuery( '#site-navigation .left-aligned' ).append( view_ );
				} );
			</script>
			<style>
			.hubby {
				display: inline-block;
				width: 100%;
				vertical-align: middle;
				transform: translateZ(0);
				webkit-transition: all 0.25s ease-in-out;
				-moz-transition: all 0.25s ease-in-out;
				-o-transition: all 0.25s ease-in-out;
				transition: all 0.25s ease-in-out;
			}
			.hubby:hover {
				transform: translateZ(0) scale(1.5);
			}

			.hubby-container {
				display: inline-block;
				vertical-align: middle;
				width: 32px;
				position: relative;
			}
			.hubby-container:hover .hubby-box { opacity: 1; }

			.hubby-box {
				position: absolute;
				background: rgba(0, 0, 0, 0.85);
				right: 0;
				bottom: 0;
				transform: translateY(145%);
				font-family: PlayFairRegular;
				font-size: 1rem;
				font-weight: normal;
				color: #fff;
				padding: 0.25em 0.75em;
				border-radius: 5px;
				opacity: 0;
				min-width: 120px;
				text-align: center;
				webkit-transition: all 0.25s ease-in-out;
				-moz-transition: all 0.25s ease-in-out;
				-o-transition: all 0.25s ease-in-out;
				transition: all 0.25s ease-in-out;
			}
			.hubby-box:after {
				bottom: 100%;
				right: 7.5px;
				border: solid transparent;
				content: ' ';
				height: 0;
				width: 0;
				position: absolute;
				pointer-events: none;
				border-color: rgba(136, 183, 213, 0);
				border-bottom-color: rgba(0, 0, 0, 0.85);
				border-width: 10px;
				margin-left: -10px;
			}
			</style>
			";
		}
	}
}
?>
