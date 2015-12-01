<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */
?>
</div><!-- .row -->
</div><!-- .container -->

    <?php
        $user_search = new WP_User_Query( array( 'role' => 'seller' ) );
        $sellers = (array) $user_search->get_results();
        $post_counts = count_many_users_posts( wp_list_pluck( $sellers, 'ID' ), 'product' );
        $i = 0;
        $seller = array();
        if ( $sellers ) {
			$isSellerFeatured='';
            foreach ($sellers as $user) {				 
                $store_info = dokan_get_store_info( $user->ID );
                $seller_enable = dokan_is_seller_enabled( $user->ID );
				$isSellerFeatured = get_user_meta($user->ID, 'dokan_feature_seller', true);
                if ($seller_enable) {
                    $map_location = isset( $store_info['location'] ) ? esc_attr( $store_info['location'] ) : '';
                    $store_name = isset( $store_info['store_name'] ) ? esc_html( $store_info['store_name'] ) : __( 'N/A', 'dokan' );
                    $store_url  = dokan_get_store_url( $user->ID);
                    $seller[$i]['id'] = $user->ID;
                    $seller[$i]['store_name'] = $store_name;
                    $seller[$i]['store_url'] = $store_url;
					$seller[$i]['featured'] = $isSellerFeatured;
                    $seller[$i]['address'] = isset( $store_info['address'] ) ? esc_attr( $store_info['address'] ) : 'N/A';;
                    $locations = explode( ',', $map_location );
                    $seller[$i]['lat'] = (isset($locations[0]) && $locations[0]!='') ? $locations[0] : '0';
                    $seller[$i]['long'] = isset($locations[1] ) ? $locations[1] : '0';
                }
				$i++;
            }
        }
    ?>
<div class="container content-wrap">
	<div class="row">
		<?php // get_sidebar( 'home-2' ); ?>
    	<div class="home-content-area col-md-12">
        	<div class="site-content">
				<?php if ( function_exists( 'dokan_get_best_selling_products' ) ) { ?>
                    <?php if ( get_theme_mod( 'show_best_selling', 'on' ) == 'on' ) { ?>
                        <div class="slider-container woocommerce">
                            <h2 class="slider-heading"><?php _e( 'Best Selling Products', 'dokan' ); ?></h2>
        
                            <div class="product-sliders">
                                <ul class="slides">
                                    <?php
                                    $best_selling_query = dokan_get_best_selling_products();
                                    ?>
                                    <?php while ( $best_selling_query->have_posts() ) : $best_selling_query->the_post(); ?>
        
                                        <?php wc_get_template_part( 'content', 'product' ); ?>
        
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        </div> <!-- .slider-container -->
                    <?php } ?>
                <?php } ?>
        
                <?php if ( function_exists( 'dokan_get_top_rated_products' ) ) { ?>
                    <?php if ( get_theme_mod( 'show_top_rated', 'on' ) == 'on' ) { ?>
                        <div class="slider-container woocommerce">
                            <h2 class="slider-heading"><?php _e( 'Top Rated Products', 'dokan' ); ?></h2>
        
                            <div class="product-sliders">
                                <ul class="slides">
                                    <?php
                                    $top_rated_query = dokan_get_top_rated_products();
                                    ?>
                                    <?php while ( $top_rated_query->have_posts() ) : $top_rated_query->the_post(); ?>
        
                                        <?php wc_get_template_part( 'content', 'product' ); ?>
        
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        </div> <!-- .slider-container -->
                    <?php } ?>
                <?php } ?>
            </div><!-- .site-content -->
        </div><!-- .home-content-area col-md-9 -->
    </div><!-- .row -->
</div><!-- .container -->
<div class="worldwide-sellers-store-locator slider-container">
         <!--<h2 class="slider-heading">Stores</h2>-->
    <div id="dokan-store-location" style="height:400px;"></div>
</div>
<?php // include_once("googleMaps.php"); ?>
<div id="user-location-current"></div>

<script type="text/javascript">
	initialize();
	var markers = new Array();
	var sellers = [];
	var m1;
	sellers = <?php echo json_encode($seller);?>;
	var gmap;
	
	$map_area = jQuery('#dokan-store-location');
	gmap = new google.maps.Map( $map_area[0], mapOptions);
	for(i=0;i<sellers.length;i++)
	{
		var seller=sellers[i];
		var curpoint = new google.maps.LatLng(seller.lat, seller.long)
		addMarker(curpoint,seller.store_url,seller.store_name, seller.address, seller.featured);
			
	}
	
	//console.log(console.log('Shyam***'+m1.getPosition()));
	gmap.setCenter(m1.getPosition());
	gmap.setZoom(10);
	 
	jQuery(function($) 
	{
		console.log(markers);	 
		var ind=0;
		for (var j = 0; j < markers.length; j++) 
		{
			setTimeout(function(){
				markers[ind++].setMap(gmap);
			},j* 80);
		}
	})
	
	function addMarker(loc, url, tit, addr, featured) 
	{
		if(featured)
		{
			custIcon= '<?php echo get_stylesheet_directory_uri(); ?>/assets/images/liquor.png';
		}
		else{
			custIcon= '<?php echo get_stylesheet_directory_uri(); ?>/assets/images/liquor.png';
		}
		m1 = new google.maps.Marker({
			position: loc,
			map: gmap,
			icon:custIcon,
			draggable: false,
			
			animation: google.maps.Animation.DROP,
			title:tit
		});
		addInfoWindow(m1, '<div>Store Name:'+tit+'</div><div>Address:'+addr+'</div><div><a href="'+url+'">Visit Store</a></div>');
		markers.push(m1);
	}
	
	function addInfoWindow(marker, message) {
		var infoWindow = new google.maps.InfoWindow({
			content: message
		});
	
		google.maps.event.addListener(marker, 'click', function () {
			infoWindow.open(gmap, marker);
		});
	}
</script>
</div><!-- #main .site-main -->

<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="footer-widget-area">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <?php dynamic_sidebar( 'footer-1' ); ?>
                </div>

                <div class="col-md-3">
                    <?php dynamic_sidebar( 'footer-2' ); ?>
                </div>

                <div class="col-md-3">
                    <?php dynamic_sidebar( 'footer-3' ); ?>
                </div>

                <div class="col-md-3">
                    <?php //dynamic_sidebar( 'footer-4' ); ?>
                </div>
            </div> <!-- .footer-widget-area -->
        </div>
    </div>

    <div class="copy-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12 footer-section">
                
               <!-----------------------Footer Section---------------------------------------->
                
                <div class="footer-copy">
                    
                    <div class="footer-main col-md-12">
                        <div class="footer-mid-content col-md-2 col-sm-2">
                            <p>Footer<span>News</span></p>
                                <ul class="footer-ul">
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/top-products/">Top Products</a></li>
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/policy-regulations/">Policy & Regulations</a></li>
                                    <li class="footer-li"><a href="#">Submit</a></li>
                                    <li class="footer-li"><a href="#">Sitemap</a></li>
                                </ul>
                        </div>
                        <div class="footer-mid-content col-md-2 col-sm-2">
                            <p>About<span>Us</span></p>
                                <ul class="footer-ul">
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/who-we-are/">Who We Are</a></li>
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/company-policy/">Company Policy</a></li>
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/certificates/">Certificates</a></li>
                                    <li class="footer-li"><a href="#">Sitemap</a></li>
                                </ul>
                        </div>
                        <div class="footer-mid-content col-md-2 col-sm-2">
                            <p>Get<span>Support</span></p>
                                <ul class="footer-ul">
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/top-products/">Top Products</a></li>
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/support-center/">Support Center</a></li>
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/policy-regulations/">Policy & Regulations</a></li>
                                    <li class="footer-li"><a href="#">Submit</a></li>
                                </ul>
                        </div>
                        <div class="footer-mid-content col-md-2 col-sm-2">
                            <p>Ordering<span>Menu</span></p>
                                <ul class="footer-ul">
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/who-we-are/">Who We Are</a></li>
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/company-policy/">Company Policy</a></li>
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/certificates/">Certificates</a></li>
                                   
                                </ul>
                        </div>
                        <div class="footer-mid-content col-md-2 col-sm-2">
                            <p>Subscribe<span>Us</span></p>
                                <ul class="footer-ul">
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/top-products/">Top Products</a></li>
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/support-center/">Support Center</a></li>
                                    <li class="footer-li"><a href="<?php echo site_url(); ?>/policy-regulations/">Policy & Regulations</a></li>
                                   
                                </ul>
                        </div>
                        <div class="footer-mid-content col-md-2 col-sm-2">
                          <?php dynamic_sidebar( 'Footer Sidebar-4' ); ?>
                        </div>
                     
                  </div>
                    
                    <div class="footer-content-2 col-md-12">
                        <div class="footer-social-links">
                        <a href="https://www.facebook.com/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/Facebook.png" alt="facebook"/></a>
                        <a href="https://twitter.com/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/Twitter.png" alt="twitter"/></a>
                        <a href="https://www.pinterest.com/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/Pinterst.png" alt="Pinterst"/></a>
                        <a href="https://www.linkedin.com/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/LinkedIn.png" alt="linked"/></a>
                        <a href="https://accounts.google.com/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/Google_Plus.png" alt="google plus"/></a>
                        </div>
                        <div class="footer-content-links">
                       
                        <div class="footer-first-content">                       
                            <ul class="new-ul-link">
                                <p><a href="<?php echo site_url(); ?>/browse-alphabetically/">Browse Alphabetically</a></p>
                                <li class="new-li"><a href="<?php echo site_url(); ?>/showroom/">Showroom</a></li>
                                <li class="new-li"><a href="<?php echo site_url(); ?>/country-search/">Country Search</a></li>
                                <li class="new-li"><a href="<?php echo site_url(); ?>/manufacturer/">Manufacturer</a></li>
                                <li class="new-li"><a href="<?php echo site_url(); ?>/supplier/">Supplier</a></li>
                                <li class="new-li"><a href="<?php echo site_url(); ?>/production/">Production</a></li>
                            </ul> 
                        </div> 
                        <div class="footer-second-content">
                            <ul class="second-new-ul">
                                <li class="second-new-li"><a href="<?php echo site_url(); ?>/product-listing-policy/">Product Listiing Policy</a></li>
                                <li class="second-new-li"><a href="<?php echo site_url(); ?>/intellectual-property-policy-and-management-center/">Iintellectual Property Policy and management center</a></li>
                                <li class="second-new-li"><a href="<?php echo site_url(); ?>/privacy-policy/">Privacy Policy</a></li>
                                <li class="second-new-li"><a href="<?php echo site_url(); ?>/terms-of-use/">Terms of use</a></li>
                            </ul>
                        </div>
                        </div>
                        <div class="links-footer">
                        <a href="#" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/paypal.png" alt="paypal"/></a>
                        <a href="#" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/money-booker.png" alt="money-booker"/></a>
                        <a href="#" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/visa.png" alt="visa"/></a>
                        <a href="#" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/cirrus.png" alt="cirrus"/></a>
                        <a href="#" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/discover.png" alt="discover"/></a>
                        <a href="#" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/amejan.png" alt="amejan"/></a>
                        <a href="#" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/mastercard.png" alt="mastercard"/></a>
                        <a href="#" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/2ccc.png" alt="2ccc"/></a>
                        </div>
                  
                        <div class="col-md-6 site-info">
                            <?php
                            $footer_text = get_theme_mod( 'footer_text' );

                            if ( empty( $footer_text ) ) {
                                printf( __( '&copy; %d, %s. All rights are reserved.', 'dokan' ), date( 'Y' ), get_bloginfo( 'name' ) );
                               // printf( __( 'Powered by <a href="%s" target="_blank">Dokan</a> from <a href="%s" target="_blank">weDevs</a>', 'dokan' ), esc_url( 'http://wedevs.com/theme/dokan/?utm_source=dokan&utm_medium=theme_footer&utm_campaign=product' ), esc_url( 'http://wedevs.com/?utm_source=dokan&utm_medium=theme_footer&utm_campaign=product' ) );
                            } else {
                                echo $footer_text;
                            }
                            ?>
                        </div>
                        <div class="bottom-icon"><a href="#" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/icon.png" alt="Bottom-Icon"/></a></div>
                       
                        </div>
                     </div>
                        
                        
                
                <!------------------------Footer Section End------------------------------------------------>
                
                
                    <!--<div class="footer-copy">-->
                        <!--<div class="col-md-6 site-info">
                            <?php
                            /*$footer_text = get_theme_mod( 'footer_text' );

                            if ( empty( $footer_text ) ) {
                                printf( __( '&copy; %d, %s. All rights are reserved.', 'dokan' ), date( 'Y' ), get_bloginfo( 'name' ) );
                                printf( __( 'Powered by <a href="%s" target="_blank">Dokan</a> from <a href="%s" target="_blank">weDevs</a>', 'dokan' ), esc_url( 'http://wedevs.com/theme/dokan/?utm_source=dokan&utm_medium=theme_footer&utm_campaign=product' ), esc_url( 'http://wedevs.com/?utm_source=dokan&utm_medium=theme_footer&utm_campaign=product' ) );
                            } else {
                                echo $footer_text;
                            }*/
                            ?>
                        </div>--><!-- .site-info -->

                        <div class="col-md-6 footer-gateway" style="display:none;">
                            <?php
                                wp_nav_menu( array(
                                    'theme_location'  => 'footer',
                                    'depth'           => 1,
                                    'container_class' => 'footer-menu-container clearfix',
                                    'menu_class'      => 'menu list-inline pull-right',
                                ) );
                            ?>
                        </div>
                    <!--</div>-->
                </div>
            </div><!-- .row -->
        </div><!-- .container -->
    </div> <!-- .copy-container -->
</footer><!-- #colophon .site-footer -->
</div><!-- #page .hfeed .site -->

<?php wp_footer(); ?>
<?php echo do_shortcode('[wp-disclaimer id="465"]'); ?>
<div id="yith-wcwl-popup-message" style="display:none;"><div id="yith-wcwl-message"></div></div>
</body>
</html>