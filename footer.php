<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package shoppi
 */

?>

	<footer id="colophon" class="site-footer">
		<div class="container">
			<div class="footer-widgets row">
				<div class="footer-widget col-sm-3">
					<?php if ( is_active_sidebar('footer-1') ) { ?>
						<?php dynamic_sidebar('footer-1'); ?>
					<?php } ?>
				</div>
				<div class="footer-widget col-sm-2">
					<?php if ( is_active_sidebar('footer-2') ) { ?>
						<?php dynamic_sidebar('footer-2'); ?>
					<?php } ?>
				</div>
				<div class="footer-widget col-sm-2">
					<?php if ( is_active_sidebar('footer-3') ) { ?>
						<?php dynamic_sidebar('footer-3'); ?>
					<?php } ?>
				</div>
				<div class="footer-widget col-sm-2">
					<?php if ( is_active_sidebar('footer-4') ) { ?>
						<?php dynamic_sidebar('footer-4'); ?>
					<?php } ?>
				</div>
				<div class="footer-widget col-sm-3">
					<?php if ( is_active_sidebar('footer-5') ) { ?>
						<?php dynamic_sidebar('footer-5'); ?>
					<?php } ?>
				</div>
			</div>
			<div class="site-info">
				<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'shoppi' ) ); ?>">
					<?php
					/* translators: %s: CMS name, i.e. WordPress. */
					printf( esc_html__( 'Proudly powered by %s', 'shoppi' ), 'WordPress' );
					?>
				</a>
				<span class="sep"> | </span>
					<?php
					/* translators: 1: Theme name, 2: Theme author. */
					printf( esc_html__( 'Theme: %1$s by %2$s.', 'shoppi' ), 'shoppi', '<a href="https://joblenda.me/">John Oblenda</a>' );
					?>
			</div><!-- .site-info -->
			<div>
				<img src="http://localhost/shoppi/wp-content/uploads/2022/11/payment-300x17-1.webp" alt="">
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
