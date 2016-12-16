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
		<button id="save-company-meta" class="green-bold-button">Save</button>
	</div>
</div>
