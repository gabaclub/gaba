<?php
/**
 * Dokan Shipping Class
 *
 * @author weDves
 */

class Dokan_Template_Specifications {

    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Template_Specifications();
        }

        return $instance;
    }

    public function __construct() {

        //add_action( 'woocommerce_shipping_init', array($this, 'include_shipping' ) );
        //add_action( 'woocommerce_shipping_methods', array($this, 'register_shipping' ) );
        //add_action( 'woocommerce_product_tabs', array($this, 'register_product_tab' ) );
        //add_action( 'woocommerce_after_checkout_validation', array($this, 'validate_country' ) );
    }
	
	public function render_specification($prodId, $catName='')
{
	global $wpdb;
	$sp_data= array();
	?>
<div class="dokan-form-horizontal">

<?php
			if(isset($catName) && $catName!='')
			{
				$passCat = $catName;
			}else{
				$category = get_the_terms($post->ID, 'product_cat');
				$passCat = $category[0]->name;
				$sp_data = maybe_unserialize( get_post_meta($prodId) );
			}
			
			switch($passCat)
			{
			
				case 'Anise':
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
                		<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottle Size', 'dokan' ); ?></label>
                		<div class="dokan-w6 dokan-text-left">                          
                     <?php
												
						dokan_post_input_box( $post->ID, 'bottle_size', array('options' =>$bottle_size , 'value' => $sp_data['bottle_size'][0]),  'select' );
						
					?>
                    	</div>
                    </div>
                    
                    <?php								
								
						$brands = array( 0=> 'Select Brand');
						$bId = array(''); 
						$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 26 ORDER BY brand_title");	
							if(!empty($resBrands)){ 
								foreach($resBrands as $r) {	
									array_push($brands, $r->brand_title);
									array_push($bId, $r->id);
								}
									$jointBrand= array_combine($bId, $brands);
							}
					?>
                 <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brand', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">
                    <?php		
						
							dokan_post_input_box( $post->ID, 'brand', array('options' =>$jointBrand, 'value' => $sp_data['brand'][0]), 'select' );
						?>
                        	</div>
                        </div>
                       
                        <?php
			
						$cats = array( 0=> 'Select Category');
						$cId = array(''); 
						$resCats = $wpdb->get_results("SELECT * FROM custom_category WHERE category_id = 26 ORDER BY category_title");	
						if(!empty($resCats)){ 
     						foreach($resCats as $r) {	
								array_push($cats, $r->category_title);
								array_push($cId, $r->id); 	
							}
								$jointCat= array_combine($cId, $cats);
						}
						?>
                         <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Category', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'category', array('options' =>$jointCat, 'value' => $sp_data['category'][0]), 'select' );
						?>
						</div>
                     </div>
                     	<div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Distillery', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
						<?php
							dokan_post_input_box( $post->ID, 'distillery', array(), 'text' );
						?>
                    	</div>
                     </div>
                    <?php
					break;
				case 'Beer':
						$bottle_size= array (''=> 'Select Size',
											 '010473'=>'1 × Can 473 ml'	,
											 '060355'=>'6 × Can 355 ml',
											 '120355'=>'12 × Can 355 ml',
											 '240355'=>'24 × Can 355 ml',
											 '240473'=>'24 × Can 473 ml',
											 '480355'=>'48 × Can 355 ml',
											 '010710'=>'1 × Bottle 710 ml',
											 '060207'=>'6 × Bottle 207 ml',
											 '060330'=>'6 × Bottle 330 ml',
											 '120207'=>'12 × Bottle 207 ml',
											 '120330'=>'12 × Bottle 330 ml',
											 '120710'=>'12 × Bottle 710 ml',
											 '180330'=>'18 × Bottle 330 ml',
											 '240207'=>'24 × Bottle 207 ml',
											 '240330'=>'24 × Bottle 330 ml',
											 '360330'=>'36 × Bottle 330 ml');
				?>
                      <div class="dokan-form-group">
                		<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottle Size', 'dokan' ); ?></label>
                		<div class="dokan-w6 dokan-text-left">                          
                <?php
					dokan_post_input_box( $post->ID, 'bottle_size', array('options' =>$bottle_size , 'value' => $sp_data['bottle_size'][0]),  'select' );
				?>
                    	</div>
                    </div>                   
                    <?php							
						$brands = array( 0=> 'Select Brand');
						$bId = array(''); 
						$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 36 ORDER BY brand_title");	
							if(!empty($resBrands)){ 
								foreach($resBrands as $r) {	
									array_push($brands, $r->brand_title);
									array_push($bId, $r->id);
								}
									$jointBrand= array_combine($bId, $brands);
							}
					?>
                 <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brand', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">
                    <?php		
							dokan_post_input_box( $post->ID, 'brand', array('options' =>$jointBrand, 'value' => $sp_data['brand'][0]), 'select' );
						?>
                        	</div>
                        </div>
                       
                        <?php
			
						$cats = array( 0=> 'Select Category');
						$cId = array(''); 
						$resCats = $wpdb->get_results("SELECT * FROM custom_category WHERE category_id = 36 ORDER BY category_title");	
						if(!empty($resCats)){ 
     						foreach($resCats as $r) {	
								array_push($cats, $r->category_title);
								array_push($cId, $r->id); 	
							}
								$jointCat= array_combine($cId, $cats);
						}
						?>
                         <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Category', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'category', array('options' =>$jointCat, 'value' => $sp_data['category'][0]), 'select' );
						?>
						</div>
                     </div>
                     <?php			
							$brewery= array(''=>'Select Brewery');
					?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brewery', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php						
					 dokan_post_input_box( $post->ID, 'brewery', array('options' =>$brewery, 'value' => $sp_data['brewery'][0]), 'select' );						
				    ?>
                    </div>
                    </div>
                    <?php			
							$brew_loc= array(''=>'Select Brewery Location',
												 1=>'England',
												 2=>'Ireland',
												 3=>'Scotland',
												 4=>'Wales',
												 5=>'Germany',
												 6=>'Checkoslavakia',
												 7=>'Usa',
												 8=>'India'); 
					?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brewery Location', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php						
					 dokan_post_input_box( $post->ID, 'brew_loc', array('options' =>$brew_loc, 'value' => $sp_data['brew_loc'][0]), 'select' );						
				    ?>
                    </div>
                    </div>
                     <?php
								$attribute= array(''=>'Select Attribute', 1=>'Premium',2=>'Imported', 3=>'Craft');
				     ?>
                     <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Attribute', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php							
				  		dokan_post_input_box( $post->ID, 'attribute', array('options' =>$attribute, 'value' => $sp_data['attribute'][0]), 'select' );							
			        ?>
			        </div>
                    </div>
                     <?php
								$packaging= array(''=>'Select Packaging', 1=>'Bottle',2=>'Can', 3=>'Keg');
				     ?>
                     <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Packaging', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php							
				  		dokan_post_input_box( $post->ID, 'packaging', array('options' =>$packaging, 'value' => $sp_data['packaging'][0]), 'select' );							
			        ?>
			        </div>
                    </div>
                <?php
					break;
				case 'Brandy':
						?>
                        
                        <?php
						$bottle_size= array (''=> 'Select Size', 
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
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottle Size', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
                         dokan_post_input_box( $post->ID, 'bottle_size', array('options' =>$bottle_size, 'value' => $sp_data['bottle_size'][0]), 'select' );
						?>
                        	</div>
                        </div>
                       
                        <?php
						$brands = array( 0=> 'Select Brand');
						$bId = array('');
						$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 28 ORDER BY brand_title");	
						if(!empty($resBrands)){ 
     						foreach($resBrands as $r){	
								array_push($brands, $r->brand_title);
								array_push($bId, $r->id);
							}
								$jointBrand= array_combine($bId, $brands);
						}
						?>
                     <div class="dokan-form-group">
                        <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brand', 'dokan' ); ?></label>
                        <div class="dokan-w6 dokan-text-left">
                        <?php				
					dokan_post_input_box( $post->ID, 'brand', array('options' =>$jointBrand, 'value' => $sp_data['brand'][0]), 'select' );
						?>
                        </div>
                        </div>
                       
                        <?php
						$flavour = array( 0=> 'Select Flavour');
						$fId = array('');
						$resFlavour = $wpdb->get_results("SELECT * FROM custom_flavour WHERE category_id = 28 ORDER BY flavour_title");	
						if(!empty($resFlavour)){ 
     						foreach($resFlavour as $r){	
								array_push($flavour, $r->flavour_title);
								array_push($fId, $r->id);	
							}
								$jointFlavour= array_combine($fId, $flavour);
						}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Flavour', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left"> 
                        <?php
					dokan_post_input_box( $post->ID, 'flavour', array('options' =>$jointFlavour, 'value' => $sp_data['flavour'][0]), 'select' );		
						?>
                        </div>
                        </div>
                        
						<?php
					 	$cats = array( 0=> 'Select Category');
						$cId = array('');
						$resCats = $wpdb->get_results("SELECT * FROM custom_category WHERE category_id = 28 ORDER BY category_title");	
						if(!empty($resCats)){ 
     						foreach($resCats as $r) {	
								array_push($cats, $r->category_title);
								array_push($cId, $r->id);	
							}
								$jointCat= array_combine($cId, $cats);
						}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Category', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
						
					dokan_post_input_box( $post->ID, 'category', array('options' =>$jointCat, 'value' => $sp_data['category'][0]), 'select' );
						?>
						</div>
                        </div>
                        <div class="dokan-form-group">
                   			 <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Distillery', 'dokan' ); ?></label>
                    			<div class="dokan-w6 dokan-text-left"> 
					<?php		
                       dokan_post_input_box( $post->ID, 'distillery', array('options' =>array(''=>'Select Distillery'), 'value' => $sp_data['distillery'][0]), 'select' );		
                    ?>		
                   			 </div>
                   		 </div>
                        
                        <?php
					 	$mfg_country= array( 0=> 'Select Country');
						$mId = array(''); 
						$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
						if(!empty($resMfgCountry)){ 
     						foreach($resMfgCountry as $r) {	
								array_push($mfg_country, $r->country_title);
								array_push($mId, $r->id); 	
							}
								$jointCountry= array_combine($mId, $mfg_country);
						}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Country(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_country', array('options' =>$jointCountry, 'value' => $sp_data['mfg_country'][0]), 'select' );
						?>
						</div>
                        </div>
                        
                        <?php
					 	$mfg_region= array( 0=> 'Select Region'); 
						$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region ORDER BY region_title");
						if(!empty($resMfgRegion)){ 
     						foreach($resMfgRegion as $r) {	
								array_push($mfg_region, $r->region_title); 	
							}
						}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Region(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_region', array('options' =>$mfg_region, 'value' => $sp_data['mfg_region'][0]), 'select' );
						?>
						</div>
                        </div>
                        
						<?php			
						$fruits= array('' => 'Select Fruit','apple'=>'Apple', 'apricot'=>'Apricot', 'blueberry'=>'Blueberry', 'cherry'=>'Cherry', 'peach'=>'Peach');?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Fruits', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
					<?php
					dokan_post_input_box( $post->ID, 'fruit', array('options' =>$fruits, 'value' => $sp_data['fruit'][0]), 'select' );
						?>
                        </div>
                        </div>       
                <?php   
					break;
				case 'Gin':
						$brands = array( 0=> 'Select Brand');
						$bId = array('');
						$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 29 ORDER BY brand_title");	
						if(!empty($resBrands)){ 
     						foreach($resBrands as $r) {	
								array_push($brands, $r->brand_title);
								array_push($bId, $r->id); 	
							}
								$jointBrand= array_combine($bId, $brands);
						}
				?>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brand', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">
                <?php
					dokan_post_input_box( $post->ID, 'brand', array('options' =>$jointBrand, 'value' => $sp_data['brand'][0]), 'select' );
				?>
                </div>
                </div>
                <?php
					 	$mfg_country= array( 0=> 'Select Country');
						$mId = array(''); 
						$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
						if(!empty($resMfgCountry)){ 
     						foreach($resMfgCountry as $r) {	
								array_push($mfg_country, $r->country_title);
								array_push($mId, $r->id); 	
							}
								$jointCountry= array_combine($mId, $mfg_country);
						}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Country(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_country', array('options' =>$jointCountry, 'value' => $sp_data['mfg_country'][0]), 'select' );
						?>
						</div>
                        </div>
                        
                        <?php
					 	$mfg_region= array( 0=> 'Select Region'); 
						$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region ORDER BY region_title");
						if(!empty($resMfgRegion)){ 
     						foreach($resMfgRegion as $r) {	
								array_push($mfg_region, $r->region_title); 	
							}
						}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Region(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_region', array('options' =>$mfg_region, 'value' => $sp_data['mfg_region'][0]), 'select' );
						?>
						</div>
                        </div>
                <?php				
								$style= array (''=> 'Select Style', 
												1 => 'Distilled Gin',
												2 => 'Gin',	
												3 => 'Juniper Flavored Spirit',		
												4 => 'London Gin');
				?>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Select Style', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">
                <?php								
												
					dokan_post_input_box( $post->ID, 'style', array('options' =>$style, 'value' => $sp_data['style'][0]), 'select' );
			    ?>	
                </div>
                </div>				
		        <?php				$production= array (''=> 'Select Production Method', 
                                            1 => 'Column Distilled Gin',	
                                            2 => 'Compound Gin',		
                                            3 => 'Post distilled gin');
					?>
                    <div class="dokan-form-group">
                        <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Production', 'dokan' ); ?></label>
                        <div class="dokan-w6 dokan-text-left">
                    <?Php								
					dokan_post_input_box( $post->ID, 'production', array('options' =>$production, 'value' => $sp_data['production'][0]), 'select' );
					?>
                    </div>
                    </div>
                    <?php			
					break;
				case 'Liquor':
						
						$bottle_size= array (''=> 'Select Size', 
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
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottle_size', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">
                    <?php
					   dokan_post_input_box( $post->ID, 'bottle_size', array('options' =>$bottle_size, 'value' => $sp_data['bottle_size'][0]), 'select' );
					?>
                    </div>
                    </div>								
				    <?php
						$brands = array( 0=> 'Select Brand');
						$bId = array('');
						$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 11 ORDER BY brand_title");	
						if(!empty($resBrands)){ 
     						foreach($resBrands as $r) {	
								array_push($brands, $r->brand_title);
								array_push($bId, $r->id); 	
							}
								$jointBrand= array_combine($bId, $brands);
						}
					?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brand', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">
                    <?php	
						dokan_post_input_box( $post->ID, 'brand', array('options' =>$jointBrand, 'value' => $sp_data['brand'][0]), 'select' );				
			        ?>
                    </div>
                    </div>
                    <?php
								
						 $cats = array( 0=> 'Select Category');
						 $cId = array('');
						$resCats = $wpdb->get_results("SELECT * FROM custom_category WHERE category_id = 11 ORDER BY category_title");	
						if(!empty($resCats)){ 
     						foreach($resCats as $r) {	
								array_push($cats, $r->category_title);
								array_push($cId, $r->id); 	
							}
								$jointCat= array_combine($cId, $cats);
						}
				  ?>
                   <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Category', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">
                  <?php		
						dokan_post_input_box( $post->ID, 'category', array('options' =>$jointCat, 'value' => $sp_data['category'][0]), 'select' );
			      ?>
                  
                  </div>
                  </div>
                   <div class="dokan-form-group">
                   			 <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Distillery', 'dokan' ); ?></label>
                    			<div class="dokan-w6 dokan-text-left"> 
					<?php		
                       dokan_post_input_box( $post->ID, 'distillery', array('options' =>array(''=>'Select Distillery'), 'value' => $sp_data['distillery'][0]), 'select' );		
                    ?>		
                   			 </div>
                   		 </div>
                         
                    <?php
					 	$mfg_country= array( 0=> 'Select Country');
						$mId = array(''); 
						$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
						if(!empty($resMfgCountry)){ 
     						foreach($resMfgCountry as $r) {	
								array_push($mfg_country, $r->country_title);
								array_push($mId, $r->id); 	
							}
								$jointCountry= array_combine($mId, $mfg_country);
						}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Country(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_country', array('options' =>$jointCountry, 'value' => $sp_data['mfg_country'][0]), 'select' );
						?>
						</div>
                        </div>
                        
                        <?php
								$mfg_region= array( 0=> 'Select Region'); 
								$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region ORDER BY region_title");
								if(!empty($resMfgRegion)){ 
									foreach($resMfgRegion as $r) {	
										array_push($mfg_region, $r->region_title); 	
									}
								}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Region(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_region', array('options' =>$mfg_region, 'value' => $sp_data['mfg_region'][0]), 'select' );
						?>
						</div>
                        </div>
                  <?php
						
						$flavour = array( 0=> 'Select Flavour');
						$fId = array('');
						$resFlavour = $wpdb->get_results("SELECT * FROM custom_flavour WHERE category_id = 11 ORDER BY flavour_title");	
						if(!empty($resFlavour)){ 
     						foreach($resFlavour as $r){	
								array_push($flavour, $r->flavour_title);
								array_push($fId, $r->id);	
							}
								$jointFlavour= array_combine($fId, $flavour);
						}
				?>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Flavour', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">
                <?php		
			          dokan_post_input_box( $post->ID, 'flavour', array('options' =>$jointFlavour, 'value' => $sp_data['flavour'][0]), 'select' );
			    ?>
                </div>
                </div>
				<?php		
					break;
				case 'Sake':				
							$bottle_size= array (''=> 'Select Size', 
													180 => '180 ml',	
													200 => '200 ml',
													300 => '300 ml',
													330 => '330 ml',
													350 => '350 ml',
													500 => '500 ml',
													700 => '700 ml',
													720 => '720 ml',
													1800 => '1800 ml (1.8L)',	
													2000 => '2000 ml (2L)');
					?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottle_size', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">
                     <?php                               
					dokan_post_input_box( $post->ID, 'bottle_size', array('options' =>$bottle_size, 'value' => $sp_data['bottle_size'][0]), 'select' );								
					?>
                    </div>
                    </div>
                    <?php	
						
							$brands = array( 0=> 'Select Brand');
							$bId = array('');
						$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 30 ORDER BY brand_title");	
						if(!empty($resBrands)){ 
     						foreach($resBrands as $r) {	
								array_push($brands, $r->brand_title);
								array_push($bId, $r->id);	
							}
								$jointBrand= array_combine($bId, $brands);
						}
						?>
                     <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brand', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php
						
					dokan_post_input_box( $post->ID, 'brand', array('options' =>$jointBrand, 'value' => $sp_data['brands'][0]), 'select' );
					?>
                    </div>
                    </div>
                    <?php	
						$cats = array( 0=> 'Select Category');
						$cId = array(''); 
						$resCats = $wpdb->get_results("SELECT * FROM custom_category WHERE category_id = 30 ORDER BY category_title");	
						if(!empty($resCats)){ 
     						foreach($resCats as $r) {	
								array_push($cats, $r->category_title);
								array_push($cId, $r->id); 	
							}
								$jointCat= array_combine($cId, $cats);
						}
						?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Category', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                        <?php
					 dokan_post_input_box( $post->ID, 'category', array('options' =>$jointCat, 'value' => $sp_data['category'][0]), 'select' );
			           ?>
                       </div>
                       </div>
                       <?php
			
							$grade= array(''=>'Select Grade',
										'daiginjo'=> 'Daiginjo', 
										'ginjo'=> 'Ginjo', 
										'honjozo'=> 'Honjozo', 
										'junmai'=> 'Junmai');
					    ?>
                        <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Grade', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                     <?php					
					dokan_post_input_box( $post->ID, 'grade', array('options' =>$grade, 'value' => $sp_data['grade'][0]), 'select' );
			         ?>
                    </div>
                    </div>
                         <?php $brewery= array(0=>'Select Brewery',
											1 => 'Akita Seishu Shuzo',
											2 => 'Asahi Shuzo',
											3 => 'Asamai Shuzo',
											4 => 'Gochoda Shuzo',
											5 => 'Hakkai Jozo',
											6 => 'Imada Shuzo',
											7 => 'Ikegami Shuzo',
											8 => 'Ishikawa Shuzo',
											9 => 'Kaetsu Shuzo',
											10 => 'Kamotsuru Shuzo',
											11 => 'Katokichibee Shoten',
											12 => 'Kikumasamune Shuzo',
											13 => 'Kikusui Shizo',
											15 => 'Masuichi Ichimura Shuzo',
											16 => 'Miyao Shuzo',
											17 => 'Miyozakura Shuzo',
											18 => 'Muromachi Shuzo',
											19 => 'Nanbubijin Co. Ltd.',
											20 => 'Ryujin Shuzo',
											21 => 'akeroku Shuzo',
											22 => 'Shata Shuzo',
											23 => 'Sudo Honke Shuzo',
											24 => 'Tabata Shuzo',
											25 => 'Takara Shuzo',
											26 => 'Takasago Shuzo',
											27 => 'Takenotsuyu Shuzo',
											28 => 'Tamanohikari Shuzo',
											29 => 'Taruhei Shuzo',
											30 => 'Totsuka Shuzo',
											31 => 'Toyosawa Shuzo',
											32 => 'Uehara Shuzo');
					?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brewery', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                   <?php						
				 dokan_post_input_box( $post->ID, 'brewery', array('options' =>$brewery, 'value' => $sp_data['brewery'][0]), 'select' );						
					?>	
			        </div>
                    </div>
			        <?php
								$brew_method= array(''=>'Select Brewery Method',
											1 => 'Kimoto',
											2 => 'Sokugo',
											3 => 'Yamahai');
				     ?>
                     <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brewery Method', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                   <?php							
				  dokan_post_input_box( $post->ID, 'brew_method', array('options' =>$brew_method, 'value' => $sp_data['brew_method'][0]), 'select' );							
			       ?>
			        </div>
                    </div>
					<?php						
					break;
				case 'Soju/Sochu':
								$bottle_size= array (''=> 'Select Size', 
													180 => '180 ml',	
													200 => '200 ml',
													300 => '300 ml',
													330 => '330 ml',
													350 => '350 ml',
													500 => '500 ml',
													700 => '700 ml',
													720 => '720 ml',
													750 => '750 ml',
													1800 => '1800 ml (1.8L)',	
													2000 => '2000 ml (2L)');
				?>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottle_size', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                <?Php
                                                    
				  dokan_post_input_box( $post->ID, 'bottle_size', array('options' =>$bottle_size, 'value' => $sp_data['bottle_size'][0]), 'select' );										
				?>
                </div>
                </div>
                <?php		
						
						$brands = array( 0=> 'Select Brand');
						$bId = array('');
						$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 31 ORDER BY brand_title");	
						if(!empty($resBrands)){ 
     						foreach($resBrands as $r) {	
								array_push($brands, $r->brand_title);
								array_push($bId, $r->id);	
							}
								$jointBrand= array_combine($bId, $brands);
						}
					?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brand', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php					
					dokan_post_input_box( $post->ID, 'brand', array('options' =>$jointBrand, 'value' => $sp_data['brand'][0]), 'select' );
					?>
                    </div>
                    </div>
                    
                    <?php
								$mfg_country= array( 0=> 'Select Country');
								$mId = array(''); 
								$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
								if(!empty($resMfgCountry)){ 
									foreach($resMfgCountry as $r) {	
										array_push($mfg_country, $r->country_title);
										array_push($mId, $r->id); 	
									}
										$jointCountry= array_combine($mId, $mfg_country);
								}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Country(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_country', array('options' =>$jointCountry, 'value' => $sp_data['mfg_country'][0]), 'select' );
						?>
						</div>
                        </div>
                        
                        <?php
									$mfg_region= array( 0=> 'Select Region'); 
									$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region ORDER BY region_title");
									if(!empty($resMfgRegion)){ 
										foreach($resMfgRegion as $r) {	
											array_push($mfg_region, $r->region_title); 	
										}
									}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Region(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_region', array('options' =>$mfg_region, 'value' => $sp_data['mfg_region'][0]), 'select' );
						?>
						</div>
                        </div>
                    <?php			
							$brewery= array(''=>'Select Brewery',
											1 => 'Anami Oshima',
											2 => 'Asahi',
											3 => 'Denen',
											4 => 'Hamada',
											5 => 'Jikuya');
					?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brewery', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php						
					 dokan_post_input_box( $post->ID, 'brewery', array('options' =>$brewery, 'value' => $sp_data['brewery'][0]), 'select' );						
				    ?>
                    </div>
                    </div>
                    <?php
							$sochu_type= array(''=>'Select Type',
													1 => 'Korui',
													2 => 'Otsurui');
					?>
					<div class="dokan-form-group">
                    	<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Type of Sochu', 'dokan' ); ?></label>
                    	<div class="dokan-w6 dokan-text-left"> 
                    <?php						
					 	dokan_post_input_box( $post->ID, 'sochu_type', array('options' =>$sochu_type, 'value' => $sp_data['sochu_type'][0]), 'select' );						
				    ?>
                    	</div>
                    </div>
                    <?php			
							$sochu_variety= array(''=>'Select Variety',
												1 => 'Genshu',
												2 => 'Hanatare',
												3 => 'Koshu',
												4 => 'Kame/Tsubo Shikomi');											
					?>
					<div class="dokan-form-group">
                    	<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Variety of Sochu', 'dokan' ); ?></label>
                    	<div class="dokan-w6 dokan-text-left"> 
                    <?php						
					 	dokan_post_input_box( $post->ID, 'sochu_variety', array('options' =>$sochu_variety, 'value' => $sp_data['sochu_variety'][0]), 'select' );						
				    ?>
                    	</div>
                    </div>  
			<?php
					break;
				case 'Tequila':
							$bottle_size= array (''=> 'Select Size', 
													180 => '180 ml',	
													200 => '200 ml',
													300 => '300 ml',
													330 => '330 ml',
													350 => '350 ml',
													500 => '500 ml',
													700 => '700 ml',
													720 => '720 ml',
													750 => '750 ml',
													1750 => '1750 ml (1.75L)');
													?>
                       <div class="dokan-form-group">
                		<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottle Size', 'dokan' ); ?></label>
                		<div class="dokan-w6 dokan-text-left">                                  
                     <?php
				    dokan_post_input_box( $post->ID, 'bottle_size', array('options' =>$bottle_size, 'value' => $sp_data['bottle_size'][0]), 'select' );							
					?>
                    </div>
                    </div>
						
						<?php
							$brands = array( 0=> 'Select Brand');
							$bId = array('');
							$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 33 ORDER BY brand_title");	
							if(!empty($resBrands)){ 
								foreach($resBrands as $r) {	
									array_push($brands, $r->brand_title);
									array_push($bId, $r->id);	
								}
									$jointBrand= array_combine($bId, $brands);
							}
							?>
                       <div class="dokan-form-group">
                		<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brand', 'dokan' ); ?></label>
                		<div class="dokan-w6 dokan-text-left">
                      <?php      
						
					 dokan_post_input_box( $post->ID, 'brand', array('options' =>$jointBrand, 'value' => $sp_data['brand'][0]), 'select' );
						?>
                        </div>
                        </div>
                     <?php
						 $cats = array( 0=> 'Select Category');
						 $cId = array('');
						$resCats = $wpdb->get_results("SELECT * FROM custom_category WHERE category_id = 33 ORDER BY category_title");	
						if(!empty($resCats)){ 
     						foreach($resCats as $r) {	
								array_push($cats, $r->category_title);
								array_push($cId, $r->id); 	
							}
								$jointCat= array_combine($cId, $cats);
						}
						?>
                        <div class="dokan-form-group">
                		<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Category', 'dokan' ); ?></label>
                		<div class="dokan-w6 dokan-text-left">
                        <?php
				      dokan_post_input_box( $post->ID, 'category', array('options' =>$jointCat, 'value' => $sp_data['category'][0]), 'select' );		
						?>
                        </div>
                        </div>
                        
			          <?php
			
						$distillery= array(''=>'Select Distillery',
										1 => 'Agave Conquista',
										2 => 'Agave Tequilana',
										3 => 'Agaveros Unidos de Amatitan', 
										4 => 'Agaveros y Tequiloeros Unidos de Los Altos', 
										5 => 'Agroindustrias Casa Ramirez');
					?>
                      <div class="dokan-form-group">
                		<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Distillery', 'dokan' ); ?></label>
                		<div class="dokan-w6 dokan-text-left">                   
					<?php					
					 dokan_post_input_box( $post->ID, 'distillery', array('options' =>$distillery, 'value' => $sp_data['distillery'][0]), 'select' );					
				    ?>	
                    </div>	
                    </div>		
			       <?php
			
						$cooking= array(''=>'Select Cooking Type',
										1 => 'Brick',
										2 => 'Ceramic',
										3 => 'Clay', 
										4 => 'Diffuser', 
										5 => 'Stainless steel');
				?>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Cooking', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">    
               
                <?php						
										
					dokan_post_input_box( $post->ID, 'cooking', array('options' =>$cooking, 'value' => $sp_data['cooking'][0]), 'select' );					
			?>
            </div>
            </div>
            <?php
					$distilation= array(''=>'Select Distilation',
										1 => 'Double',
										2 => 'Triple',
										3 => 'Quadruple', 
										4 => '5x');
			?>
            <div class="dokan-form-group">
                <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Distilation', 'dokan' ); ?></label>
                <div class="dokan-w6 dokan-text-left">    
            <?php							
					dokan_post_input_box( $post->ID, 'distilation', array('options' =>$distilation, 'value' => $sp_data['distilation'][0]), 'select' );					
			?>
            </div>
            </div>
            <?php
			
						$distMethod= array(''=>'Select Cooking Type',
										1 => 'Pot Still-Stainless Steel',
										2 => 'Column Still',
										3 => 'Pot Still-Copper', 
										4 => 'diffuser');
		?>
        <div class="dokan-form-group">
                <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Distillation Method', 'dokan' ); ?></label>
                <div class="dokan-w6 dokan-text-left"> 
              <?php
					dokan_post_input_box( $post->ID, 'distMethod', array('options' =>$distMethod, 'value' => $sp_data['distMethod'][0]), 'select' );					
										
			?>
       </div>
       </div>     
            <?php
			
						$aging= array(''=>'Select Cooking Type',
										1 => 'American Oak',
										2 => 'Blend',
										3 => 'Bordeaux',
										4 => 'Bourbon',
										5 => 'Congan');
			?>
             <div class="dokan-form-group">
                <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Barrel Aging Type', 'dokan' ); ?></label>
                <div class="dokan-w6 dokan-text-left"> 
            <?php							
					dokan_post_input_box( $post->ID, 'aging', array('options' =>$aging, 'value' => $sp_data['aging'][0]), 'select' );						
			?>
            </div>
            </div>
            <?php
			
						
					break;
				case 'Sugar cane spirit':
						
						$bottle_size= array (''=> 'Select Size', 
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
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottle_size', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                <?php								
					dokan_post_input_box( $post->ID, 'bottle_size', array('options' =>$bottle_size, 'value' => $sp_data['bottle_size'][0]), 'select' );							
				?>									
                </div>
                </div>
			    <?php
						$brands = array( 0=> 'Select Brand');
						$bId = array(''); 
							$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 32 ORDER BY brand_title");	
							if(!empty($resBrands)){ 
								foreach($resBrands as $r) {	
									array_push($brands, $r->brand_title);
									array_push($bId, $r->id);	
								}
									$jointBrand= array_combine($bId, $brands);
							}
				?>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brand', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                <?php						
			      dokan_post_input_box( $post->ID, 'brand', array('options' =>$jointBrand, 'value' => $sp_data['brand'][0]), 'select' );
				?>
                </div>
                </div>
                <?php		
						$flavour = array( 0=> 'Select Flavour');
						$fId = array('');
						$resFlavour = $wpdb->get_results("SELECT * FROM custom_flavour WHERE category_id = 32 ORDER BY flavour_title");	
						if(!empty($resFlavour)){ 
     						foreach($resFlavour as $r){	
								array_push($flavour, $r->flavour_title);
								array_push($fId, $r->id); 	
							}
								$jointFlavour= array_combine($fId, $flavour);
						}
				?>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Flavour', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                <?php		
				  dokan_post_input_box( $post->ID, 'flavour', array('options' =>$jointFlavour, 'value' => $sp_data['flavour'][0]), 'select' );	
				?>
                </div>
                </div>						
				<?php	
						$cats = array( 0=> 'Select Category');
						$cId = array(''); 
						$resCats = $wpdb->get_results("SELECT * FROM custom_category WHERE category_id = 32 ORDER BY category_title");	
						if(!empty($resCats)){ 
     						foreach($resCats as $r) {	
								array_push($cats, $r->category_title);
								array_push($cId, $r->id);	
							}
								$jointCat= array_combine($cId, $cats);
						}
				?>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Category', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                <?php		
				  dokan_post_input_box( $post->ID, 'category', array('options' =>$jointCat, 'value' => $sp_data['category'][0]), 'select' );	
			    ?>
			    </div>
                </div>
				<?php	$distillery= array(''=>'Select Distillery',
										1 => 'Anguilla',
										2 => '-Anguilla Rum Company blender / bottler',
										3 => '-Antigua and Barbuda', 
										4 => '-Antigua Distillery Limited distiller', 
										5 => '-Barrettos blender / bottler');
				?>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Distillery', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                <?php						
				  dokan_post_input_box( $post->ID, 'distillery', array('options' =>$distillery, 'value' => $sp_data['distillery'][0]), 'select' );						
				?>						
			    </div>
                </div>
                
                   <?php
							$mfg_country= array( 0=> 'Select Country');
							$mId = array(''); 
							$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
							if(!empty($resMfgCountry)){ 
								foreach($resMfgCountry as $r) {	
									array_push($mfg_country, $r->country_title);
									array_push($mId, $r->id); 	
								}
									$jointCountry= array_combine($mId, $mfg_country);
							}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Country(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_country', array('options' =>$jointCountry, 'value' => $sp_data['mfg_country'][0]), 'select' );
						?>
						</div>
                        </div>
                        
                        <?php
								$mfg_region= array( 0=> 'Select Region'); 
								$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region ORDER BY region_title");
								if(!empty($resMfgRegion)){ 
									foreach($resMfgRegion as $r) {	
										array_push($mfg_region, $r->region_title); 	
									}
								}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Region(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_region', array('options' =>$mfg_region, 'value' => $sp_data['mfg_region'][0]), 'select' );
						?>
						</div>
                        </div>
				<?php		
					break;
				case 'Vodka':
				
						$bottle_size= array (''=> 'Select Size', 
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
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottle_size', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php							
				    dokan_post_input_box( $post->ID, 'bottle_size', array('options' =>$bottle_size, 'value' => $sp_data['bottle_size'][0]), 'select' );									
					?>	
                    </div>
                    </div>							
			        <?php
			
						$brands = array( 0=> 'Select Brand');
						$bId = array(''); 
							$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 34 ORDER BY brand_title");	
							if(!empty($resBrands)){ 
								foreach($resBrands as $r) {	
									array_push($brands, $r->brand_title);
									array_push($bId, $r->id); 	
								}
									$jointBrand= array_combine($bId, $brands);
							}
					?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brand', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php					
					dokan_post_input_box( $post->ID, 'brand', array('options' =>$jointBrand, 'value' => $sp_data['brand'][0]), 'select' );
				     ?>
                     </div>
                     </div>
                      <div class="dokan-form-group">
                   			 <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Distillery', 'dokan' ); ?></label>
                    			<div class="dokan-w6 dokan-text-left"> 
					<?php		
                       dokan_post_input_box( $post->ID, 'distillery', array('options' =>array(''=>'Select Distillery'), 'value' => $sp_data['distillery'][0]), 'select' );		
                    ?>		
                   			 </div>
                   		 </div>
                         
                         <?php
								$mfg_country= array( 0=> 'Select Country');
								$mId = array(''); 
								$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
								if(!empty($resMfgCountry)){ 
									foreach($resMfgCountry as $r) {	
										array_push($mfg_country, $r->country_title);
										array_push($mId, $r->id); 	
									}
										$jointCountry= array_combine($mId, $mfg_country);
								}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Country(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_country', array('options' =>$jointCountry, 'value' => $sp_data['mfg_country'][0]), 'select' );
						?>
						</div>
                        </div>
                        <?php
								$mfg_region= array( 0=> 'Select Region'); 
								$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region ORDER BY region_title");
								if(!empty($resMfgRegion)){ 
									foreach($resMfgRegion as $r) {	
										array_push($mfg_region, $r->region_title); 	
									}
								}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Region(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_region', array('options' =>$mfg_region, 'value' => $sp_data['mfg_region'][0]), 'select' );
						?>
						</div>
                        </div>
                         
                     <?php
						$flavour = array( 0=> 'Select Flavour');
						$fId = array('');
						$resFlavour = $wpdb->get_results("SELECT * FROM custom_flavour WHERE category_id = 34 ORDER BY flavour_title");	
						if(!empty($resFlavour)){ 
     						foreach($resFlavour as $r){	
								array_push($flavour, $r->flavour_title);
								array_push($fId, $r->id);	
							}
								$jointFlavour= array_combine($fId, $flavour);
						}
					?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Flavour', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php	
				    dokan_post_input_box( $post->ID, 'flavour', array('options' =>$jointFlavour, 'value' => $sp_data['flavour'][0]), 'select' );	
			        ?>
                    </div>
                    </div>
			        <?php
					$base= array(''=>'Select Base',
										1 => 'Barley',
										2 => 'Cereal Grains',
										3 => 'Corn',
										4 => 'Fig',
										5 => 'Fruits');
					?>
                    <div class="dokan-form-group">
                        <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Base', 'dokan' ); ?></label>
                        <div class="dokan-w6 dokan-text-left"> 
                    <?php					
				   dokan_post_input_box( $post->ID, 'base', array('options' =>$base, 'value' => $sp_data['base'][0]), 'select' );							
			        ?>
                      </div>
                    </div>
			        <?php
					$distill_No= array(''=>'Select Distillary No',
										4 => '>4',
										5 => '>5',
										6 => '>6',
										7 => '>7',
										8 => '>8');
					?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'No of Distillation', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php					
				  dokan_post_input_box( $post->ID, 'distill_No', array('options' =>$distill_No, 'value' => $sp_data['distill_No'][0]), 'select' );							
			        ?>
                    </div>
                    </div>
			       <?php
					break;
				case 'Whisky':
				
					$bottle_size= array (''=> 'Select Size', 
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
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottle Size', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php							
					dokan_post_input_box( $post->ID, 'bottle_size', array('options' =>$bottle_size, 'value' => $sp_data['bottle_size'][0]), 'select' );							
					?>							
					</div>
                    </div>
				
				 <?php  $brands = array( 0=> 'Select Brand');
				 		$bId = array(''); 
						$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 24 ORDER BY brand_title");	
						if(!empty($resBrands)){ 
     						foreach($resBrands as $r){	
								array_push($brands, $r->brand_title);
								array_push($bId, $r->id);	
							}
								$jointBrand= array_combine($bId, $brands);
						}
				?>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brand', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                <?php		
					dokan_post_input_box( $post->ID, 'brand', array('options' =>$jointBrand, 'value' => $sp_data['brand'][0]), 'select' );					
				?>	
                </div>
                </div>
				<?php
						$flavour = array( 0=> 'Select Flavour');
						$fId = array('');
						$resFlavour = $wpdb->get_results("SELECT * FROM custom_flavour WHERE category_id = 24 ORDER BY flavour_title");	
						if(!empty($resFlavour)){ 
     						foreach($resFlavour as $r){	
								array_push($flavour, $r->flavour_title);
								array_push($fId, $r->id);	
							}
								$jointFlavour= array_combine($fId, $flavour);
						}
				?>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Flavour', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                <?php		
					dokan_post_input_box( $post->ID, 'flavour', array('options' =>$jointFlavour, 'value' => $sp_data['flavour'][0]), 'select' );					
				?>	
				</div>
                </div>		
				<?php		
					 $cats = array( 0=> 'Select Category');
					 $cId = array(''); 
						$resCats = $wpdb->get_results("SELECT * FROM custom_category WHERE category_id = 24 ORDER BY category_title");	
						if(!empty($resCats)){ 
     						foreach($resCats as $r) {	
								array_push($cats, $r->category_title);
								array_push($cId, $r->id); 	
							}
								$jointCat= array_combine($cId, $cats);
						}
				?>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Category', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                <?php		
				   dokan_post_input_box( $post->ID, 'category', array('options' =>$jointCat, 'value' => $sp_data['category'][0]), 'select' );		
				?>		
			    </div>
                </div>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Distillery', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                <?php		
				   dokan_post_input_box( $post->ID, 'distillery', array('options' =>array(''=>'Select Distillery'), 'value' => $sp_data['distillery'][0]), 'select' );		
				?>		
			    </div>
                </div>
                
						<?php
                                $mfg_country= array( 0=> 'Select Country');
                                $mId = array(''); 
                                $resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
                                if(!empty($resMfgCountry)){ 
                                    foreach($resMfgCountry as $r) {	
                                        array_push($mfg_country, $r->country_title);
                                        array_push($mId, $r->id); 	
                                    }
                                        $jointCountry= array_combine($mId, $mfg_country);
                                }
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Country(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_country', array('options' =>$jointCountry, 'value' => $sp_data['mfg_country'][0]), 'select' );
						?>
						</div>
                        </div>
                        
                        <?php
								$mfg_region= array( 0=> 'Select Region'); 
								$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region ORDER BY region_title");
								if(!empty($resMfgRegion)){ 
									foreach($resMfgRegion as $r) {	
										array_push($mfg_region, $r->region_title); 	
									}
								}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Region(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_region', array('options' =>$mfg_region, 'value' => $sp_data['mfg_region'][0]), 'select' );
						?>
						</div>
                        </div>
                
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottler', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                <?php		
				   dokan_post_input_box( $post->ID, 'bottler', array('options' =>array(''=>'Select Bottler'), 'value' => $sp_data['bottler'][0]), 'select' );		     
				?>		
			    </div>
                </div>
				<div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Whisky Age', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
			     <?php dokan_post_input_box( $post->ID, 'whiskyAge', array('value' => $sp_data['wiskyAge'][0]), 'text' );?>
                </div>
                </div>
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Distillation Date', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
			    <?php dokan_post_input_box( $post->ID, 'distill_date', array('value' => $sp_data['distill_date'][0]), 'text' );?>
                </div>
                </div>
			   <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottling Date', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
			     <?php dokan_post_input_box( $post->ID, 'bottling_date', array('value' => $sp_data['bottling_date'][0]), 'text' );?>
                </div>
                </div>
					<?php
					break;
					
				case 'Wine':
						
						$bottle_size= array (''=> 'Select Size',
												187 => '187 ml Split',
												375 => '375 ml half bottle', 
												750 => '750 ml bottle',	
												200 => '200 ml',
												300 => '300 ml',
												330 => '330 ml',
												350 => '350 ml',
												500 => '500 ml',
												700 => '700 ml',
												720 => '720 ml',
												1500 => '1.5L Mangnum',
												3000 => '3L Double Mangnum',
												3000 => '3L Jeroboam',
												4500 => '4.5L Jeroboam',
												4500 => '4.5L Rehoboam',
												6000 => '6L Imperial',
												6000 => '6L Methuselah',
												9000 => '9L Salmanazar/Case',
												12000 => '12L Balthazar',
												15000 => '15L Nebuchadnezzar'
												);
					?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Bottle Size', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php							
						 dokan_post_input_box( $post->ID, 'bottle_size', array('options' =>$bottle_size, 'value' => $sp_data['bottle_size'][0]), 'select' );	
						 					
					?>
                    </div>
                    </div>
				 <?php
						$brands = array( 0=> 'Select Brand');
						$bId = array(''); 
						$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 9 ORDER BY brand_title");	
						if(!empty($resBrands)){ 
     						foreach($resBrands as $r) {	
								array_push($brands, $r->brand_title);
								array_push($bId, $r->id); 	
							}
								$jointBrand= array_combine($bId, $brands);
						}
					?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Brand', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php	
					    dokan_post_input_box( $post->ID, 'brand', array('options' =>$jointBrand, 'value' => $sp_data['brands'][0]), 'select' );
			        ?>
                    </div>
                    </div>
                    <?php
						 $cats = array( 0=> 'Select Category');
						 $cId = array('');
						$resCats = $wpdb->get_results("SELECT * FROM custom_category WHERE category_id = 9 ORDER BY category_title");	
						if(!empty($resCats)){ 
     						foreach($resCats as $r) {	
								array_push($cats, $r->category_title);
								array_push($cId, $r->id); 	
							}
								$jointCat= array_combine($cId, $cats);
						}
					?>
                    <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Category', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left"> 
                    <?php	
			           dokan_post_input_box( $post->ID, 'category', array('options' =>$jointCat, 'value' => $sp_data['category'][0]), 'select' );
			        ?>
                    </div>
                    </div>
                    
						<?php
                            $mfg_country= array( 0=> 'Select Country');
                            $mId = array(''); 
                            $resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
                            if(!empty($resMfgCountry)){ 
                                foreach($resMfgCountry as $r) {	
                                    array_push($mfg_country, $r->country_title);
                                    array_push($mId, $r->id); 	
                                }
                                    $jointCountry= array_combine($mId, $mfg_country);
                            }
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Country(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_country', array('options' =>$jointCountry, 'value' => $sp_data['mfg_country'][0]), 'select' );
						?>
						</div>
                        </div>
                        
                        <?php
								$mfg_region= array( 0=> 'Select Region'); 
								$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region ORDER BY region_title");
								if(!empty($resMfgRegion)){ 
									foreach($resMfgRegion as $r) {	
										array_push($mfg_region, $r->region_title); 	
									}
								}
						?>
                        <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Region(Mfg.)', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left">
                        <?php
							dokan_post_input_box( $post->ID, 'mfg_region', array('options' =>$mfg_region, 'value' => $sp_data['mfg_region'][0]), 'select' );
						?>
						</div>
                        </div>                    
			        <?php
						$variety= array('' => 'Select Variety', 1 => 'Charrdonay', 2 => 'Merlot');
						?>
                	<div class="dokan-form-group">
                    	<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Variety', 'dokan' ); ?></label>
                      <div class="dokan-w6 dokan-text-left"> 
                	<?php
						dokan_post_input_box( $post->ID, 'variety', array('options' =>$variety, 'value' => $sp_data['variety'][0]), 'select' );
			    	?>
					  </div>
                	</div>
                    
                    <?php
						$appellation= array('' => 'Select Appellation');
						?>
                	<div class="dokan-form-group">
                    	<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Appellation', 'dokan' ); ?></label>
                      <div class="dokan-w6 dokan-text-left"> 
                	<?php
						dokan_post_input_box( $post->ID, 'variety', array('options' =>$appellation, 'value' => $sp_data['appellation'][0]), 'select' );
			    	?>
					  </div>
                	</div>
                    <?php 
						$winyard= array('' => 'Select Winyard');
					?>
                	<div class="dokan-form-group">
                    	<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Winyard', 'dokan' ); ?></label>
                      <div class="dokan-w6 dokan-text-left"> 
                	<?php
						dokan_post_input_box( $post->ID, 'winyard', array('options' =>$winyard, 'value' => $sp_data['winyard'][0]), 'select' );
			    	?>
					  </div>
                	</div>
                    <?php 
						$winery= array('' => 'Select Winery');
					?>
                	<div class="dokan-form-group">
                    	<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Winery', 'dokan' ); ?></label>
                      <div class="dokan-w6 dokan-text-left"> 
                	<?php
						dokan_post_input_box( $post->ID, 'winery', array('options' =>$winery, 'value' => $sp_data['winery'][0]), 'select' );
			    	?>
					  </div>
                	</div>
                    <?php 
						$vintage= array('' => 'Select Vintage', 
											1 =>'2001',
											2 =>'2002',
											3 =>'2003',
											4 =>'2004',
											5 =>'2005',
											6 =>'2006',
											7 =>'2007',
											8 =>'2008',
											9 =>'2009',
											10 =>'2010',
											11 =>'2011',
											12 =>'2012',
											13 =>'2013',
											14 =>'2014',
											15 =>'2015');
					?>
                	<div class="dokan-form-group">
                    	<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Vintage', 'dokan' ); ?></label>
                      <div class="dokan-w6 dokan-text-left"> 
                	<?php
						dokan_post_input_box( $post->ID, 'vintage', array('options' =>$vintage, 'value' => $sp_data['vintage'][0]), 'select' );
			    	?>
					  </div>
                	</div>
				<?php		
					break;
				default:
			}
			?>
            
                <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Alc./Vol.', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">
            <?php
					dokan_post_input_box( $post->ID, 'alcohal', array('value' => $sp_data['alcohal'][0]), 'text' );
			?>
			    </div>
              </div>
              <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Rating', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">
              <?php
					dokan_post_input_box( $post->ID, 'product_rating', array('options' =>array(0 =>'Select Rating', 1=>1, 2=>2, 3=>3, 4=>4, 5=>5), 'value' => $sp_data['product_rating'][0]), 'select' );            
              ?>
			  	</div>
              </div>
              <div class="dokan-form-group">
                    <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Organic', 'dokan' ); ?></label>
                    <div class="dokan-w6 dokan-text-left">
              <?php
					dokan_post_input_box( $post->ID, 'organic', array('options' =>array(1=>'Yes', 0=>'No'), 'value' => $sp_data['organic'][0]), 'select' );            
              ?>
			  	</div>
              </div>
              		<?php $gift= array(''=>'Gift Wrapping', 1 => 'Yes',0 => 'No'); ?>
                       <div class="dokan-form-group">
                			<label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Gift Wrapping', 'dokan' ); ?></label>
                			<div class="dokan-w6 dokan-text-left"> 
                        <?php					
							dokan_post_input_box( $post->ID, 'gift', array('options' =>$gift, 'value' => $sp_data['gift'][0]), 'select' );				?>
                    	</div>
                     </div>
</div> <!-- .form-horizontal -->
<?php } 

}