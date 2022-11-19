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

	<?php 
		$product = new WC_Product( get_the_ID() ); 
		$active_wishlist = isset($_COOKIE['wishlist_ids']) ? in_array( get_the_id(), explode(',', $_COOKIE['wishlist_ids']) ) : null;
		$wish_class = $active_wishlist===true ? 'active' : null;
	?>

	<div class="product-thumbnails">
		<?php 
			$hover_image = get_post_meta($post->ID, 'hover_image', true);
			$image_attributes = wp_get_attachment_image_src($hover_image, 'full');
			$main_image = wp_get_attachment_image_src($product->image_id, 'full');
			//$image_attributes[0]
				
			//shoppi_post_thumbnail( );
			echo '<div class="product-image">';
			if($hover_image) {
				echo '<img class="product-thumbnail" src="' . $main_image[0] . '" data-src="' . $main_image[0] . '" data-hover="' . $image_attributes[0] . '"/>';
			} else {
				echo '<img class="product-thumbnail" src="' . $main_image[0] . '" data-src="' . $main_image[0] . '"/>';
			}
				
			echo '</div>';
			echo '<div class="product-status">';
				if($product->is_on_sale()) {
					echo '<span class="sale">Sale!</span>';
				}
				if($product->get_stock_status() == 'outofstock') {
					echo '<span class="out-of-stock">Out Of Stock</span>';
				} 
			echo '</div>';
		?>
	</div>

	<?php 	
		echo '<div class="product-tags">';
			echo wc_get_product_tag_list( $product->get_id(), ' ' );
		echo '</div>';
		echo '<div class="product-intro">';
			echo '<p data-id="'. get_the_ID() .'" class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">'. $product->get_name() .'</a></p>';
			
			echo '<a id="'.get_the_ID().'" href="#" class="'. $wish_class .'"><i class="fa-solid fa-heart"></i></a>';

		echo '</div>';
		echo wc_price( wc_get_price_including_tax( $product ) );
		
		
	?>

</article><!-- #post-<?php the_ID(); ?> -->