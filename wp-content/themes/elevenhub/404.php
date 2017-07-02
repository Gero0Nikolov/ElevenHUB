<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package elevenhub
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				<header class="page-header">
					<h1 class="page-sign animated rubberBand"><?php esc_html_e( "404", "elevenhub" ); ?></h1>
					<h2 class="page-title animated rubberBand"><?php esc_html_e( "Wooah... There's nothing here.", "elevenhub" ); ?></h2>
				</header><!-- .page-header -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
