<?php
/**
 * View for Company requests
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;

parse_str( $_SERVER[ "QUERY_STRING" ] );
$user_id = get_current_user_id();

if ( !isset( $company_id ) || empty( $company_id ) ) { $company_id = $user_id; }
?>

<div id="requests-list">
	<?php
	if ( !isset( $request_id ) || empty( $request_id ) ) { $brother_->get_requests( (object)array( "user_id" => $company_id ) ); }
	else {
		$request_ = $brother_->get_request( $request_id );
		$requester_first_name = get_user_meta( $request_->requester_id, "first_name", true );
		$requester_last_name = get_user_meta( $request_->requester_id, "last_name", true );
		$requester_avatar = $brother_->get_user_avatar_url( $request_->requester_id );
		?>

		<div id="request-information">
			<div class="flex-container">
				<div class="requester-information">
					<a href="<?php echo get_author_posts_url( $request_->requester_id ); ?>" class="requester_anchor">
						<div class="avatar" style="background-image: url(<?php echo $requester_avatar; ?>);"></div>
						<div class="names"><?php echo $requester_first_name ." ". $requester_last_name; ?></div>
					</a>
					<div class="bio">
						<h1>About:</h1>
						<div class="text"><?php echo get_user_meta( $request_->requester_id, "user_biography", true ); ?></div>
					</div>
				</div>
				<div class="requester-links">
					<?php if ( !empty( $request_->requester_cv ) ) { ?> <a href="<?php echo $request_->requester_cv; ?>" id="cv-link" class="blue-bold-button display-block mb-1em text-align-center" target="_blank">View CV</a> <?php } ?>
					<?php if ( !empty( $request_->requester_portfolio ) ) { ?> <a href="<?php echo $request_->requester_portfolio; ?>" id="portfolio-link" class="green-bold-button display-block mb-1em text-align-center" target="_blank">View Portfolio</a> <?php } ?>
				</div>
			</div>
			<div class="request-meta">
				<p class="meta">Request data: <?php echo date( "d-m-Y", strtotime( $request_->request_date ) ); ?></p>
				<div class="flex-container">
					<p class="meta">Request answer: <?php echo !empty( $request_->request_response ) ? ucfirst( $request_->request_response ) : "Pending"; ?></p>
					<?php
					if ( empty( $request_->request_response ) ) {
					?>
						<div class="buttons">
							<button id="request-response-controller-accept" class="green-bold-button mr-05em" request-id="<?php echo $request_->id; ?>">Accept</button>
							<button id="request-response-controller-decline" class="red-skeleton-bold-button" request-id="<?php echo $request_->id; ?>">Decline</button>
						</div>
					<?php
					}
					?>
				</div>
			</div>
		</div>

		<?php
	}
	?>
</div>
