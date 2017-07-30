<?php
/**
 * View for Companies List pagetemplate
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;
?>
<div id="companies-list">
	<?php
	$employers_ = $brother_->get_user_employers();
	$follows_ = $brother_->get_user_follows();
	$followed_companies = array();

	foreach ( $follows_ as $followed_user ) {
		$followed_user->user_follow_body = (object)$followd_user->user_follow_body;
		if ( $brother_->is_company( $followed_user->user_follow_body->user_id ) ) {
			array_push( $followed_companies, $followed_user );
		}
	}

	if ( !empty( $employers_ ) || !empty( $followed_companies ) ) {
	?>
	<div id="employers">
		<h1 class="list-title">My companies</h1>
		<?php
		$followed_employers = array();

		if ( count( $employers_ ) > 0 ) {
			foreach ( $employers_ as $employer_user ) {
				$employer_user->employer = (object)$employer_user->employer;
				if ( $brother_->is_company( $employer_user->employer->user_id ) ) {
				?>

				<a href="<?php echo $employer_user->employer->user_url; ?>" id='user-anchor-<?php echo $employer_user->employer->user_id; ?>' class='user-anchor'>
					<div id='user-<?php echo $employer_user->employer->user_id; ?>' class='list-item animated fadeIn' style='background-image: url(<?php echo $employer_user->employer->banner_url; ?>);'>
						<div class='overlay'>
							<div id='user-avatar-<?php echo $employer_user->employer->user_id; ?>' class='avatar' style='background-image: url(<?php echo $employer_user->employer->avatar_url; ?>);'>
							</div>
							<h1 id='user-brand-<?php echo $employer_user->employer->user_id; ?>' class='user-brand'><?php echo !empty( $employer_user->employer->short_name ) ? $employer_user->employer->short_name : $employer_user->employer->first_name ." ". $employer_user->employer->last_name; ?></h1>
							<div id='badges' class='badges'>
								<i class='fa fa-star icon employer' title='Employer'></i>
								<?php if ( $brother_->is_follower( $employer_user->employer->user_id ) ) { array_push( $followed_employers, $employer_user->employer->user_id ); ?> <i class='fa fa-child icon following' title='Following'></i> <?php } ?>
							</div>
						</div>
					</div>
				</a>

				<?php
				}
			}
		}

		foreach ( $followed_companies as $followed_user ) {
			if ( $brother_->is_company( $followed_user->user_follow_body->user_id ) && !in_array( $followed_user->user_follow_body->user_id, $followed_employers ) ) {
				?>

				<a href="<?php echo $followed_user->user_follow_body->user_url; ?>" id='user-anchor-<?php echo $followed_user->user_follow_body->user_id; ?>' class='user-anchor'>
					<div id='user-<?php echo $followed_user->user_follow_body->user_id; ?>' class='list-item animated fadeIn' style='background-image: url(<?php echo $followed_user->user_follow_body->user_banner_url; ?>);'>
						<div class='overlay'>
							<div id='user-avatar-<?php echo $followed_user->user_follow_body->user_id; ?>' class='avatar' style='background-image: url(<?php echo $followed_user->user_follow_body->user_avatar_url; ?>);'>
							</div>
							<h1 id='user-brand-<?php echo $followed_user->user_follow_body->user_id; ?>' class='user-brand'><?php echo !empty( $followed_user->user_follow_body->user_shortname ) ? $followed_user->user_follow_body->user_shortname : $followed_user->user_follow_body->user_first_name ." ". $followed_user->user_follow_body->user_last_name; ?></h1>
							<div id='badges' class='badges'>
								<i class='fa fa-child icon following' title='Following'></i>
							</div>
						</div>
					</div>
				</a>

				<?php
			}
		}
		?>
	</div>
	<?php } ?>
	<h1 class="list-title">All companies</h1>
	<div id="companies">
		<?php $brother_->get_companies( -1 ); ?>
	</div>
</div>
