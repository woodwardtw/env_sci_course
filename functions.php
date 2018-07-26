<?php
/**
 * Understrap functions and definitions
 *
 * @package understrap
 */

/**
 * Initialize theme default settings
 */
require get_template_directory() . '/inc/theme-settings.php';

/**
 * Theme setup and custom theme supports.
 */
require get_template_directory() . '/inc/setup.php';

/**
 * Register widget area.
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Enqueue scripts and styles.
 */
require get_template_directory() . '/inc/enqueue.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom pagination for this theme.
 */
require get_template_directory() . '/inc/pagination.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Custom Comments file.
 */
require get_template_directory() . '/inc/custom-comments.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load custom WordPress nav walker.
 */
require get_template_directory() . '/inc/class-wp-bootstrap-navwalker.php';

/**
 * Load WooCommerce functions.
 */
require get_template_directory() . '/inc/woocommerce.php';

/**
 * Load Editor functions.
 */
require get_template_directory() . '/inc/editor.php';

/**
 * Load widget functions.
 */
require get_template_directory() . '/inc/custom-widgets.php';

/**
 * Load widget functions.
 */
require get_template_directory() . '/inc/custom-post-types.php';


//set a path for IMGS

  if( !defined('THEME_IMG_PATH')){
   define( 'THEME_IMG_PATH', get_stylesheet_directory_uri() . '/imgs/' );
  }

//ADD FONTS and VCU Brand Bar
add_action('wp_enqueue_scripts', 'alt_lab_scripts');
function alt_lab_scripts() {
	$query_args = array(
		'family' => 'Roboto:300,400,700|Old+Standard+TT:400,700|Oswald:400,500,700',
		'subset' => 'latin,latin-ext',
	);
	wp_enqueue_style ( 'google_fonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );

	wp_enqueue_script( 'vcu_brand_bar', 'https:///branding.vcu.edu/bar/academic/latest.js', array(), '1.1.1', true );

	wp_enqueue_script( 'alt_lab_js', get_template_directory_uri() . '/js/alt-lab.js', array(), '1.1.1', true );

    }


function bannerMaker(){
	global $post;
	 if ( get_the_post_thumbnail_url( $post->ID ) && $post->post_type === 'page' ) {
      //$thumbnail_id = get_post_thumbnail_id( $post->ID );
      $thumb_url = get_the_post_thumbnail_url($post->ID);
      //$alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

        return '<div class="jumbotron custom-header-img" style="background-image:url('. $thumb_url .')"></div>';

    } 
}


//PAGINATION ADDITION FROM STACKOVERFLOW https://wordpress.stackexchange.com/questions/154360/pagination-custom-query
if ( ! function_exists( 'pagination' ) ) :
    function pagination( $paged = '', $max_page = '' )
    {
        $big = 999999999; // need an unlikely integer
        if( ! $paged )
            $paged = get_query_var('paged');
        if( ! $max_page )
            $max_page = $wp_query->max_num_pages;

        return paginate_links( array(
            'base'       => str_replace($big, '%#%', esc_url(get_pagenum_link( $big ))),
            'format'     => '?paged=%#%',
            'current'    => max( 1, $paged ),
            'total'      => $max_page,
            'mid_size'   => 1,
            'prev_text'  => __('«'),
            'next_text'  => __('»'),
            'type'       => 'list'
        ) );
    }
endif;




//shortcode for content by category
function altlab_content_shortcode( $atts, $content = null ) {
    extract(shortcode_atts( array(
         'cat' => '',  
         'num' => '',
         'ex' => ''
    ), $atts));     

    if (!$num){
      $num = 3;
    } 

    if (!$cat){
      $cat = false;
      $cat_link = get_post_type_archive_link();
    } else {
      $cat_id = get_cat_id($cat);
      $cat_name = get_cat_name($cat_id);
      $cat_link = get_category_link($cat_id);
    }

    if (!$ex){
      $ex = false;
    } 

    $html ='';
    $num = intval($num);    
   
               $args = array(
                      'posts_per_page' => $num,
                      'post_type'   => 'post', 
                      'post_status' => 'publish', 
                      'orderby' => 'date',  
                      'category_name' => $cat,
                      'nopaging' => false,                                        
                    );
               
                    // query
                    $the_query = new WP_Query( $args );
                    if( $the_query->have_posts() ): 
                      while ( $the_query->have_posts() ) : $the_query->the_post(); 
                      $html .= '<div class="row sc-posts">';
                      $html .= '<div class="sc-post-img col-md-12">';
                        if ( has_post_thumbnail() ) {
                        $html .=  get_the_post_thumbnail(get_the_ID(),'medium', array('class' => 'sc-post-image responsive aligncenter', 'alt' => 'Featured image.'));
                        }  
                       $html .= '</div><a href="'.get_post_permalink().'"><h3 class="sc-post-title">';
                       $html .=  get_the_title();
                       $html .= '</h3></a>';  
                       if ($ex){                  
                        $html .= '<div class="row"><div class="sc-post-content">' .get_the_excerpt() . '</div>';
                      }
                       $html .= '</div>';          
                     endwhile;
                     $html .= '<a class="content-button" href="'.$cat_link.'">See More '. $cat_name .'</a>';
                  endif;
            wp_reset_query();  // Restore global post data stomped by the_post().
   return $html;
}

add_shortcode( 'get-posts', 'altlab_content_shortcode' );


