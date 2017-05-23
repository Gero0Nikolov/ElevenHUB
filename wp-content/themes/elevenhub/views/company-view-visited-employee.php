<?php
/**
 * View for Company page visited by Employee
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

$company_meta = $brother_->get_company_meta( $v_user_id );
?>

<script type="text/javascript">
var vUserID = "<?php echo $v_user_id; ?>";
var companyID = "<?php echo $v_user_id; ?>";
</script>
<div id="company-container" class="company-container">
	<div id="company-information" class="company-information-container" style="background-image: url(<?php echo $brother_->get_user_banner_url( $v_user_id ); ?>);">
		<div class="overlay">
			<div id="company-logo" class="logo" style="background-image: url('<?php echo $brother_->get_user_avatar_url( $v_user_id ); ?>');"></div>
			<h1 id="company-brand" class="brand"><?php echo empty( $v_user_shortname ) || !isset( $v_user_shortname ) ? $v_user_first_name ." ". $v_user_last_name : $v_user_shortname; ?></h1>
			<div id="company-meta" class="company-meta-container">
				<span class="meta-text">
					<?php
					$v_user_followers_num = count( $brother_->get_user_followers( $v_user_id ) );
					echo $v_user_followers_num == 1 ? $v_user_followers_num ." follower" : $v_user_followers_num ." followers";
					?>
				</span>
				<span class="bull-separator">&bull;</span>
				<span class="meta-text">
					<?php
					$v_user_employees_num = count( $brother_->get_user_employees( $v_user_id ) );
					echo $v_user_employees_num == 1 ? $v_user_employees_num ." employee" : $v_user_employees_num ." employees";
					?>
				</span>
			</div>
		</div>
	</div>
	<div id="company-controls" class="company-controls-container">
        <?php if ( $company_meta->writing_permissions == "everyone" ) { ?>
        <button id="composer-controller" class="green-bold-button">Compose</button>
        <span class="bull-separator">&bull;</span>
        <?php } ?>
        <?php if ( $company_meta->media_uploads_permissions == "everyone" ) { ?>
        <a href="<?php echo get_permalink( 98 ) ."?company_id=". $v_user_id; ?>" id="media-conroller" class="grape-bold-button">Media</a>
		<span class="bull-separator">&bull;</span>
		<?php } ?>
        <button id="leave-company-controller" class="skeleton-bold-button" company="true">Leave</button>
	</div>
</div>

<div id="company-story-board" class="stories-container">
	<?php
	$brother_->get_company_stories((object)array(
		"company_id" => $v_user_id,
		"requester_id" => $user_id,
		"stories" => 5,
		"status" => "publish"
	));
	?>
</div>

<script type="text/javascript">
initializeStoryControls();
</script>
