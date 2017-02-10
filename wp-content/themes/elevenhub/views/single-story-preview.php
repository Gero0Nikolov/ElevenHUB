<?php
/**
 * View for Company settings page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */

$brother_ = new BROTHER;

$post_id = get_the_ID();
$post_ = get_post( $post_id );
$post_banner = $brother_->get_post_banner_url( $post_id );
$post_likes = count( $brother_->get_story_likes( $post_id ) );
$post_comments = $brother_->get_story_comments( (object)array( "story_id" => $post_id, "user_id" => $post_->post_author ) );
$post_comments_count = count( $post_comments );

$company_id = get_post_meta( $post_id, "related_company_id", true );
$company_avatar = $brother_->get_user_avatar_url( $company_id );
$company_first_name = get_user_meta( $company_id, "first_name", true );
$company_last_name = get_user_meta( $company_id, "last_name", true );
$company_short_name = get_user_meta( $company_id, "user_shortname", true );
$company_url = get_author_posts_url( $company_id );

$author_id = $post_->post_author;
$author_avatar = $brother_->get_user_avatar_url( $author_id );
$author_banner = $brother_->get_user_banner_url( $author_id );
$author_first_name = get_user_meta( $author_id, "first_name", true );
$author_last_name = get_user_meta( $author_id, "last_name", true );
$author_short_name = get_user_meta( $author_id, "user_shortname", true );
$author_stories = $brother_->count_user_stories( $author_id );
$author_url = get_author_posts_url( $author_id );

//var_dump( $post_ );
?>

<div id="story-view" class="single-story-view">
	<div id="author-container" class="author-container">
		<a href="<?php echo $author_url; ?>" class="author-anchor">
			<div class="author-banner" style="background-image: url(<?php echo $author_banner; ?>);">
				<div class="author-avatar" style="background-image: url(<?php echo $author_avatar; ?>);"></div>
			</div>
			<h1 class="author-names"><?php echo empty( $author_short_name ) ? $author_first_name ." ". $author_last_name : $author_short_name; ?></h1>
		</a>
		<div class="author-meta">
			<p class="meta"><span class="fa fa-at icon belize-hole"></span><a href="<?php echo $company_url; ?>" class="company-anchor"><?php echo empty( $company_short_name ) ? $company_first_name ." ". $company_last_name : $company_short_name; ?><span class="company-avatar" style="background-image: url(<?php echo $company_avatar; ?>);"></span></a></p>
			<p class="meta"><span class="fa fa-archive icon wisteria"></span><?php echo $author_stories ." ". ( $author_stories == 1 ? "story" : "stories" ); ?></p>
		</div>
	</div>
	<div id="story-container" class="story-container">
		<div class="story-banner" style="background-image: url(<?php echo $post_banner; ?>);"></div>
		<h1 class="story-title"><?php echo $post_->post_title; ?></h1>
		<div class="post-content"><?php echo $post_->post_content; ?></div>
		<div class="post-meta">
			<button id="like-controller" class="fa <?php echo !$brother_->has_liked( "", $post_id ) ? "fa-heart-o" : "fa-heart" ; ?> control" story-id="<?php echo $post_id; ?>"><i class="numbers"><?php echo $post_likes; ?></i></button>
			<button id="comment-controller" class="fa fa-comment control" story-id="<?php echo $post_id; ?>"><i class="numbers"><?php echo $post_comments_count; ?></i></button>
		</div>
		<div id="comments-container" class="post-comments">
			<div id="comments" class="comments-list">
				<?php
				foreach ( $post_comments as $comment_ ) {
				?>

				<div id="comment-<?php echo $comment_->id; ?>" class="comment">
					<div class="user-container">
						<a href="<?php echo $comment_->user[ "url" ]; ?>" class="user-anchor">
							<div class="avatar" style="background-image: url(<?php echo $comment_->user[ "avatar" ] ?>);"></div>
						</a>
					</div>
					<div class="comment-content">
						<?php echo $comment_->content; ?>
					</div>
					<?php if ( $comment_->user[ "is_author" ] == true ) { ?>
					<div class="comment-meta">
						<button id="edit-<?php echo $comment_->id; ?>" class="edit-controller fa fa-pencil"></button>
						<button id="delete-<?php echo $comment_->id; ?>" class="delete-controller fa fa-trash-o"></button>
					</div>
					<?php } ?>
				</div>

				<?php
				}
				?>
			</div>

			<div class="comment-composer">
				<input type="text" id="comment-holder">
				<button id="comment-controller" class="fa fa-paper-plane"></button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
initializeSingleStoryControls();

jQuery( document ).ready(function(){
	jQuery( ".story-container .post-comments .comment" ).each(function(){
		jQuery( this ).find( ".edit-controller" ).on( "click", function(){
			storyController = new UserStory();
			storyController.editComment( jQuery( this ).attr( "id" ).split( "-" )[1], ".story-container .post-comments .comments-list #comment-"+ jQuery( this ).attr( "id" ).split( "-" )[1] +" .comment-content", ".story-container .post-comments #comment-holder" );
		} );

		jQuery( this ).find( ".delete-controller" ).on( "click", function(){
			storyController = new UserStory();
			storyController.deleteComment( jQuery( this ).attr( "id" ).split( "-" )[1], function( response ){
				if ( response.result == true ) { jQuery( ".story-container .post-comments .comments-list #comment-"+ response.comment_id ).remove(); }
				else { console.log( response ); }
			} );
		} );
	});
});
</script>
