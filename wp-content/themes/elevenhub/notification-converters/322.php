<?php
/*
*	Converter ID: 322
*	Converter for: Commented story notification: relation_story_liked
*/

$_CONVERTER_URL_ = function( $url_, $notifier_id, $notified_id, $notification_id, $notification_meta = array() ) {
	$story_id = $notification_meta->liked_story_id;
	return str_replace( "[single_view]", get_permalink( $story_id ), $url_ );
};

$_CONVERTER_TXT_ = function( $text_, $notifier_id, $notified_id, $notificaiton_id, $notification_meta = array() ){
	return str_replace( "[notifier_short_name]", get_user_meta( $notifier_id, "user_shortname", true ) ? get_user_meta( $notifier_id, "user_shortname", true ) : get_user_meta( $notifier_id, "first_name", true ) ." ". get_user_meta( $notifier_id, "last_name", true ), $text_ );
};
?>
