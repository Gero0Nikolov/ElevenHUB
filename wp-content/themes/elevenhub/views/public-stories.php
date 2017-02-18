<?php
/**
 * View for Public Stories page template
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package elevenhub
 */
?>
<div id="public-stories-container" class="public-stories-container"><?php get_public_stories(); ?></div>
<script type="text/javascript">
var storiesOffset = 5;
var lockStoriesLoad = false;
jQuery( window ).scroll(function(){
	if ( jQuery( window ).scrollTop() + jQuery( window ).height() > jQuery( document ).height() - 100 ) {
		if ( lockStoriesLoad == false ) {
			jQuery( "#public-stories-container" ).append( loading );

			jQuery.ajax({
				url : ajax_url,
				type : 'post',
				data : {
					action : "call_get_public_stories",
					offset: storiesOffset
				},
				success : function ( response ) {
					jQuery( "#public-stories-container #loader" ).remove();
					jQuery( "#public-stories-container" ).append( response );
					storiesOffset += 5;

					if ( response != 0 ) { lockStoriesLoad = false; }
				}
			});
		}
		lockStoriesLoad = true;
	}
});
</script>
