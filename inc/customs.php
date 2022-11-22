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
}?>

