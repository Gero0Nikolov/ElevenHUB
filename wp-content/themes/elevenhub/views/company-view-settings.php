<?php
/**
 * View for Company settings page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;

// User settings
$user_id = get_current_user_id();
$user_first_name = get_user_meta( $user_id, "first_name", true );
$user_last_name = get_user_meta( $user_id, "last_name", true );
$user_short_name = get_user_meta( $user_id, "user_shortname", true ); // A.K.A. Nickname

// Company settings
$company_type = get_user_meta( $user_id, "company_type", true );
$company_writing_permissions = get_user_meta( $user_id, "company_writing_permissions", true );
$company_media_uploads_permissions = get_user_meta( $user_id, "company_media_uploads_permissions", true );
$company_publications_communication_permissions = get_user_meta( $user_id, "company_publications_communication_permissions", true );
$company_notify_over_email = get_user_meta( $user_id, "email_notifications", true );

// Company premium
$company_premium_end_int = get_user_meta( $user_id, "premium_end", true );
$company_premium_end = !empty( $company_premium_end_int ) ? date( "d.m.Y", $company_premium_end_int ) : "";
?>
<div id="user-meta-container">
	<div class="user-meta-container">
		<label for="first-name">First name</label>
		<input id="first-name" type="text" value="<?php echo $user_first_name; ?>" placeholder="First name">
		<label for="last-name">Last name</label>
		<input id="last-name" type="text" value="<?php echo $user_last_name; ?>" placeholder="Last name">
		<label for="short-name">Short (company) name</label>
		<input id="short-name" type="text" value="<?php echo $user_short_name; ?>" placeholder="Short (company) name">
		<label for="user-password">Password</label>
		<input id="user-password" type="password" autocomplete="off" placeholder="New Password">
		<label for="company-type">Company type (public / private)</label>
		<select id="company-type">
			<option id="public" value="public">Public</option>
			<option id="private" value="private">Private</option>
		</select>
		<label for="company-writing-permissions">Publishing options (only me / everyone)</label>
		<select id="company-writing-permissions">
			<option id="only-me" value="only-me">Only me</option>
			<option id="everyone" value="everyone">Everyone</option>
		</select>
		<label for="company-publications-communication-permissions">Publications comments</label>
		<select id="company-publications-communication-permissions">
			<option id="allow" value="allow">Allow</option>
			<option id="disable" value="disable">Disable</option>
		</select>
		<label for="company-media-uploads-permissions">Media uploads options (only me / everyone)</label>
		<select id="company-media-uploads-permissions">
			<option id="only-me" value="only-me">Only me</option>
			<option id="everyone" value="everyone">Everyone</option>
		</select>
		<label for="notify_over_email">Email notification when somethig happens?</label>
		<select id="notify_over_email">
			<option id="true" value="true">Yes</option>
			<option id="false" value="false">No</option>
		</select>
		<div id="premium-container" class="premium-container">
			<p class="text">
				<?php
				if ( !empty( $company_premium_end ) ) { echo "Your premium will be active untill: ". $company_premium_end; }
				else { echo "Your premium is not active yet"; }
				?>
			</p>
			<a href="<?php echo get_permalink( 667 ); ?>" class="blue-bold-button">
				<?php
				if ( !empty( $company_premium_end ) ) { echo "Renew now"; }
				else { echo "Activate it"; }
				?>
			</a>
		</div>
		<button id="save-company-meta" class="green-bold-button">Save</button>
	</div>
</div>

<script type="text/javascript">
jQuery( document ).ready(function(){

	jQuery( "#company-type #<?php echo $company_type; ?>" ).attr( "selected", "selected" );
	jQuery( "#company-writing-permissions #<?php echo $company_writing_permissions; ?>" ).attr( "selected", "selected" );
	jQuery( "#company-publications-communication-permissions #<?php echo $company_publications_communication_permissions; ?>" ).attr( "selected", "selected" );
	jQuery( "#company-media-uploads-permissions #<?php echo $company_media_uploads_permissions; ?>" ).attr( "selected", "selected" );
	jQuery( "#notify_over_email #<?php echo $company_notify_over_email; ?>" ).attr( "selected", "selected" );

});
</script>
