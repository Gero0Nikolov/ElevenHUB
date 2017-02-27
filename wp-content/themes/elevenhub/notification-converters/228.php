<?php
/*
*	Converter ID: 228
*	Converter for: Company Invite Request notification: relation_company_invite_request
*/

$_CONVERTER_URL_ = function( $url_, $notifier_id, $notified_id, $notification_id, $notification_meta = array() ){
	$request_id = $notification_meta->company_joinrequest_id;
	return str_replace( "[company_request_preview]", get_permalink( 85 ) ."?request_id=". $request_id, $url_ );
};

$_CONVERTER_TXT_ = function( $text_, $notifier_id, $notified_id, $notificaiton_id, $notification_meta = array() ){
	return str_replace( "[notifier_short_name]", get_user_meta( $notifier_id, "user_shortname", true ) ? get_user_meta( $notifier_id, "user_shortname", true ) : get_user_meta( $notifier_id, "first_name", true ) ." ". get_user_meta( $notifier_id, "last_name", true ), $text_ );
};
?>
