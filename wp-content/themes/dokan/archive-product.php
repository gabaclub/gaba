<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Dokan
 * @subpackage WooCommerce/Templates
 * @version 2.0.0
 */
get_header(); ?>

<?php get_sidebar( 'shop' ); 
?>
<div id="primary" class="content-area col-md-9">
    <div id="content" class="site-content" role="main">
	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action('woocommerce_before_main_content');
	?>

		<div class="archive-title clearfix">
		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

			<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>

		<?php endif; ?>

			<?php
				/**
				 * woocommerce_before_shop_loop hook
				 *
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				do_action( 'woocommerce_before_shop_loop' );
			?>

		</div>

		<?php do_action( 'woocommerce_archive_description' ); ?>
        
		<?php if ( have_posts() ) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php woocommerce_product_subcategories(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

			<?php
				/**
				 * woocommerce_after_shop_loop hook
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
			?>

		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

			<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>

	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action('woocommerce_after_main_content');
	?>
		<div class="store_locator">
			<h1 class="page-title">Store Locations:</h1>
			<?php echo do_shortcode('[SLPLUS addr="" center_map_at=""  hide_search_form="false"]'); ?>
         </div>
	</div><!-- #content .site-content -->
</div><!-- #primary .content-area -->
<script type="text/javascript">
jQuery(document).ready(function(e) {
	  var addr=  '<?php  if(isset($_GET['zipcode']) && $_GET['zipcode']!=''){ echo  $searchZip= $_GET['zipcode']; }  ?>';
		var radius=  '<?php  if(isset($_GET['radius']) && $_GET['radius']!=''){ echo  $searchRadius= $_GET['radius']; }  ?>';
				
    jQuery('#addressInput').val(addr);	
	jQuery("#radiusSelect option").each(function(){
		if (jQuery(this).val() == radius)
			jQuery(this).attr("selected","selected");
		});
	jQuery('#address_search').hide();
});
jQuery(window).load(function(e) {
    jQuery('#radius_in_submit').submit();
});
</script>
<?php get_footer(); ?>