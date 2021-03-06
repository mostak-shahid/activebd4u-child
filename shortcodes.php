<?php
function admin_shortcodes_page(){
	//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null )
    add_menu_page( 
        __( 'Theme Short Codes', 'textdomain' ),
        'Short Codes',
        'manage_options',
        'shortcodes',
        'shortcodes_page',
        'dashicons-book-alt',
        3
    ); 
}
add_action( 'admin_menu', 'admin_shortcodes_page' );
function shortcodes_page(){
	?>
	<div class="wrap">
		<h1>Theme Short Codes</h1>
		<ol>
			<li>[home-url slug=''] <span class="sdetagils">displays home url</span></li>
			<li>[site-identity class='' container_class=''] <span class="sdetagils">displays site identity according to theme option</span></li>
			<li>[site-name link='0'] <span class="sdetagils">displays site name with/without site url</span></li>
			<li>[copyright-symbol] <span class="sdetagils">displays copyright symbol</span></li>
			<li>[this-year] <span class="sdetagils">displays 4 digit current year</span></li>		
			<li>[feature-image wrapper_element='div' wrapper_atts='' height='' width=''] <span class="sdetagils">displays feature image</span></li>		
			<li>[font-awesome class="" container-class=""] <span class="sdetagils">displays feature image</span></li>		
			<li>[mos-embed url="" ratio="32by9/21by9/16by9/4by3/1by1"] <span class="sdetagils">displays Embeds</span></li>		
			<li>[mos-popup url="" icon-class=""] <span class="sdetagils">displays Popup</span></li>		
		</ol>
	</div>
	<?php
}
function home_url_func( $atts = array(), $content = '' ) {
	$atts = shortcode_atts( array(
		'slug' => '',
	), $atts, 'home-url' );

	return home_url( $atts['slug'] );
}
add_shortcode( 'home-url', 'home_url_func' );
function site_identity_func( $atts = array(), $content = null ) {
	global $forclient_options;
	$logo_url = ($forclient_options['logo']['url']) ? $forclient_options['logo']['url'] : get_template_directory_uri(). '/images/logo.png';
	$logo_option = $forclient_options['logo-option'];
	$html = '';
	$atts = shortcode_atts( array(
		'class' => '',
		'container_class' => ''
	), $atts, 'site-identity' ); 
	
	
	$html .= '<div class="logo-wrapper '.$atts['container_class'].'">';
		if($logo_option == 'logo') :
			$html .= '<a class="logo '.$atts['class'].'" href="'.home_url().'">';
			list($width, $height) = getimagesize($logo_url);
			$html .= '<img class="img-responsive img-fluid" src="'.$logo_url.'" alt="'.get_bloginfo('name').' - Logo" width="'.$width.'" height="'.$height.'">';
			$html .= '</a>';
		else :
			$html .= '<div class="text-center '.$atts['class'].'">';
				$html .= '<h1 class="site-title"><a href="'.home_url().'">'.get_bloginfo('name').'</a></h1>';
				$html .= '<p class="site-description">'.get_bloginfo( 'description' ).'</p>';
			$html .= '</div>'; 
		endif;
	$html .= '</div>'; 
		
	return $html;
}
add_shortcode( 'site-identity', 'site_identity_func' );

function site_name_func( $atts = array(), $content = '' ) {
	$html = '';
	$atts = shortcode_atts( array(
		'link' => 0,
	), $atts, 'site-name' );
	if ($atts['link']) $html .=	'<a href="'.esc_url( home_url( '/' ) ).'">';
	$html .= get_bloginfo('name');
	if ($atts['link']) $html .=	'</a>';
	return $html;
}
add_shortcode( 'site-name', 'site_name_func' );
function copyright_symbol_func() {
	return '&copy;';
}
add_shortcode( 'copyright-symbol', 'copyright_symbol_func' );
function this_year_func() {
	return date('Y');
}
add_shortcode( 'this-year', 'this_year_func' );


function feature_image_func( $atts = array(), $content = '' ) {
	global $mosacademy_options;
	$html = '';
	$img = '';
	$atts = shortcode_atts( array(
		'wrapper_element' => 'div',
		'wrapper_atts' => '',
		'height' => '',
		'width' => '',
	), $atts, 'feature-image' );

	if (has_post_thumbnail()) $img = get_the_post_thumbnail_url();	
	elseif(@$mosacademy_options['blog-archive-default']['id']) $img = wp_get_attachment_url( $mosacademy_options['blog-archive-default']['id'] ); 
	if ($img){
		if ($atts['wrapper_element']) $html .= '<'. $atts['wrapper_element'];
		if ($atts['wrapper_atts']) $html .= ' ' . $atts['wrapper_atts'];
		if ($atts['wrapper_element']) $html .= '>';
		list($width, $height) = getimagesize($img);
		if ($atts['width'] AND $atts['height']) :
			if ($width > $atts['width'] AND $height > $atts['height']) $img_url = aq_resize($img, $atts['width'], $atts['height'], true);
			else $img_url = $img;
		elseif ($atts['width']) :
			if ($width > $atts['width']) $img_url = aq_resize($img, $atts['width']);
			else $img_url = $img;
		else : 
			$img_url = $img;
		endif;
		list($fwidth, $fheight) = getimagesize($img_url);
		$html .= '<img class="img-responsive img-fluid img-featured" src="'.$img_url.'" alt="'.get_the_title().'" width="'.$fwidth.'" height="'.$fheight.'" />';
		if ($atts['wrapper_element']) $html .= '</'. $atts['wrapper_element'] . '>';
	}
	return $html;
}
add_shortcode( 'feature-image', 'feature_image_func' );

function font_awesome_func( $atts = array(), $content = '' ) {
    $html= "";
	$atts = shortcode_atts( array(
		'class' => '',
		'container-class' => '',
	), $atts, 'font-awesome' );
    $html .= '<div class="'.$atts['container-class'].'"><i class="fa fas '.$atts['class'].'"></i></div>';
	return $html;
}
add_shortcode( 'font-awesome', 'font_awesome_func' );

function mos_embed_func($atts = array(), $content = '') {
	$atts = shortcode_atts( array(
        'url' => '',
		'ratio' => '21by9',
	), $atts, 'mos-embed' );
    ob_start(); ?>
        <div class="embed-responsive embed-responsive-<?php echo $atts['ratio'] ?>">
            <iframe class="embed-responsive-item" src="<?php echo $atts['url'] ?>"></iframe>
        </div>
    <?php $html = ob_get_clean();
    return $html;
}
add_shortcode( 'mos-embed', 'mos_embed_func' );

function mos_popup_func($atts = array(), $content = '') {
	$atts = shortcode_atts( array(
        'url' => '',
        'icon-class' => 'fa-play',
	), $atts, 'mos-popup' );
    ob_start(); ?>
        <div class="popup-btn-wrapper">
            <a data-fancybox="gallery" href="<?php echo $atts['url'] ?>"><i class="fa <?php echo $atts['icon-class'] ?>"></i></a>
        </div>
    <?php $html = ob_get_clean();
    return $html;
}
add_shortcode( 'mos-popup', 'mos_popup_func' );



add_shortcode( 'my_purchased_products', 'bbloomer_products_bought_by_curr_user' );
   
function bbloomer_products_bought_by_curr_user() {
    $html = '';
    // GET CURR USER
    $current_user = wp_get_current_user();
    if ( 0 == $current_user->ID ) return;
   
    // GET USER ORDERS (COMPLETED + PROCESSING)
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => $current_user->ID,
        'post_type'   => wc_get_order_types(),
        'post_status' => array_keys( wc_get_is_paid_statuses() ),
    ) );
   
    // LOOP THROUGH ORDERS AND GET PRODUCT IDS
    if ( ! $customer_orders ) return;
    $product_ids = array();
    foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order->ID );
        $items = $order->get_items();
        foreach ( $items as $item ) {
            $product_id = $item->get_product_id();
            $product_ids[] = $product_id;
        }
    }
    $product_ids = array_unique( $product_ids );
    $product_ids_str = implode( ",", $product_ids );
   
    // PASS PRODUCT IDS TO PRODUCTS SHORTCODE
    if ($product_ids_str) {
        $html .= '<h4>Products for you</h4>';
        $html .= do_shortcode("[products ids='$product_ids_str' columns=2]");
    }
    return $html;
   
}

function porduct_carousel_func( $atts = array(), $content = '' ) {
	$html = '';
    ob_start();
	$atts = shortcode_atts( array(
        'title'             => '',
		'limit'				=> '-1',
		'offset'			=> 0,
		'category'			=> '',
		'tag'				=> '',
		'orderby'			=> '',
		'order'				=> '',
		'container'			=> 0,
		'container_class'	=> '',
		'class'				=> '',
        'show'              => 4,
	), $atts, 'porduct_carousel' );

	$cat = ($atts['category']) ? preg_replace('/\s+/', '', $atts['category']) : '';
	$tag = ($atts['tag']) ? preg_replace('/\s+/', '', $atts['tag']) : '';

	$args = array( 
		'post_type' 		=> 'product',
		'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
	);
	$args['posts_per_page'] = $atts['limit'];
	if ($atts['offset']) $args['offset'] = $atts['offset'];

	if ($atts['category'] OR $atts['tag']) {
		$args['tax_query'] = array();
		if ($atts['category'] AND $atts['tag']) {
			$args['tax_query']['relation'] = 'OR';
		}
		if ($atts['category']) {
			$args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => explode(',', $cat),
				);
		}
		if ($atts['tag']) {
			$args['tax_query'][] = array(
					'taxonomy' => 'product_tag',
					'field'    => 'term_id',
					'terms'    => explode(',', $tag),
				);
		}
	}
	if ($atts['orderby']) $args['orderby'] = $atts['orderby'];
	if ($atts['order']) $args['order'] = $atts['order'];
	if (@$atts['author']) $args['author'] = $atts['author'];
    $rand = rand(1000,9999); 
    ?>
    <div class="product-carousel-wrap">
    <?php 
	$query = new WP_Query( $args );
	if ( $query->have_posts() ) :
        ?>
        <div class="paginator-center">
            <h2 class="product-carousel-title"><?php echo $atts['title'] ?></h2>
            <ul>
                <li class="prev"><i class="fa fa-angle-left"></i></li>
                <li class="next"><i class="fa fa-angle-right"></i></li>
            </ul>
        </div>
		<div id="product-carousel-<?php echo $rand?>" class="product-carousel-container <?php echo $atts['container_class'] ?>"  data-slick='{"slidesToShow": <?php echo $atts['show'] ?>}'>
        <?php if ($atts['title']) : ?>
        <?php endif?>
		<?php while ( $query->have_posts() ) : $query->the_post(); 
            $product = wc_get_product( get_the_ID() );
            ?>
		    <div <?php post_class( $classes ); ?>>
		        <div class="wpisset-woo-loop-thumbnail-wrapper">
                        <a href="http://skyla.lpdthemesdemo.com/product/argan-baby-lotion/" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
                        <?php if ($product->is_on_sale()) : ?>
		                    <span class="onsale"><i class="icon-star"></i></span>
		                <?php endif?>
		                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(),'medium') ?>" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" loading="lazy">
		                </a>

		            <div class="button-rating-wrapper">
                        <?php if($product->get_average_rating()) : ?>
		                <div class="star-rating-wrapper">
		                    <div class="star-rating" role="img" aria-label="Rated <?php echo $product->get_average_rating(); ?> out of 5"><span style="width:80%">Rated <strong class="rating"><?php echo $product->get_average_rating(); ?></strong> out of 5</span></div>
		                </div>
		                <?php endif?>
		                <?php if($product->get_type() == 'variable') : ?>
		                <a href="<?php echo get_the_permalink() ?>" class="button">Select Item</a>
		                <?php else : ?>
		                <a href="?add-to-cart=<?php echo get_the_ID() ?>" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo get_the_ID() ?>" data-product_sku="123456789-17" aria-label="Add “<?php echo get_the_title() ?>” to your cart" rel="nofollow">Add to cart</a>
		                <?php endif;?>
		            </div>
		        </div>
		        <div class="wpisset-woo-loop-content-wrapper">
		            <div class="wpisset-woo-loop-content">
		                <h4 class="woocommerce-loop-product__title"><?php echo get_the_title() ?></h4>
		                <?php echo $product->get_price_html(); ?>
		            </div>
		        </div>
		    </div>
        <?php endwhile; ?>
		</div><!--/.product-carousel-container-->
		<?php wp_reset_postdata();
    endif;
    ?>
    </div>
    <script>
    jQuery(document).ready(function($){
        $('#product-carousel-<?php echo $rand?>').slick({
            infinite: true,
            slidesToShow: 4,
            slidesToScroll: 1,
            prevArrow: $('#product-carousel-<?php echo $rand?>').siblings().find('.prev'),
            nextArrow: $('#product-carousel-<?php echo $rand?>').siblings().find('.next'),
            autoplay: true,
            autoplaySpeed: 2000,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                    }
                }
            ],
        });
    });
    </script>
    <?php
    $html = ob_get_clean();
    return $html; 
}
add_shortcode( 'porduct_carousel', 'porduct_carousel_func' );