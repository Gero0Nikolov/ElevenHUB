<?php
/**
 * View for Hubbers List pagetemplate
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;
?>
<script type="text/javascript">
	var usersOffset = 100;
</script>
<div id="hubbers-list">
	<?php $brother_->get_hubbers( (object)array( "number" => 100 ) ); ?>
</div>
