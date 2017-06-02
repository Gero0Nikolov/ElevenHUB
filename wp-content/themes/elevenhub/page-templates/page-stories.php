<?php
/**

*	Template Name: Stories Page (Blog)

*	@package eleven hub

*/

get_header();

$args = array(
	"pots_per_page" => -1,
	"post_type" => "story",
	"post_status" => "publish",
	"orderby" => "ID",
	"order" => "DESC"
);
$stories_ = get_posts( $args );

foreach ( $stories_ as $story_ ) {
	$story_featured_image = get_the_post_thumbnail_url( $story_->ID );
	?>

	<a href="<?php echo get_permalink( $story_->ID ); ?>" class="story-anchor">
		<div id="story-<?php echo $story_->ID; ?>" class="story-container" style="background-image: url(<?php echo $story_featured_image; ?>);">
			<div class="overlay">
				<h1 class="title"><?php echo get_the_title( $story_->ID ); ?></h1>
			</div>
		</div>
	</a>

	<?php
}

get_footer();
?>
