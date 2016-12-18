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
	<?php $brother_->get_companies(); ?>
</div>
