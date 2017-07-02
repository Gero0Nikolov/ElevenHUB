<?php
/**

*	Template Name: Homepage

*	@package eleven hub

*/

get_header();
?>

<div id="homepage-banner" class="big-banner" <?php if ( wp_is_mobile() ) { ?>style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/banner.jpg');"<?php } ?>>
	<?php if ( !wp_is_mobile() ) { ?>
	<video id="video-background" preload="none" autoplay="autoplay" loop="loop" muted="muted">
		<source src="<?php echo get_template_directory_uri(); ?>/assets/images/hub-video.mp4" type="video/mp4">
	</video>
	<?php } ?>
	<h1 class="banner-text">The <span class="red-highlithed">hub</span> where your company stays connected!</h1>
</div>

<div class="flex-container mt-3em">
	<div class="left-side">
		<h1 class="section-header">What is the <span class="dark-highlithed">hub</span>?</h1>
		<div class="section-content">
			By definition the <span class="quote">hub is the central part of a wheel that connects...</span>
			And exactly like it's explanation 11hub is connecting your employees!
			<div class="separator"></div>
			It works just like a social media, but what makes it different from the rest is the <strong>productivity</strong>.
			We are connecting your company members and keeping their productivity up to <strong>99%</strong>!
		</div>
	</div>
	<div class="right-side">
		<div class="full-size-banner" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/banner_1.jpg');">
		</div>
	</div>
</div>

<div class="flex-container mt-3em">
	<div class="left-side">
		<div class="full-size-banner" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/banner_2.jpg');">
		</div>
	</div>
	<div class="right-side">
		<h1 class="section-header">Who <span class="dark-highlithed">uses</span> it?</h1>
		<div class="section-content">
			ElevenHUB is for everyone! Here the companies can find their great minds and the great minds can find the right company for them!
			That becomes possible with the chance of business to share their company needs with the community and every user can write articles based on their experience, which helps the businesses to find the <strong>right fit</strong>.
			<div class="separator"></div>
			In the company itself employees can collaborate over projects, tasks &amp; ideas or simply share funny stories to melt the ices in the end of the working week!
		</div>
	</div>
</div>

<div class="flex-container mt-3em">
	<div class="left-side">
		<h1 class="section-header">How it <span class="dark-highlithed">works</span>?</h1>
		<div class="section-content">
			The hub works very simple &amp; semantic.<br/>
			It connects your team not only with the interesting stories from the office... In the hub you can create tasks for each of your team members which will keep the direction &amp; the communication cleaner. You can also schedule meetings in or out the office, keep track on the time spent on each of your products... And many more other possibilities are available here in the <span class="red-highlithed">hub</span>!
		</div>
	</div>
	<div class="right-side">
		<div class="full-size-banner" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/banner_3.jpg');">
		</div>
	</div>
</div>

<?php get_footer(); ?>
