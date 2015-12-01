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
                        
                       
                    
                    
                        <!-- .site-info -->

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
                    </div>
                </div>
            </div><!-- .row -->
        </div><!-- .container -->
    </div> <!-- .copy-container -->
</footer><!-- #colophon .site-footer -->
</div><!-- #page .hfeed .site -->

<?php wp_footer(); ?>
<?php echo do_shortcode('[wp-disclaimer id="465"]'); ?>
<div id="yith-wcwl-popup-message" style="display:none;"><div id="yith-wcwl-message"></div></div>
<script type="text/javascript">
function checkHtttpUrl()
{
	jQuery("form .dokan-w5 .dokan-input-group input[type='text']").each(function() {
		var val= jQuery(this).val();
		if (val && !val.match(/^http([s]?):\/\/.*/)) {
		 jQuery(this).val('http://' + val);
	   }
	});
}
jQuery(document).ready(function(e) {
    jQuery("select.orderby option[value='popularity']").remove();
});
</script>
</body>
</html>