<?php
/**
 * View for Employee page visited
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;

$v_user_id = get_queried_object_id();
$v_user_first_name = get_user_meta( $v_user_id, "first_name", true );
$v_user_last_name = get_user_meta( $v_user_id, "last_name", true );
$v_user_short_name = get_user_meta( $v_user_id, "user_shortname", true );
$v_user_biography = get_user_meta( $v_user_id, "user_biography", true );
?>

<script type="text/javascript">var vUserID = "<?php echo $v_user_id; ?>";</script>
<div id="profile-board" class="profile-board">
	<div class="user-board">
		<div id="user-container" class="user-container" style="background-image: url(<?php echo $brother_->get_user_banner_url( $v_user_id ); ?>);">
			<div class="user-information">
				<div id="user-avatar" class="avatar" style="background-image: url('<?php echo $brother_->get_user_avatar_url( $v_user_id ); ?>');"></div>
			</div>
		</div>
		<h1 class="user-names"><?php echo !empty( $v_user_short_name ) ? $v_user_short_name : $v_user_first_name ." ". $v_user_last_name ; ?></h1>
		<div class="user-meta">
			<?php
			if ( get_user_meta( get_current_user_id(), "account_association", true ) != "company" ) {
			?>
			<button id="follow-controller" class="<?php echo $brother_->is_follower( $v_user_id ) ? "unfollow" : "follow"; ?>-button mb-05em"><?php echo $brother_->is_follower( $v_user_id ) ? "Unfollow" : "Follow"; ?></button>
			<?php
			} else {
				if ( !$brother_->is_employee( get_current_user_id(), $v_user_id ) ) {
					?>
					<button id="invite-to-company-controller" class="green-bold-button mb-05em">Invite</button>
					<?php
				}
			}
			?>
			<div id="followers" class="meta">
				<i class="fa fa-globe icon belize-hole"></i>
				<?php
				$v_user_followers_num = count( $brother_->get_user_followers( $v_user_id ) );
				echo $v_user_followers_num == 1 ? $v_user_followers_num ." follower" : $v_user_followers_num ." followers";
				?>
			</div>
			<?php
			$mutual_company = $brother_->is_colleges( $v_user_id );
			if ( !is_bool( $mutual_company ) ) {
			?>
			<div id="mutual-company" class="meta">
				<i class="fa fa-hand-peace-o icon emerald"></i>
				You are colleges at <div class="meta-avatar" style="background-image: url(<?php echo $mutual_company->employer->avatar_url; ?>);"></div>
			</div>
			<?php }

			if ( $brother_->is_employee( get_current_user_id(), $v_user_id ) ) {
			?>
			<div id="employee" class="meta">
				<i class="fa fa-star icon sun-flower"></i>
				<?php echo !empty( $v_user_short_name ) ? $v_user_short_name : $v_user_first_name; ?> is your employee
			</div>
			<?php } ?>
			<div class="user-text"><?php echo nl2br( $v_user_biography ); ?></div>
		</div>
	</div>
	<div id="stories-board" class="stories-board">
		<?php $brother_->get_user_board( (object)array( "user_id" => $v_user_id, "user_compositions" => true ) ); ?>
	</div>
</div>
<script type="text/javascript">
var storiesOffset = 0;
var firstLoad = false;
setTimeout(function(){ pullUserStoriesBoard( "#stories-board", { userID: vUserID, offset: storiesOffset, compositions: false } ); }, 1000 );

var lockStoriesLoad = false;
jQuery( window ).scroll(function(){
if ( jQuery( window ).scrollTop() + jQuery( window ).height() > jQuery( document ).height() - 100 ) {
	if ( lockStoriesLoad == false && firstLoad == true ) {
		pullUserStoriesBoard( "#stories-board", {
			userID: vUserID,
			offset: storiesOffset,
			compositions: false
		} );
	}
	lockStoriesLoad = true;
}
});
</script>
