<?php
/**
 * View for Employee settings page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;

$user_id = get_current_user_id();
$user_first_name = get_user_meta( $user_id, "first_name", true );
$user_last_name = get_user_meta( $user_id, "last_name", true );
$user_notify_over_email = get_user_meta( $user_id, "email_notifications", true );
$user_biography = get_user_meta( $user_id, "user_biography", true );
?>
<div id="user-meta-container">
	<div class="user-meta-container">
		<label for="first-name">First name</label>
		<input id="first-name" type="text" value="<?php echo $user_first_name; ?>" placeholder="First name">
		<label for="last-name">Last name</label>
		<input id="last-name" type="text" value="<?php echo $user_last_name; ?>" placeholder="Last name">
		<label for="password">Password</label>
		<input id="user-password" type="password" autocomplete="off" placeholder="New Password">
		<label for="notify_over_email">Email notification when somethig happens?</label>
		<select id="notify_over_email">
			<option id="true" value="true">Yes</option>
			<option id="false" value="false">No</option>
		</select>
		<label for="biography">About me:</label>
		<textarea id="biography" class="richtext" placeholder="Who are you?"><?php echo $user_biography; ?></textarea>
		<button id="save-user-meta" class="green-bold-button">Save</button>
	</div>
</div>
<script type="text/javascript">
jQuery( document ).ready(function(){
	jQuery( "#notify_over_email #<?php echo $user_notify_over_email; ?>" ).attr( "selected", "selected" );
});
</script>
