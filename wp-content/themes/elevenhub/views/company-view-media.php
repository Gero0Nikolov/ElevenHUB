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

parse_str( $_SERVER[ "QUERY_STRING" ] ); // Convert company_id from the URL to real $company_id

if ( isset( $company_id ) && !empty( $company_id ) && ( $user_id == $company_id || $brother_->is_employee( $company_id, $user_id ) ) ) {
	$user_id = get_current_user_id();
	$available_space = $brother_->convert_bytes( $brother_->get_available_media_space( $company_id ) );
	?>

	<script type="text/javascript">
		var selectedElements = [];
		var mediaOffset = 20;
		companyID = "<?php echo $company_id; ?>";
	</script>
	<div id='media-controls' class='simple-controls mt-1em mb-1em'>
		<p href="#" id="space-pointer" class="just-text green"><span class="tiny-text">Available Space:</span><span id="space"><?php echo $available_space; ?></space>MB</p>
		<button id="media-uploader-opener" class="blue-bold-button">Upload</button>
		<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="media-uploader" class="hidden" method='POST' enctype='multipart/form-data'>
			<input type="file" name="upload[]" id="file-holder" multiple="multiple">
			<input type='hidden' name='action' value='upload_user_media_files'>
			<input type="hidden" name="company_id" value="<?php echo $company_id; ?>">
		</form>
	</div>
	<div id="medias-container">
		<?php $brother_->get_user_media( (object)array( "user_id" => $company_id, "is_ajax" => false ) ); ?>
	</div>
	<!-- <button id="load-more-controller" class="blue-skeleton-bold-button display-block mh-auto mt-1em">Load more</button> -->
	<?php
}
?>
