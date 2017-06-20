<?php
$post_id = get_the_ID();
$post_ = get_post( $post_id );
$post_featured_image = get_the_post_thumbnail_url( $post_id );
?>
<div id="story-<?php echo $post_id ?>" class="public-story">
	<div id="banner-container" style="background-image: url(<?php echo $post_featured_image; ?>);">
		<div class="overlay">
			<h1 class="title"><?php echo $post_->post_title; ?></h1>
		</div>
	</div>
	<div class="text"><?php echo wpautop( $post_->post_content ); ?></div>
</div>
