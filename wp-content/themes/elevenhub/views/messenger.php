<?php
$receiver_id = isset( $_GET[ "u_id" ] ) && !empty( $_GET[ "u_id" ] ) ? sanitize_text_field( $_GET[ "u_id" ] ) : "";
$real_id = strpos( $receiver_id, "_group" ) ? explode( "_", $receiver_id )[ 0 ] : $receiver_id;
$user_id = get_current_user_id();
$brother_ = new BROTHER;

if ( !empty( $receiver_id ) && ( strpos( $receiver_id, "_group" ) || $brother_->is_company( $real_id ) ) ) {
	if ( !$brother_->is_employee( $real_id, $user_id ) ) {
		?>
		<script type="text/javascript">
		window.location = "<?php echo get_site_url() ."/messenger"; ?>";
		</script>
		<?php
	}
}
?>
<script type="text/javascript">
var searchRequestInterval = setTimeout(function(){}, 1000);
var receiver_id = "<?php echo $receiver_id; ?>";
var last_message_id = 0;
var lock_requests = true;
var message_limit = 15;
var message_offset = message_limit;
</script>
<div id="messenger-body">
	<div id="chat-history">
		<input type="text" id="search-controller" class="search-box-flat" placeholder="Search for...">
		<div id="default-container" class="list"></div>
		<div id="search-container" class="list"></div>
	</div>
	<div id="<?php echo !empty( $receiver_id ) ? "receiver-". $receiver_id : "empty-box"; ?>" class="chat-holder">
		<?php
		if ( empty( $receiver_id ) ) {
			?>

			<h1 class="empty-message"><img src="<?php echo get_template_directory_uri(); ?>/assets/fonts/emojies/1f60f.png" class="bounceIn animated" /><span class="animated fadeIn">Choose your partner...</span></h1>

			<?php
		} else {
			$user_info = get_userdata( $real_id  );
			if ( $user_info != false ) {
				$user_avatar_url = $brother_->get_user_avatar_url( $real_id );
				$user_first_name = get_user_meta( $real_id, "first_name", true );
				$user_last_name = get_user_meta( $real_id, "last_name", true );
				$user_short_name = get_user_meta( $real_id, "user_shortname", true );
				$user_url = get_author_posts_url( $real_id );
				?>

				<div class="user-info">
					<a href="<?php echo $user_url; ?>" target="_blank">
						<div class="avatar" style="background-image: url(<?php echo $user_avatar_url; ?>);"></div>
						<span class="names"><?php echo $user_short_name != "" && $user_short_name != false ? $user_short_name : $user_first_name ." ". $user_last_name; echo strpos( $receiver_id, "_group" ) ? " group" : ""; ?></span>
					</a>
				</div>
				<div id="chat-room" class="chat-room">
				</div>
				<div id="messenger-controller-container">
					<input type="text" id="message-container" placeholder="Hookah after work?">
					<button id="emoji-controller" class="controller">
						<div id="emoji-container"></div>
						<img src="<?php echo get_template_directory_uri(); ?>/assets/fonts/emojies/1f60d.png" id="emoji-icon" />
					</button>
					<button id="messenger-controller">Send</button>
				</div>

				<?php
			} else {
				?>

				<h1 class="empty-message"><img src="<?php echo get_template_directory_uri(); ?>/assets/fonts/emojies/1f60f.png" class="bounceIn animated" /><span class="animated fadeIn">Choose your partner...</span></h1>

				<?php
			}
		}
		?>
	</div>
</div>
