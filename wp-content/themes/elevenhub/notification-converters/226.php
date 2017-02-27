<?php
/*
*	Converter ID: 226
*	Converter for: Company Join Declined notification: relation_company_join_declined
*/

$_CONVERTER_URL_ = function( $url_, $notifier_id, $notified_id, $notification_id, $notification_meta = array() ){
	return str_replace( "[notified_archive_page]", get_author_posts_url( $notified_id ), $url_ );
};

$_CONVERTER_TXT_ = function( $text_, $notifier_id, $notified_id, $notificaiton_id, $notification_meta = array() ){
	return str_replace( "[notifier_short_name]", get_user_meta( $notifier_id, "user_shortname", true ) ? get_user_meta( $notifier_id, "user_shortname", true ) : get_user_meta( $notifier_id, "first_name", true ) ." ". get_user_meta( $notifier_id, "last_name", true ), $text_ );
};
?>
