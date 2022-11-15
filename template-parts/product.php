<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package shoppi
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php $product = new WC_Product( get_the_ID() ); ?>

	<div class="product-thumbnail">
		<?php 
			//shoppi_post_thumbnail( );
			echo $product->get_image('full'); 
			if($product->is_on_sale()) {
				echo '<span class="sale">Sale!</span>';
			}
		?>
	</div>

	<?php 	
		echo '<p class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">'. $product->get_name() .'</a></p>';
		echo wc_price( wc_get_price_including_tax( $product ) ); 
	?>

</article><!-- #post-<?php the_ID(); ?> -->