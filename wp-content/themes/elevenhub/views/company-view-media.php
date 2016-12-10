<?php
/**
 * View for Company page personal
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;

parse_str( $_SERVER[ "QUERY_STRING" ] ); // Convert company_id from the URL to real $company_id
$user_id = get_current_user_id();

$available_space = $brother_->convert_bytes( $brother_->get_available_media_space( $company_id ) );
?>
<script type="text/javascript">
	var companyID = "<?php echo !empty( $company_id ) ? $company_id : ""; ?>";
</script>
<div id='media-controls' class='simple-controls mt-1em mb-1em'>
	<a href="#" class="normal-bold-button green"><span class="tiny-text">Available Space:</span><?php echo $available_space; ?>MB</a>
	<button id="media-uploader-opener" class="blue-bold-button">Upload</button>
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="media-uploader" class="hidden" method='POST' enctype='multipart/form-data'>
		<input type="file" name="upload[]" id="file-holder" multiple="multiple">
		<input type='hidden' name='action' value='upload_user_media_files'>
		<input type="hidden" name="company_id" value="<?php echo $company_id; ?>">
	</form>
</div>
<div id="medias-container">
	<?php echo $brother_->get_user_media( (object)array( "user_id" => $company_id, "is_ajax" => false ) ); ?>
</div>
