<?php
/**
 * View for Company page personal
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;
$user_id = get_current_user_id();
$user_shortname = get_user_meta( $user_id, "user_shortname", true );
if ( empty( $user_shortname ) || !isset( $user_shortname ) ) {
	$user_first_name = get_user_meta( $user_id, "first_name", true );
	$user_last_name = get_user_meta( $user_id, "last_name", true );
}
?>

<div id="company-container" class="company-container">
	<div id="company-information" class="company-information-container" style="background-image: url(<?php echo $brother_->get_user_banner_url( $user_id ); ?>);">
		<div class="overlay">
			<button class="fa fa-cog invisible-control" onclick="openProfileImages();" title="Change your banner or profile picture"></button>
			<div id="company-logo" class="logo" style="background-image: url('<?php echo $brother_->get_user_avatar_url( $user_id ); ?>');"></div>
			<h1 id="company-brand" class="brand"><?php echo empty( $user_shortname ) || !isset( $user_shortname ) ? $user_first_name ." ". $user_last_name : $user_shortname; ?></h1>
			<div id="company-meta" class="company-meta-container">
				<button id="company-followers-controller" class="meta-button hvr-underline-from-center">
					<?php
					$user_followers_num = count( $brother_->get_user_followers( $user_id ) );
					echo $user_followers_num == 1 ? $user_followers_num ." follower" : $user_followers_num ." followers";
					?>
				</button>
				<span class="bull-separator">&bull;</span>
				<button id="company-employees-controller" class="meta-button hvr-underline-from-center">
					<?php
					$user_employees_num = count( $brother_->get_user_employees( $user_id ) );
					echo $user_employees_num == 1 ? $user_employees_num ." employee" : $user_employees_num ." employees";
					?>
				</button>
			</div>
		</div>
	</div>
	<div id="company-controls" class="company-controls-container">
		<button id="composer-controller" class="green-bold-button">Compose</button>
		<span class="bull-separator">&bull;</span>
		<button id="events-controller" class="orange-bold-button">Events</button>
		<span class="bull-separator">&bull;</span>
		<a href="<?php echo get_permalink( 85 ) ."?company_id=". $user_id; ?>" id="requests-controller" class="banana-bold-button">Requests</a>
		<span class="bull-separator">&bull;</span>
		<a href="<?php echo get_permalink( 98 ) ."?company_id=". $user_id; ?>" id="media-conroller" class="grape-bold-button">Media</a>
	</div>
</div>

<div id="company-story-board" class="stories-container">
	<?php
	$brother_->get_company_stories((object)array(
		"company_id" => $user_id,
		"stories" => 10,
		"status" => "publish"
	));
	?>
</div>

<script type="text/javascript">
initializeStoryControls();
</script>
