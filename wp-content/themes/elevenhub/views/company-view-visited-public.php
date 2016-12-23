<?php
/**
 * View for Company page personal
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;

$v_user_id = get_queried_object_id();

$v_user_shortname = get_user_meta( $v_user_id, "user_shortname", true );
if ( empty( $v_user_shortname ) || !isset( $v_user_shortname ) ) {
	$v_user_first_name = get_user_meta( $v_user_id, "first_name", true );
	$v_user_last_name = get_user_meta( $v_user_id, "last_name", true );
}
?>

<script type="text/javascript">var vUserID = "<?php echo $v_user_id; ?>";</script>
<div id="company-container" class="company-container">
	<div id="company-information" class="company-information-container" style="background-image: url(<?php echo $brother_->get_user_banner_url( $v_user_id ); ?>);">
		<div class="overlay">
			<div id="company-logo" class="logo" style="background-image: url('<?php echo $brother_->get_user_avatar_url( $v_user_id ); ?>');"></div>
			<h1 id="company-brand" class="brand"><?php echo empty( $v_user_shortname ) || !isset( $v_user_shortname ) ? $v_user_first_name ." ". $v_user_last_name : $v_user_shortname; ?></h1>
			<div id="company-meta" class="company-meta-container">
				<span id="company-followers-controller" class="meta-text">
					<?php
					$v_user_followers_num = count( $brother_->get_user_followers( $v_user_id ) );
					echo $v_user_followers_num == 1 ? $v_user_followers_num ." follower" : $v_user_followers_num ." followers";
					?>
				</span>
				<span class="bull-separator">&bull;</span>
				<span id="company-employees-controller" class="meta-text">
					<?php
					$v_user_employees_num = count( $brother_->get_user_employees( $v_user_id ) );
					echo $v_user_employees_num == 1 ? $v_user_employees_num ." employee" : $v_user_employees_num ." employees";
					?>
				</span>
			</div>
		</div>
	</div>
	<div id="company-controls" class="company-controls-container">
		<?php if ( $brother_->is_follower( $v_user_id ) ) { ?>
		<button id="follow-controller" class="skeleton-bold-button" company="true">Unfollow</button>
		<?php } else { ?>
		<button id="follow-controller" class="green-bold-button" company="true">Follow</button>
		<?php } ?>
		<span class="bull-separator">&bull;</span>
		<button id="join-controller" class="orange-bold-button" company="true">Join</button>
	</div>
</div>

<div id="story-board" class="stories-container">
</div>
