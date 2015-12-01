<?php
/**
 * The Template for displaying all single posts.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

$store_user = get_userdata( get_query_var( 'author' ) );
$store_info = dokan_get_store_info( $store_user->ID );
$map_location = isset( $store_info['location'] ) ? esc_attr( $store_info['location'] ) : '';

$scheme = is_ssl() ? 'https' : 'http';
wp_enqueue_script( 'google-maps', $scheme . '://maps.google.com/maps/api/js?sensor=true' );

get_header();
?>

<?php $url = get_template_directory_uri() . '/assets/images/footer'; ?>


<div id="primary" class="content-area dokan-single-store col-md-12">
    <div id="content" class="site-content store-page-wrap woocommerce" role="main">

        <div class="profile-frame">

            <?php if ( isset( $store_info['banner'] ) && !empty( $store_info['banner'] ) ) { ?>
            <style type="text/css">
                .profile-frame {
                    background-image: url('<?php echo wp_get_attachment_url( $store_info['banner'] ); ?>');
                }
            </style>
            <?php } ?>

            <div class="col-md-4 profile-info-box">
                <div class="profile-img">
                    <?php echo get_avatar( $store_user->ID, 80 ); ?>
                </div>

                <div class="profile-info">
                    <ul class="list-unstyled">

                        <?php if ( isset( $store_info['store_name'] ) ) { ?>
                            <li class="store-name"><?php echo esc_html( $store_info['store_name'] ); ?></li>
                        <?php } ?>

                        <?php if ( isset( $store_info['address'] ) && !empty( $store_info['address'] ) ) { ?>
                            <li><i class="fa fa-map-marker"></i> <?php echo esc_html( $store_info['address'] ); ?></li>
                        <?php } ?>

                        <?php if ( isset( $store_info['phone'] ) && !empty( $store_info['phone'] ) ) { ?>
                            <li><i class="fa fa-mobile"></i>
                                <a href="tel:<?php echo esc_html( $store_info['phone'] ); ?>"><?php echo esc_html( $store_info['phone'] ); ?></a>
                            </li>
                        <?php } ?>

                        <?php if ( isset( $store_info['show_email'] ) && $store_info['show_email'] == 'yes' ) { ?>
                            <li><i class="fa fa-envelope-o"></i>
                                <a href="mailto:<?php echo antispambot( $store_user->user_email ); ?>"><?php echo antispambot( $store_user->user_email ); ?></a>
                            </li>
                        <?php } ?>

                        <li>
                            <i class="fa fa-star"></i>
                            <?php dokan_get_readable_seller_rating( $store_user->ID ); ?>
                        </li>
                    </ul>

                    <ul class="list-inline store-social">
                        <?php if ( isset( $store_info['social']['fb'] ) && !empty( $store_info['social']['fb'] ) ) { ?>
                            <li>
                                <a href="<?php echo esc_url( $store_info['social']['fb'] ); ?>" target="_blank"><i class="fa fa-facebook-square"></i></a>
                            </li>
                        <?php } ?>

                        <?php if ( isset( $store_info['social']['gplus'] ) && !empty( $store_info['social']['gplus'] ) ) { ?>
                            <li>
                                <a href="<?php echo esc_url( $store_info['social']['gplus'] ); ?>" target="_blank"><i class="fa fa-google-plus-square"></i></a>
                            </li>
                        <?php } ?>

                        <?php if ( isset( $store_info['social']['twitter'] ) && !empty( $store_info['social']['twitter'] ) ) { ?>
                            <li>
                                <a href="<?php echo esc_url( $store_info['social']['twitter'] ); ?>" target="_blank"><i class="fa fa-twitter-square"></i></a>
                            </li>
                        <?php } ?>

                        <?php if ( isset( $store_info['social']['linkedin'] ) && !empty( $store_info['social']['linkedin'] ) ) { ?>
                            <li>
                                <a href="<?php echo esc_url( $store_info['social']['linkedin'] ); ?>" target="_blank"><i class="fa fa-linkedin-square"></i></a>
                            </li>
                        <?php } ?>

                        <?php if ( isset( $store_info['social']['youtube'] ) && !empty( $store_info['social']['youtube'] ) ) { ?>
                            <li>
                                <a href="<?php echo esc_url( $store_info['social']['youtube'] ); ?>" target="_blank"><i class="fa fa-youtube-square"></i></a>
                            </li>
                        <?php } ?>

                        <?php if ( isset( $store_info['social']['flickr'] ) && !empty( $store_info['social']['flickr'] ) ) { ?>
                            <li>
                                <a href="<?php echo esc_url( $store_info['social']['flickr'] ); ?>" target="_blank"><i class="fa fa-flickr"></i></a>
                            </li>
                        <?php } ?>

                        <?php if ( isset( $store_info['social']['instagram'] ) && !empty( $store_info['social']['instagram'] ) ) { ?>
                            <li>
                                <a href="<?php echo esc_url( $store_info['social']['instagram'] ); ?>" target="_blank"><i class="fa fa-instagram"></i></a>
                            </li>
                        <?php } ?>

                    </ul>
                </div> <!-- .profile-info -->
            </div> <!-- .profile-info-box -->
        </div> <!-- .profile-frame -->

        <?php do_action( 'dokan_store_profile_frame_after', $store_user, $store_info ); ?>
        
        <div class="storeWiseProduct">
          <h2 class="slider-heading">Search
            <form role="search" method="get" id="quick_search" class="woocommerce-product-search">
        	  <input type="search" class="search-field" placeholder="Enter Product Keyword" value="<?php echo ((isset($_REQUEST['s']) && $_REQUEST['s']!=''))?$_REQUEST['s']:''; ?>" name="s" title="Search for:">
            </form>
           </h2>
        </div>

        <?php if ( have_posts() ) { ?>

            <div class="seller-items">

                <?php woocommerce_product_loop_start(); ?>

                    <?php while ( have_posts() ) : the_post(); ?>

                        <?php wc_get_template_part( 'content', 'product' ); ?>

                    <?php endwhile; // end of the loop. ?>

                <?php woocommerce_product_loop_end(); ?>

            </div>

            <?php dokan_content_nav( 'nav-below' ); ?>

        <?php } else { ?>

            <p class="dokan-info"><?php _e( 'No products were found of this seller!', 'dokan' ); ?></p>

        <?php } ?>

    </div><!-- #content .site-content -->
</div><!-- #primary .content-area -->
<?php get_sidebar( 'store' ); ?>
<script type="text/javascript">
jQuery(document).ready(function(e) {
    var yLoc= '<div class="product"><input type="text" class="search-field"  placeholder="Zip…/ Address" value="" id="zipcode" name="zipcode" onchange="javascript: jQuery(\'#daddr\').val(this.value);"><input type="hidden" name="saddr" id="saddr" value="<?php echo $map_location; ?>"><input type="hidden" name="daddr" id="daddr" value=""><br><a href="javascript:void(0);" onclick="javascript: findLoc();" >Find Distance</a></div>';
	jQuery('.dokan-store .location-container').append(yLoc);
});
function findLoc(){
    var sLoc= jQuery('#saddr').val();
	var dLoc= jQuery('#daddr').val();
	if(dLoc)
	{
	  	window.open('https://maps.google.com?saddr='+sLoc+'&daddr='+dLoc+'', '_blank');
	}else{
		alert('Please enter your location zipcode to find the distance from the store');
	}
}
</script>
<?php get_footer(); ?>