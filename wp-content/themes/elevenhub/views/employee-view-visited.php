<?php
/**
 * View for Employee page visited
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;

$v_user = get_queried_object();
$v_user_id = $v_user->ID;

$v_user_first_name = get_user_meta( $v_user_id, "first_name", true );
$v_user_last_name = get_user_meta( $v_user_id, "last_name", true );
$v_user_biography = get_user_meta( $v_user_id, "user_biography", true );
?>
<script type="text/javascript">var vUserID = "<?php echo $v_user_id; ?>";</script>

<div id="user-container" class="user-container-profile visited" style="background-image: url(<?php echo $brother_->get_user_banner_url( $v_user_id ); ?>);">
	<div class="white-box">
		<div class="user-information">
			<div id="user-avatar" class="avatar" style="background-image: url('<?php echo $brother_->get_user_avatar_url( $v_user_id ); ?>');"></div>
			<h1 class="user-names"><?php echo $v_user_first_name ." ". $v_user_last_name; ?></h1>
		</div>
		<div class="user-meta">
			<h2 class="followers">
			<?php
			$v_user_followers_num = count( $brother_->get_user_followers( $v_user_id ) );
			echo $v_user_followers_num == 1 ? $v_user_followers_num ." follower" : $v_user_followers_num ." followers";
			?>
			</h2>
			<?php			
			if ( get_user_meta( get_current_user_id(), "account_association", true ) != "company" ) {
				if ( $brother_->is_follower( $v_user_id ) ) {
					?>
					<button id="follow-controller" class="unfollow-button">Unfollow</button>
					<?php
				} else {
					?>
					<button id="follow-controller" class="follow-button">Follow</button>
					<?php
				}
			}
			?>
		</div>
	</div>
</div>

<?php if ( !empty( $v_user_biography ) ) { ?>
<div id="user-bio-container" class='user-bio-container'>
	<div id="meta-header" class="meta-header">
		<h1>About me:</h1>
	</div>
	<div id="meta-content" class="meta-content">
		<?php echo $v_user_biography; ?>
	</div>
</div>
<?php } ?>

<div id="stories-container" class="posts-container">
</div>
