<?php
$page_id = get_the_ID();
$page_ = get_post( $page_id );
?>
<div class="inner-page">
	<h1 class="section-header"><?php echo $page_->post_title; ?></h1>
	<div class="section-content"><?php echo $page_->post_content; ?></div>
</div>
