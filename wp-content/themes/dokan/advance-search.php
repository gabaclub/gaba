<?php
/**
 * The Template for displaying a full width page.
 *
 * Template Name: Advanced Search
 *
 * @package dokan
 * @package dokan - 2013 1.0
 */
get_header();
?>

<div id="primary" class="content-area col-md-12">
    <div id="content" class="site-content" role="main">
    
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <h1 class="entry-title"><?php the_title(); ?></h1>
    </header><!-- .entry-header -->

    <div class="entry-content">
        <?php the_content(); ?>
        <div class="dokan-form-horizontal">
        
        
        <?php
              $bottle_size= array (''=> 'Select Size',
					50 => '50 ml',
					100 => '100 ml', 
					180 => '180 ml',	
					200 => '200 ml',
					300 => '300 ml',
					330 => '330 ml',
					350 => '350 ml',
					500 => '500 ml',
					700 => '700 ml',
					720 => '720 ml',
					1750 => '1750 ml (1.75L)');
				
					?>
          <div class="dokan-form-group">
            <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottle Size', 'dokan' ); ?>*</label>
            <div class="dokan-w6 dokan-text-left">                          
         <?php
                                    
            dokan_post_input_box( $post->ID, 'bottle_size', array('options' =>$bottle_size , 'value' => $sp_data['bottle_size'][0]),  'select' );
            
        ?>
            </div>
        </div>

      
        <?php
        $brands = array( 0=> 'Select Brand'); 
					$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 26 ORDER BY brand_title");	
						if(!empty($resBrands)){ 
							foreach($resBrands as $r) {	
								array_push($brands, $r->brand_title); 	
							}
						}
				?>
                 <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Select Brand', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">
                    <?php		
						
							dokan_post_input_box( $post->ID, 'brand', array('options' =>$brands, 'value' => $sp_data['brand'][0]), 'select' );
						?>
                        	</div>
                        </div>
                   <?php
                    $cats = array( 0=> 'Select Category'); 
						$resCats = $wpdb->get_results("SELECT * FROM custom_category WHERE category_id ORDER BY category_title");	
						if(!empty($resCats)){ 
     						foreach($resCats as $r) {	
								array_push($cats, $r->category_title); 	
							}
						}
						?>
                         <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Select Category', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'category', array('options' =>$cats, 'value' => $sp_data['category'][0]), 'select' );
						?>
						</div>
                     </div>    
            
              </div>      
		   
    </div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->

     <?php wp_link_pages( array('before' => '<div class="page-links">' . __( 'Pages:', 'dokan' ), 'after' => '</div>') ); ?>
     <?php //edit_post_link( __( 'Edit', 'dokan' ), '<span class="edit-link">', '</span>' ); ?>
    
   
    </div><!-- #content .site-content -->
</div><!-- #primary .content-area -->
<?php get_footer(); ?>