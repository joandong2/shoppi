<?php 
/*
 Template Name: Wishlist
*/

get_header();
?>

    <main>
        <div class="container">
            <h1 class="page-title">Wishlist</h1>
        <?php 

            if ( isset( $_COOKIE['wishlist_ids'] ) ) {
                $args = array(  
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'orderby' => 'date', 
                    'post__in'   => explode( ',', $_COOKIE['wishlist_ids'] ),
                );

                $loop = new WP_Query( $args ); 

                echo '<div class="product-loop">';

                if($loop->have_posts()) :
                    while ( $loop->have_posts() ) : $loop->the_post(); 
                        get_template_part( 'template-parts/product', get_post_type() );
                    endwhile; 
                endif; 

                echo '</div>';

                wp_reset_postdata();

            } else {
                echo '<p>No products in your wishlists..</p>';
            }
        ?>
        </div>
    </main>
<?php
get_footer();
