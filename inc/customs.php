<?php
/**
 * 
 * Custom functions
 *
 * @package shoppi
 */

function jc_tabbed_products_functions( ) {
    $loop = new WP_Query( array(
		'post_type' => 'product',
		'post_status' => 'publish',
		'posts_per_page' => 8, 
		'tax_query' => array(
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
			),
		),
	));

    ob_start(); ?>
        <ul id="jc-tabbed-products" class="jc-tabbed-products">
            <li id="featured" class="active">Featured</li>
			<span class="dashicons dashicons-controls-pause"></span>
            <li id="top-seller">Top Sellers</li>
			<span class="dashicons dashicons-controls-pause"></span>
            <li id="new-arrivals">New Arrivals</li>
        </ul>
		<div class="loader">
			<div class="ripple"></div>
		</div>
		<div id="jc-tabbed-content">
			
			<?php
			if($loop->have_posts()) :
				while ( $loop->have_posts() ) : $loop->the_post(); 
					get_template_part( 'template-parts/product', get_post_type() );
				endwhile; 
			endif;
			?>
		</div>

	<?php wp_reset_postdata();
	$output = ob_get_clean();
	return $output;
}
add_shortcode( 'jc-tabbed-products', 'jc_tabbed_products_functions' );

function jo_load_more() {

	$curr_id = sanitize_text_field($_POST['curr_id']);

	switch($curr_id) { 
		case 'featured':
			$args = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'posts_per_page' => 8, 
				'tax_query' => array(
                    array(
                        'taxonomy' => 'product_visibility',
                        'field'    => 'name',
                        'terms'    => 'featured',
                    ),
                ),
            );
			break;

		case 'top-seller':
			$args = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'meta_key' => 'total_sales',
				'orderby' => 'meta_value_num',
				'posts_per_page' => 8,
			);
			break;

		case 'new-arrivals':
			$args = array(  
				'post_type' => 'product',
				'post_status' => 'publish',
				'posts_per_page' => 8,
				'orderby' => 'date', 
			);
			break;
	}
	
	$loop = new WP_Query( $args ); 

	if($loop->have_posts()) :
		while ( $loop->have_posts() ) : $loop->the_post(); 
			get_template_part( 'template-parts/product', get_post_type() );
		endwhile; 
	endif; 

	wp_die();
}
add_action('wp_ajax_jo_load_more', 'jo_load_more');
add_action('wp_ajax_nopriv_jo_load_more', 'jo_load_more');

function jo_add_to_wishlist() {
	$wish_id = sanitize_text_field($_POST['wish_id']);

	if (!empty($wish_id)) {
		if(in_array($wish_id, explode( ',', $_COOKIE['wishlist_ids'] ))) {
			$new_arr = explode( ',', $_COOKIE['wishlist_ids'] );
            if (($delete_post_id = array_search($wish_id, $new_arr)) !== false) {
                unset($new_arr[$delete_post_id]);
            }
            setcookie('wishlist_ids', implode(',', $new_arr) , time() + 3600 * 24 * 30, '/');
			echo 'removed';
		} else {
			$new_wish_id = array($wish_id);
			$wishlist_ids = array_merge($new_wish_id, (isset($_COOKIE['wishlist_ids']) ? explode( ',', $_COOKIE['wishlist_ids'] ) : array()));
			$wishlist_ids = array_diff($wishlist_ids, array(''));
			$wishlist_ids = array_unique($wishlist_ids);
			setcookie('wishlist_ids', implode(',', $wishlist_ids) , time() + 3600 * 24 * 365, '/');
			echo 'added';
		}
	}
	wp_die();
}
add_action('wp_ajax_jo_add_to_wishlist', 'jo_add_to_wishlist');
add_action('wp_ajax_nopriv_jo_add_to_wishlist', 'jo_add_to_wishlist');


add_action( 'add_meta_boxes', 'create_custom_product_meta_box' );
function create_custom_product_meta_box()
{
	add_meta_box(
		'hover_image',
		__( 'Hover Product Image <em>(optional)</em>', 'shoppi' ),
		'add_custom_content_meta_box',
		'product',
		'side',
		'default'
	);
}

function add_custom_content_meta_box( $post ){
	$hover_image = get_post_meta($post->ID, 'hover_image', true) ? get_post_meta($post->ID, 'hover_image', true) : '';
    echo multi_media_uploader_field( 'hover_image', $hover_image ); 
}

function multi_media_uploader_field($name, $value = '') {
    $image_str = '';
    $display = 'none';

    if (!empty($value)) {
		if ($image_attributes = wp_get_attachment_image_src($value, '$image_size')) {
			$image_str .= '<li data-attechment-id=' . $value . '><img style="max-width:260px;max-height:380px;" src="' . $image_attributes[0] . '" /></li>';
		}
    }

    if($image_str){
        $display = 'inline-block';
    }

    return '
		<div class="multi-upload-medias">
			<ul>' . $image_str . '</ul>
			<a href="#" class="wc_multi_upload_image_button">Set Hover Image</a>
			<input type="hidden" class="attechments-ids ' . $name . '" name="' . $name . '" id="' . $name . '" value="' . esc_attr($value) . '" />
			<a href="#" class="wc_multi_remove_image_button" style="margin-left:10px;display:inline-block;display:' . $display . '">Remove media</a>
		</div>';
}

// Save Meta Box values.
add_action( 'save_post', 'wc_meta_box_save' );

function wc_meta_box_save( $post_id ) {
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return; 
    }
    
    if( isset( $_POST['hover_image'] ) ){
        update_post_meta( $post_id, 'hover_image', $_POST['hover_image'] );
    }
}

function jc_blog_posts( $atts ) {
	$atts = shortcode_atts(
		array(
			'num' => 4,
		), $atts );

    $loop = new WP_Query( array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'posts_per_page' => $atts["num"], 
	));

    ob_start(); ?>
		<div id="jc-blog-posts">
			<?php
			if($loop->have_posts()) :
				while ( $loop->have_posts() ) : $loop->the_post(); 
					get_template_part( 'template-parts/content-blog', get_post_type() );
				endwhile; 
			endif;
			?>
		</div>

	<?php wp_reset_postdata();
	$output = ob_get_clean();
	return $output;
}
add_shortcode( 'jc-blog-posts', 'jc_blog_posts' );

function jc_load_more() {

	//check_ajax_referer( 'jo_nonce', 'nonce' );  // This function will die if nonce is not correct.
	$paged = sanitize_text_field($_POST['paged']);
	$orderBy = sanitize_text_field($_POST['orderBy']);
	
	// } else {
		// $args = array(  
		// 	'post_type' => 'post',
		// 	'category__in' => $curr_id ? $curr_id : null,
		// 	'posts_per_page' => get_option('posts_per_page'),
		// 	'orderby' => 'date',
		// 	'order' => 'DESC',
		// 	'paged' => $paged,
		// );
	//}

	$args = array(  
		'post_type' => 'product',
		//'category__in' => $curr_id ? $curr_id : null,
		'posts_per_page' => get_option('posts_per_page'),
		'orderby' => $orderBy ? $orderBy : 'date',
		'order' => 'DESC',
		'paged' => $paged,
	);

	$loop = new WP_Query( $args ); 

	if($loop->have_posts()) :
		while ( $loop->have_posts() ) : $loop->the_post(); 
			wc_get_template_part( 'content', 'product' );
		endwhile; 
	endif; 

	wp_die();
  }
  add_action('wp_ajax_jc_load_more', 'jc_load_more');
  add_action('wp_ajax_nopriv_jc_load_more', 'jc_load_more');


remove_action( 'woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title', 10 );
add_action('woocommerce_shop_loop_item_title', 'jc_product_loop_title', 10 );
function jc_product_loop_title() {
	$product = new WC_Product( get_the_ID() ); 
	$active_wishlist = isset($_COOKIE['wishlist_ids']) ? in_array( get_the_id(), explode(',', $_COOKIE['wishlist_ids']) ) : null;
	$wish_class = $active_wishlist===true ? 'active' : null;

    echo '<div class="product-tags">';
		echo wc_get_product_tag_list( $product->get_id(), ' ' );
	echo '</div>';
	echo '<div class="product-intro">';
		echo '<div style="display:flex">';
			echo '<p data-id="'. get_the_ID() .'" class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">'. $product->get_name() .'</a></p>';
			echo '<span>'. jo_starRating($product->average_rating) .'</span>';
		echo '</div>';
		echo '<a id="'.get_the_ID().'" href="#" class="'. $wish_class .' jo-wishlist-icon"><span class="dashicons dashicons-heart"></span></a>';

	echo '</div>';
	echo wc_price( wc_get_price_including_tax( $product ) );
}

remove_action( 'woocommerce_before_shop_loop_item_title','woocommerce_template_loop_product_thumbnail', 10 );
add_action('woocommerce_before_shop_loop_item_title', 'jc_product_loop_image', 10 );

function jc_product_loop_image() { 
	?>
	<div class="product-thumbnails">
		<?php 
			$product = new WC_Product( get_the_ID() );
			$hover_image = get_post_meta(get_the_ID(), 'hover_image', true);
			$image_attributes = wp_get_attachment_image_src($hover_image, 'full');
			$main_image = wp_get_attachment_image_src($product->image_id, 'full');
			//$image_attributes[0]
				
			//shoppi_post_thumbnail( );
			if($image_attributes) {
				echo '<div class="product-image-with-hover">';
					echo '<a href="'. get_permalink() .'">';
						echo '<img height="400" alt="main-image" class="hover-thumbnail" src="' . $image_attributes[0] . '"/>';
						echo '<img height="400" alt="hover-image" class="main-thumbnail" src="' . $main_image[0] . '"/>';
					echo '</a>';
				echo '</div>';
			} else {
				echo '<div class="product-image">';
					echo '<a href="'. get_permalink() .'">';
						echo '<img height="400" alt="main-image" class="main-thumbnail" src="' . $main_image[0] . '"/>';
					echo '</a>';
				echo '</div>';
			}
			
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
}
