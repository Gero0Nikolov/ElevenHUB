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
$user_short_name = get_user_meta( $user_id, "user_shortname", true );
$user_biography = get_user_meta( $user_id, "user_biography", true );
?>
<div id="profile-board" class="profile-board">
	<div class="user-board">
		<div id="user-container" class="user-container" style="background-image: url(<?php echo $brother_->get_user_banner_url( $user_id ); ?>);">
			<button class="fa fa-cog invisible-control" onclick="openProfileImages();" title="Change your banner or profile picture"></button>
			<div class="user-information">
				<div id="user-avatar" class="avatar" style="background-image: url('<?php echo $brother_->get_user_avatar_url( $user_id ); ?>');"></div>
			</div>
		</div>
		<h1 class="user-names"><?php echo !empty( $user_short_name ) ? $user_short_name : $user_first_name ." ". $user_last_name ; ?></h1>
		<div class="user-meta">
			<button id="followers-controller" class="follow-button green-bold-button">
				<?php
				$user_followers_num = count( $brother_->get_user_followers( $user_id ) );
				echo $user_followers_num == 1 ? $user_followers_num ." follower" : $user_followers_num ." followers";
				?>
			</button>
			<div class="user-text"><?php echo nl2br( $user_biography ); ?></div>
			<div id="badges" class="badges"></div>
		</div>
	</div>
	<div id="stories-board" class="stories-board">
		<?php $brother_->get_user_board( (object)array( "user_id" => $user_id, "user_compositions" => true ) ); ?>
	</div>
</div>
<script type="text/javascript">
var storiesOffset = 0;
var firstLoad = false;
setTimeout(function(){ pullUserStoriesBoard( "#stories-board", { userID: "", offset: storiesOffset, compositions: false } ); }, 1000 );

var lockStoriesLoad = false;
jQuery( window ).scroll(function(){
	if ( jQuery( window ).scrollTop() + jQuery( window ).height() > jQuery( document ).height() - 100 ) {
		if ( lockStoriesLoad == false && firstLoad == true ) {
			pullUserStoriesBoard( "#stories-board", {
				userID: "",
				offset: storiesOffset,
				compositions: false
			} );
		}
		lockStoriesLoad = true;
	}
});

jQuery( document ).ready(function(){ getUserBadges(); });
</script>
