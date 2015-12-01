<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package dokan
 * @package dokan - 2013 1.0
 */
?>
<div id="secondary_sidebar_home" class="col-sm-12 col-md-12" role="complementary">
    <div class="widget-area col-sm-9 col-md-9">

        <?php do_action( 'before_sidebar' ); ?>
        <?php if ( !dynamic_sidebar( 'sidebar-home' ) ) : ?>

            <aside id="search" class="widget widget_search">
                <?php get_search_form(); ?>
            </aside>

            <aside id="archives" class="widget">
                <h1 class="widget-title"><?php _e( 'Archives', 'dokan' ); ?></h1>
                <ul>
                    <?php wp_get_archives( array('type' => 'monthly') ); ?>
                </ul>
            </aside>

            <aside id="meta" class="widget">
                <h1 class="widget-title"><?php _e( 'Meta', 'dokan' ); ?></h1>
                <ul>
                    <?php wp_register(); ?>
                    <li><?php wp_loginout(); ?></li>
                    <?php wp_meta(); ?>
                </ul>
            </aside>

        <?php endif; // end sidebar widget area ?>
        
       <!-- <aside id="product-category" class="widget">
                <?php // dokan_category_widget(); ?>
         </aside>-->
        </div>
         <div class="col-sm-3 col-md-3" >
            <aside id="genMap" class="widget">
                         <div id="mapBox" ><iframe src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d24778078.7665682!2d-73.979681!3d40.703313!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2s!4v1446114874421" width="100%" height="195" frameborder="0" style="border:0" allowfullscreen></iframe></div>
                         <label class="screen-reader-text" for="s"><a href="javascript: void(0);" style="font-size:12px; float:right; text-decoration:underline; " onclick="findCurLoc();"><i>Locate me</i></a></label>
            </aside>
		</div>
</div><!-- #secondary .widget-area -->
         
