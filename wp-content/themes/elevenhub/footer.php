<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package elevenhub
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">
		<a href="https://11hub.net/developers" target="_blank" id="develop-form-controller" class="footer-link">Develop</a>
		<span class="footer-separator">&bull;</span>
		<a href="<?php echo get_site_url(); ?>/story" class="footer-link">Stories</a>
		<span class="footer-separator">&bull;</span>
		<a href="https://www.instagram.com/11hub/" target="_blank" class="footer-link">Instagram</a>
		<span class="footer-separator">&bull;</span>
		<a href="<?php echo get_site_url(); ?>/terms-conditions" class="footer-link">Terms &amp; Conditions</a>
		<span class="footer-separator">&bull;</span>
		<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'elevenhub' ) ); ?>" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/wordpress.png" width="100px" title="Proudly powered by WordPress" alt="Proudly powered by WordPress" /></a>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
