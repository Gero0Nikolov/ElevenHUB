<?php
/**
 * View for Employee page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

 $brother_ = new BROTHER;

$user_id = get_current_user_id();
$user_first_name = get_user_meta( $user_id, "first_name", true );
$user_last_name = get_user_meta( $user_id, "last_name", true );
?>
<div id="user-container" class="user-container-profile" style="background-image: url(<?php echo $brother_->get_user_banner_url( $user_id ); ?>);">
	<button class="fa fa-cog invisible-control" onclick="openProfileImages();" title="Change your banner or profile picture"></button>
	<div class="white-box">
		<div class="user-information">
			<div id="user-avatar" class="avatar" style="background-image: url('<?php echo $brother_->get_user_avatar_url( $user_id ); ?>');"></div>
			<h1 class="user-names"><?php echo $user_first_name ." ". $user_last_name; ?></h1>
		</div>
		<div class="user-meta">
			<h2 id="followers-controller" class="followers">
			<?php
			$user_followers_num = count( $brother_->get_user_followers( $user_id ) );
			echo $user_followers_num == 1 ? $user_followers_num ." follower" : $user_followers_num ." followers";
			?>
			</h2>
			<button id="compose-controller" class="follow-button">Compose</button>
			<!-- VISITED VIEW
			<button id="follow-controller" class="follow-button">Follow</button>
			<button id="follow-controller" class="unfollow-button">Unfollow</button>
			-->
		</div>
	</div>
</div>
