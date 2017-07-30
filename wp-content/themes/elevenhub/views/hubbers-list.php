<?php
/**
 * View for Hubbers List pagetemplate
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;

$user_id = get_current_user_id();
$user_association = get_user_meta( $user_id, "account_association", true );
?>
<script type="text/javascript">
	var usersOffset = 100;
</script>
<div id="hubbers-list">
	<?php
	if ( $user_association == "employee" ) {
		$follows_ = $brother_->get_user_follows();
		if ( count( $follows_ ) > 0 ) {
		?>
		<h1 class="list-title">Following</h1>
		<div id="follows">
			<?php
			foreach ( $follows_ as $followed_user ) {
				$followed_user->user_follow_body = (object)$followed_user->user_follow_body;
				if ( !$brother_->is_company( $followed_user->user_follow_body->user_id ) ) {
				?>

				<a href="<?php echo $followed_user->user_follow_body->user_url; ?>" id='user-anchor-<?php echo $followed_user->user_follow_body->user_id; ?>' class='user-anchor'>
					<div id='user-<?php echo $followed_user->user_follow_body->user_id; ?>' class='list-item animated fadeIn' style='background-image: url(<?php echo $followed_user->user_follow_body->user_banner_url; ?>);'>
						<div class='overlay'>
							<div id='user-avatar-<?php echo $followed_user->user_follow_body->user_id; ?>' class='avatar' style='background-image: url(<?php echo $followed_user->user_follow_body->user_avatar_url; ?>);'>
							</div>
							<h1 id='user-brand-<?php echo $followed_user->user_follow_body->user_id; ?>' class='user-brand'><?php echo !empty( $followed_user->user_follow_body->user_shortname ) ? $followed_user->user_follow_body->user_shortname : $followed_user->user_follow_body->user_first_name ." ". $followed_user->user_follow_body->user_last_name; ?></h1>
						</div>
					</div>
				</a>

				<?php
				}
			}
			?>
		</div>
		<?php
		}
	} elseif ( $user_association == "company" ) {
		$employees_ = $brother_->get_user_employees();
		if ( count( $employees_ ) > 0 ) {
			?>

			<h1 class="list-title">My employees</h1>
			<div id="employees">
				<?php
				foreach ( $employees_ as $employed_user ) {
					$employed_user->user_employee_body = (object)$employed_user->user_employee_body;
					if ( !$brother_->is_company( $employed_user->user_employee_body->user_id ) ) {
					?>

					<a href="<?php echo $employed_user->user_employee_body->user_url; ?>" id='user-anchor-<?php echo $employed_user->user_employee_body->user_id; ?>' class='user-anchor'>
						<div id='user-<?php echo $employed_user->user_employee_body->user_id; ?>' class='list-item animated fadeIn' style='background-image: url(<?php echo $employed_user->user_employee_body->user_banner_url; ?>);'>
							<div class='overlay'>
								<div id='user-avatar-<?php echo $employed_user->user_employee_body->user_id; ?>' class='avatar' style='background-image: url(<?php echo $employed_user->user_employee_body->user_avatar_url; ?>);'>
								</div>
								<h1 id='user-brand-<?php echo $employed_user->user_employee_body->user_id; ?>' class='user-brand'><?php echo !empty( $employed_user->user_employee_body->user_shortname ) ? $employed_user->user_employee_body->user_shortname : $employed_user->user_employee_body->user_first_name ." ". $employed_user->user_employee_body->user_last_name; ?></h1>
							</div>
						</div>
					</a>

					<?php
					}
				}
				?>
			</div>

			<?php
		}
	}
	?>
	<h1 class="list-title">All</h1>
	<div id="hubbers">
		<?php $brother_->get_hubbers( (object)array( "number" => -1 ) ); ?>
	</div>
</div>
