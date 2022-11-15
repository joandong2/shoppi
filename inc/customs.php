<?php
/**
 * 
 * Custom functions
 *
 * @package shoppi
 */

function custom_scripts() {
	wp_enqueue_script( 'ajax-script', get_template_directory_uri(). '/inc/js/custom.js', array( 'jquery' ), _S_VERSION, true);
	wp_localize_script( 'ajax-script', 'ajax_object', array( 
		'jo_ajaxurl' => admin_url( 'admin-ajax.php' ),
		'jo_nonce' => wp_create_nonce('jo_nonce')
	));
}
add_action( 'wp_enqueue_scripts', 'custom_scripts' );

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
			/
            <li id="top-seller">Top Sellers</li>
			/
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

	check_ajax_referer( 'jo_nonce', 'nonce' );  // This function will die if nonce is not correct
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

function wishlist_ids() { 
	if (!empty( $_COOKIE['wishlist_ids'])) {
		return explode(',', $_COOKIE['wishlist_ids']);
	}
	else {
		return array();
	}
}

function jo_add_to_wishlist() {
	check_ajax_referer( 'jo_nonce', 'nonce' );  // This function will die if nonce is not correct
	$wish_id = sanitize_text_field($_POST['wish_id']);

	// if (!empty($wish_id)) {
	// 	$new_wish_id = array($wish_id);
	// 	$wishlist_ids = array_merge($new_wish_id, wishlist_ids());
	// 	$wishlist_ids = array_diff($wishlist_ids, array(''));
	// 	$wishlist_ids = array_unique($wishlist_ids);
	// 	setcookie('wishlist_ids', implode(',', $wishlist_ids) , time() + 3600 * 24 * 365, '/');
	// 	echo count($wishlist_ids);
	// }

	echo $wish_id;

	wp_die();
}
add_action('wp_ajax_jo_add_to_wishlist', 'jo_add_to_wishlist');
add_action('wp_ajax_nopriv_jo_add_to_wishlist', 'jo_add_to_wishlist');