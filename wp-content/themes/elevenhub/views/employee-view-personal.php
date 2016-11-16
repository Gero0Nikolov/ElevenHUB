<?php 
/**
 * View for Employee page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$user_id = get_current_user_id();
$user_first_name = get_user_meta( $user_id, "first_name", true );
$user_last_name = get_user_meta( $user_id, "last_name", true );
$user_background_url = get_user_meta( $user_id, "user_background_url", true );
?>
<div id="user-container" class="user-container-profile" style="background-image: url(<?php echo $user_background_url; ?>);">
	<button class="fa fa-cog invisible-control"></button>
	<div class="white-box">
		<div class="user-information">
			<?php echo get_avatar( $user_id, "128" ); ?>
			<h1 class="user-names"><?php echo $user_first_name ." ". $user_last_name; ?></h1>
		</div>
		<div class="user-meta">
			<h2 id="followers-controller" class="followers">69 followers</h2>
			<button id="compose-controller" class="follow-button">Compose</button>
			<!-- VISITED VIEW
			<button id="follow-controller" class="follow-button">Follow</button>
			<button id="follow-controller" class="unfollow-button">Unfollow</button>
			-->
		</div>
	</div>
</div>