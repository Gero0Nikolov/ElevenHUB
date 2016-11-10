<?php 
/**

*	Template Name: Homepage

*	@package eleven hub

*/

get_header();

$post_id = get_the_ID();
$page_featured_img = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
?>

<div id="homepage-banner" class="big-banner" style="background-image: url(<?php echo $page_featured_img; ?>);">
</div>
<?php echo get_post_field( 'post_content', $post_id ); ?>

<?php get_footer(); ?>