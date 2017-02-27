<?php
/*
*	Converter ID: 324
*	Converter for: Commented story notification: relation_story_comment
*/

$_CONVERTER_URL_ = function( $url_, $notifier_id, $notified_id, $notification_id, $notification_meta = array() ) {
	$story_id = $notification_meta->commented_story_id;
	$comment_id = $notification_meta->comment_id;
	return str_replace( "[single_view_to_comment]", get_permalink( $story_id ) . "?comment_id=". $comment_id, $url_ );
};

$_CONVERTER_TXT_ = function( $text_, $notifier_id, $notified_id, $notificaiton_id, $notification_meta = array() ){
	return str_replace( "[notifier_short_name]", get_user_meta( $notifier_id, "user_shortname", true ) ? get_user_meta( $notifier_id, "user_shortname", true ) : get_user_meta( $notifier_id, "first_name", true ) ." ". get_user_meta( $notifier_id, "last_name", true ), $text_ );
};
?>
