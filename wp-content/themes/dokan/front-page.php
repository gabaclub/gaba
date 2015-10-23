<?php
/**
 * The main template file for homepage.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */
get_header( 'home' );
?>

<div id="primary" class="home-content-area col-md-9">
    <div id="content" class="site-content" role="main">

        <?php do_action( 'dokan_home_slider_top' ); ?>

        <div class="row">
            <?php /*?><div class="col-md-4">
                <?php dokan_category_widget(); ?>
            </div><?php */?>

            <div class="col-md-8">
                <?php
                 if ( get_theme_mod( 'show_slider', 'on' ) == 'on' ) {
                    $slider_id = get_theme_mod( 'slider_id', '-1' );

                    if ( $slider_id != '-1' ) {
                        Dokan_Slider::init()->get_slider( $slider_id );
                    }

                    do_action( 'dokan_home_on_slider' );
                }
                ?>
            </div>
        </div> <!-- #home-page-section-1 -->

        <?php do_action( 'dokan_home_after_slider' ); ?>

        <?php if ( function_exists( 'dokan_get_featured_products' ) ) { ?>
            <?php if ( get_theme_mod( 'show_featured', 'on' ) == 'on' ) { ?>
                <div class="slider-container woocommerce">
                    <h2 class="slider-heading"><?php _e( 'Featured Products', 'dokan' ); ?></h2>

                    <div class="product-sliders">
                        <ul class="slides">
                            <?php
                            $featured_query = dokan_get_featured_products();
                            ?>
                            <?php while ( $featured_query->have_posts() ) : $featured_query->the_post(); ?>

                                <?php wc_get_template_part( 'content', 'product' ); ?>

                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div> <!-- .slider-container -->
            <?php } ?>
        <?php } ?>

        <?php do_action( 'dokan_home_after_featured' ); ?>

        <?php if ( function_exists( 'dokan_get_latest_products' ) ) {
            $show_latest = get_theme_mod( 'show_latest_pro', 'on' );
            if ( $show_latest === true || $show_latest == 'on' ) {
                ?>
                <div class="slider-container woocommerce">
                    <h2 class="slider-heading"><?php _e( 'Latest Products', 'dokan' ); ?></h2>

                    <div class="product-sliders">
                        <ul class="slides">
                            <?php
                            $latest_query = dokan_get_latest_products();
                            ?>
                            <?php while ( $latest_query->have_posts() ) : $latest_query->the_post(); ?>

                                <?php wc_get_template_part( 'content', 'product' ); ?>

                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div> <!-- .slider-container -->
            <?php } ?>
        <?php } ?>

    </div><!-- #content .site-content -->
</div><!-- #primary .content-area -->
<?php get_sidebar( 'home' ); ?>
<?php get_footer( 'home' ); ?>