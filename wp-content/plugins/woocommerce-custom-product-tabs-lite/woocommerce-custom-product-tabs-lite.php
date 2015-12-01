<?php
/**
 * Plugin Name: WooCommerce Custom Product Tabs Lite
 * Plugin URI: http://simpleintelligentsytem.com/wp/plugins
 * Description: Extends WooCommerce to add a custom product view page tab
 * Author: SkyVerge
 * Author URI: http://simpleintelligentsytem.com/
 * Version: 1.2.9
 * Tested up to: 4.2
 * Text Domain: woocommerce-custom-product-tabs-lite
 * Domain Path: /i18n/languages/
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Check if WooCommerce is active and bail if it's not
if ( ! WooCommerceCustomProductTabsLite::is_woocommerce_active() ) {
	return;
}

/**
 * The WooCommerceCustomProductTabsLite global object
 * @name $woocommerce_product_tabs_lite
 * @global WooCommerceCustomProductTabsLite $GLOBALS['woocommerce_product_tabs_lite']
 */
$GLOBALS['woocommerce_product_tabs_lite'] = new WooCommerceCustomProductTabsLite();

add_action( 'wp_ajax_add_specification', 'prefix_ajax_add_specification' );
add_action( 'wp_ajax_nopriv_add_specification', 'prefix_ajax_add_specification' );

function prefix_ajax_add_specification() {

	if(isset($_POST['data']) && $_POST['data']!='')
	{
		$dataSpec =array();
		$dataSpec= explode('|', $_POST['data']);
	}
	if(isset($dataSpec[0]) && $dataSpec[0]!=$dataSpec[1])
	{
			$tab_mixed = array( 'aging',
								'alcohal',
								'appellation',
								'attribute',
								'base',
								'bottle_size',
								'bottler',
								'bottling_date',
								'brand',
								'brewery',
								'brew_loc',
								'brew_method',
								'category',
								'cooking',
								'distilation',
								'distillery',
								'distMethod',
								'distill_No',
								'distill',
								'distill_date',
								'flavour',
								'fruit',
								'gift',
								'grade',
								'mfg_country',
								'mfg_region',
								'organic',
								'packaging',
								'product_rating',
								'rating',
								'sochu_type',
								'sochu_variety',
								'variety',
								'vintage',
								'whiskyAge',
								'winery',
								'winyard');
								
			foreach($tab_mixed as $tk)
			{
				delete_post_meta( $dataSpec[2], $tk);
			}
		
	}
	if(isset($dataSpec[0]) && $dataSpec[0]!='')
	{
		$prepHtml= $GLOBALS['woocommerce_product_tabs_lite']->gen_custom_field_attrib(array(), get_product_category_by_id($_POST['data']));
		return $prepHtml;
	}
}

function get_product_category_by_id( $category_id ) {
    $term = get_term_by( 'id', $category_id, 'product_cat', 'ARRAY_A' );
    return $term['name'];
}


class WooCommerceCustomProductTabsLite {

	private $tab_data = false;

	/** plugin version number */
	const VERSION = "1.2.5";

	/** plugin text domain */
	const TEXT_DOMAIN = 'woocommerce-custom-product-tabs-lite';

	/** plugin version name */
	const VERSION_OPTION_NAME = 'woocommerce_custom_product_tabs_lite_db_version';


	/**
	 * Gets things started by adding an action to initialize this plugin once
	 * WooCommerce is known to be active and initialized
	 */
	public function __construct() {
		// Installation
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) $this->install();

		add_action( 'init',             array( $this, 'load_translation' ) );
		add_action( 'woocommerce_init', array( $this, 'init' ) );
	}


	/**
	 * Load translations
	 *
	 * @since 1.2.5
	 */
	public function load_translation() {

		// localization
		load_plugin_textdomain( 'woocommerce-custom-product-tabs-lite', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages' );
	}


	/**
	 * Init WooCommerce Product Tabs Lite extension once we know WooCommerce is active
	 */
	public function init() {
		// backend stuff
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'product_write_panel_tab' ) );
		add_action( 'woocommerce_product_write_panels',     array( $this, 'product_write_panel' ) );
		add_action( 'woocommerce_process_product_meta',     array( $this, 'product_save_data' ), 10, 2 );
		add_filter( 'woocommerce_product_tabs', array( $this, 'add_custom_product_tabs'), 10, 2 );

		// frontend stuff
		//add_filter( 'woocommerce_product_tabs', array( $this, 'add_custom_product_tabs' ) );

		// allow the use of shortcodes within the tab content
//		add_filter( 'woocommerce_custom_product_tabs_lite_content', 'do_shortcode' );
	}
	
	
	/*******************************************************new*************************************/
	
	public function product_has_custom_tabs( $product ) {
		if ( false === $this->tab_data ) {
			$this->tab_data = maybe_unserialize( get_post_meta( $product->id, 'frs_woo_product_tabs', true ) );
		}
		// tab must at least have a title to exist
		return 'Specification';
	}
	
	
	public function add_custom_product_tabs( $tabs ) {
		global $product;
		//print_r($product);
		if ( $this->product_has_custom_tabs( $product ) ) {
				$tab_specific = __( 'Specification', 'woocommerce-custom-product-tabs-lite' );
				$tabs[ $tab['id'] ] = array(
					'title'    => apply_filters( 'woocommerce_custom_product_tabs_lite_title', $tab_specific, $product, $this ),
					'priority' => 25,
					'callback' => array( $this, 'custom_product_tabs_panel_content'),
					'content'  => $tab['content'],  // custom field
				);
		}
		return $tabs;
	}
	
	
	public function custom_product_tabs_panel_content() {
		//echo apply_filters( 'woocommerce_custom_product_tabs_lite_heading', '<h2>Specification</h2>', $tab );
		echo $this->displaySpecification();
	}

	
	
	
	public function displaySpecification() {
		global $wpdb;
		global $post;		
		//echo $post->ID;
		//exit;
				$saved_bottle_size = maybe_unserialize( get_post_meta($post->ID, 'bottle_size', true) );
				$saved_brand = maybe_unserialize( get_post_meta( $post->ID, true) );
				$saved_distillery = maybe_unserialize( get_post_meta( $post->ID, 'distillery', true) );
				$saved_bottler = maybe_unserialize( get_post_meta( $post->ID, 'bottler', true) );
				$saved_rating = maybe_unserialize( get_post_meta( $post->ID, 'rating', true) );
				$saved_category = maybe_unserialize( get_post_meta( $post->ID, 'category', true) );
				$saved_flavour = maybe_unserialize( get_post_meta( $post->ID, 'flavour', true) );
				$saved_organic = maybe_unserialize( get_post_meta( $post->ID, 'organic', true) );
				$saved_distilation = maybe_unserialize( get_post_meta( $post->ID, 'distilation', true) );
				$saved_distMethod = maybe_unserialize( get_post_meta( $post->ID, 'distMethod', true) );
				$saved_distill_No = maybe_unserialize( get_post_meta( $post->ID, 'distill_No', true) );
				$saved_aging = maybe_unserialize( get_post_meta( $post->ID, 'aging', true) );
				$saved_base = maybe_unserialize( get_post_meta( $post->ID, 'base', true) );
				$saved_variety = maybe_unserialize( get_post_meta( $post->ID, 'variety', true) );
				
				$saved_appellation = maybe_unserialize( get_post_meta( $post->ID, 'appellation', true) );
				$saved_winyard = maybe_unserialize( get_post_meta( $post->ID, 'winyard', true) );
				$saved_winery = maybe_unserialize( get_post_meta( $post->ID, 'winery', true) );
				$saved_vintage = maybe_unserialize( get_post_meta( $post->ID, 'vintage', true) );
				
				$saved_gift = maybe_unserialize( get_post_meta( $post->ID, 'gift', true) );
				$saved_grade = maybe_unserialize( get_post_meta( $post->ID, 'grade', true) );
				$saved_brewery = maybe_unserialize( get_post_meta( $post->ID, 'brewery', true) );
				$saved_sochu_type = maybe_unserialize( get_post_meta( $post->ID, 'sochu_type', true) );
				$saved_sochu_variety = maybe_unserialize( get_post_meta( $post->ID, 'sochu_variety', true) );
				$saved_brew_method = maybe_unserialize( get_post_meta( $post->ID, 'brew_method', true) );
				$saved_brew_loc = maybe_unserialize( get_post_meta( $post->ID, 'brew_loc', true) );
				$saved_fruit = maybe_unserialize( get_post_meta( $post->ID, 'fruit', true) );
				$saved_mfg_country = maybe_unserialize( get_post_meta( $post->ID, 'mfg_country', true) );
				$saved_mfg_region = maybe_unserialize( get_post_meta( $post->ID, 'mfg_region', true) );
				$saved_ships_to = maybe_unserialize( get_post_meta( $post->ID, 'ships_to', true) );
				$saved_delivers_to = maybe_unserialize( get_post_meta( $post->ID, 'delivers_to', true) );
				$saved_style = maybe_unserialize( get_post_meta( $post->ID, 'style', true) );
				$saved_cooking = maybe_unserialize( get_post_meta( $post->ID, 'cooking', true) );
				$saved_bottling_date = maybe_unserialize( get_post_meta( $post->ID, 'bottling_date', true) );
				$saved_distill_date = maybe_unserialize( get_post_meta( $post->ID, 'distill_date', true) );
				$saved_whiskyAge = maybe_unserialize( get_post_meta( $post->ID, 'whiskyAge', true) );
				$saved_alcohal = maybe_unserialize( get_post_meta( $post->ID, 'alcohal', true) );
				$saved_attribute = maybe_unserialize( get_post_meta( $post->ID, 'attribute', true) );
				$saved_packaging = maybe_unserialize( get_post_meta( $post->ID, 'packaging', true) );
				
		

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
										750 => '750 ml',
										1750 => '1750 ml (1.75L)',
										1800 => '1800 ml (1.80L)');
										
			     $style= array (''=> 'Select Style', 
									   1 => 'Distilled Gin',
									   2 => 'Gin',	
									   3 => 'Juniper Flavored Spirit',		
									   4 => 'London Gin');
					
				$grade= array(''=>'Select Grade',
									'daiginjo'=> 'Daiginjo', 
									'ginjo'=> 'Ginjo', 
									'honjozo'=> 'Honjozo', 
									'junmai'=> 'Junmai');
															   							
				$brewery= array(0=>'Select Brewery',
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
											
					$sochu_type= array(''=>'Select Type',
											1 => 'Korui',
											2 => 'Otsurui');

					$sochu_variety= array(''=>'Select Variety',
											1 => 'Genshu',
											2 => 'Hanatare',
											3 => 'Koshu',
											4 => 'Kame/Tsubo Shikomi');
											
					$brew_method= array(''=>'Select Brewery Method',
										1 => 'Kimoto',
										2 => 'Sokugo',
										3 => 'Yamahai');
										
					$brew_loc= array(''=>'Select Brewery Location',
											 1=>'England',
											 2=>'Ireland',
											 3=>'Scotland',
											 4=>'Wales',
											 5=>'Germany',
											 6=>'Checkoslavakia',
											 7=>'Usa',
											 8=>'India'); 
										
					$distillery= array(''=>'Select Distillery',
										1 => 'Agave Conquista',
										2 => 'Agave Tequilana',
										3 => 'Agaveros Unidos de Amatitan', 
										4 => 'Agaveros y Tequiloeros Unidos de Los Altos', 
										5 => 'Agroindustrias Casa Ramirez');	
										
					$bottler= array(''=>'Select Bottler');
									
					$cooking= array(''=>'Select Cooking Type',
									1 => 'Brick',
									2 => 'Ceramic',
									3 => 'Clay', 
									4 => 'Diffuser', 
									5 => 'Stainless steel');	
								
									
				    $distilation= array(''=>'Select Distilation',
									1 => 'Double',
									2 => 'Triple',
									3 => 'Quadruple', 
									4 => '5x');		
									
					$distMethod= array(''=>'Select Cooking Type',
										1 => 'Pot Still-Stainless Steel',
										2 => 'Column Still',
										3 => 'Pot Still-Copper', 
										4 => 'diffuser');
									
					$aging= array(''=>'Select Cooking Type',
									  1 => 'American Oak',
									  2 => 'Blend',
									  3 => 'Bordeaux',
									  4 => 'Bourbon',
									  5 => 'Congan');	
							  
					$base= array(''=>'Select Base',
									 1 => 'Barley',
									 2 => 'Cereal Grains',
									 3 => 'Corn',
									 4 => 'Fig',
									 5 => 'Fruits');
							 
					$distill_No= array(''=>'Select Distillary No',
									4 => '>4',
									5 => '>5',
									6 => '>6',
									7 => '>7',
									8 => '>8');
					$organic= array(1=>'Yes', 0=>'No');				
					$fruits= array('' => 'Select Fruit','apple'=>'Apple', 'apricot'=>'Apricot', 'blueberry'=>'Blueberry', 'cherry'=>'Cherry', 'peach'=>'Peach');				
					$variety= array('' => 'Select Variety', 1 => 'Charrdonay', 2 => 'Merlot');
					
					$appellation= array('' => 'Select Appellation');
					$winyard= array('' => 'Select Winyard');
					$winery= array('' => 'Select Winery');
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
					
					
					$rating= array(0=>'Select Rating', 1=>1, 2=>2, 3=>3, 4=>4, 5=>5);
					$gift= array(''=>'Gift Wrapping', 1 => 'Yes', 0 => 'No');
					$attribute= array(0=>'Select Attribute', 1=>'Premium', 2=>'Imported', 3=>'Craft');
					$packaging= array(0=>'Select Packaging', 1=>'Bottle', 2=>'Can', 3=>'Keg');
					//$ships_to= array(0=>'Any Place', 1=>'Within State', 2=>'Within Country' );	
					//$delivers_to= array(0=>'Any Place', 1=>'Within State', 2=>'Within Country' );	
					
					//echo "SELECT brand_title FROM custom_brand WHERE id =(SELECT meta_value FROM `wp_postmeta` where meta_key='brand' AND post_id=".$post->ID.")";
					//echo '<br>';
					//echo "SELECT category_title FROM custom_category WHERE id =(SELECT meta_value FROM `wp_postmeta` where meta_key='category' AND post_id=".$post->ID.")";
					//echo '<br>';
					//echo "SELECT flavour_title FROM custom_flavour WHERE id =(SELECT meta_value FROM `wp_postmeta` where meta_key='flavour' AND post_id=".$post->ID.")";
					 $resBrands = $wpdb->get_results("SELECT brand_title FROM custom_brand WHERE id =(SELECT meta_value FROM `wp_postmeta` where meta_key='brand' AND post_id=".$post->ID.")");
					if(!empty($resBrands)){ 
						foreach($resBrands as $r){	
									$rBrand = $r->brand_title;
						}
					}	
					$resCats = $wpdb->get_results("SELECT category_title FROM custom_category WHERE id =(SELECT meta_value FROM `wp_postmeta` where meta_key='category' AND post_id=".$post->ID.")");	
					if(!empty($resCats)){ 
						foreach($resCats as $r) {	
									$rCats = $r->category_title;
						}
					}
					$resFlavour = $wpdb->get_results("SELECT flavour_title FROM custom_flavour WHERE id =(SELECT meta_value FROM `wp_postmeta` where meta_key='flavour' AND post_id=".$post->ID.")");	
					if(!empty($resFlavour)){ 
						foreach($resFlavour as $r){	
									$rFlavour = $r->flavour_title;
						}
					}
					
					$resCountry = $wpdb->get_results("SELECT country_title FROM custom_country WHERE id =(SELECT meta_value FROM `wp_postmeta` where meta_key='mfg_country' AND post_id=".$post->ID.")");	
					if(!empty($resCountry)){ 
						foreach($resCountry as $r){	
									$rCountry = $r->country_title;
						}
					}
					
					/*$resRegion = $wpdb->get_results("SELECT region_title FROM custom_region WHERE id =(SELECT meta_value FROM `wp_postmeta` where meta_key='mfg_region' AND post_id=".$post->ID.")");	
					if(!empty($resRegion)){ 
						foreach($resRegion as $r){	
									$rRegion[$r->id] = $r->region_title;
						}
					}*/
						
			$wine_spec ='<div class="spec_tab_content">';
			if(isset($rBrand) && $rBrand!='')   							$wine_spec .= '<p><strong>Brand: </strong>'. $rBrand.'</p>';
			if(isset($rCats) && $rCats!='') 								$wine_spec .= '<p><strong>Category: </strong>'. $rCats.'</p>';
			if(isset($rFlavour) && $rFlavour!='') 							$wine_spec .= '<p><strong>Flavour: </strong>'. $rFlavour.'</p>';
			if(isset($rCountry) && $rCountry!='') 							$wine_spec .= '<p><strong>Country(Mfg.): </strong>'. $rCountry.'</p>';							 		  																																		
			if(isset($saved_bottle_size) && $saved_bottle_size!='' && $saved_bottle_size!=0) $wine_spec .= '<p><strong>Bottle Size: </strong>'.$bottle_size[$saved_bottle_size].'</p>';						
			if(isset($saved_style) && $saved_style!='') 					$wine_spec .= '<p><strong>Style: </strong>'.$style[$saved_style].'</p>';
			if(isset($saved_grade) && $saved_grade!='') 					$wine_spec .= '<p><strong>Grade: </strong>'.$grade[$saved_grade].'</p>';				
			if(isset($saved_brewery) && $saved_brewery!='' && $saved_brewery!=0) $wine_spec .= '<p><strong>Brewery: </strong>'.$brewery[$saved_brewery].'</p>';
			if(isset($saved_sochu_type) && $saved_sochu_type!='' && $saved_sochu_type!=0) $wine_spec .= '<p><strong>Type of Sochu: </strong>'.$sochu_type[$saved_sochu_type].'</p>';
			if(isset($saved_sochu_variety) && $saved_sochu_variety!='' && $saved_sochu_variety!=0) $wine_spec .= '<p><strong>Variety of Sochu: </strong>'.$sochu_variety[$saved_sochu_variety].'</p>';		
			if(isset($saved_brew_method) && $saved_brew_method!='') 		$wine_spec .= '<p><strong>Brewery Location: </strong>'.$brew_method[$saved_brew_method].'</p>';
			if(isset($saved_brew_loc) && $saved_brew_loc!='') 				$wine_spec .= '<p><strong>Brewery  Location: </strong>'.$brew_loc[$saved_brew_loc].'</p>';	
			if(isset($saved_distillery) && $saved_distillery!='') 			$wine_spec .= '<p><strong>Distillery: </strong>'.$distillery[$saved_distillery].'</p>';
			if(isset($saved_bottler) && $saved_bottler!='') 			$wine_spec .= '<p><strong>Distillery: </strong>'.$bottler[$saved_bottler].'</p>';	
			if(isset($saved_cooking) && $saved_cooking!='') 				$wine_spec .= '<p><strong>Cooking: </strong>'.$cooking[$saved_cooking].'</p>';					
			if(isset($saved_distilation) && $saved_distilation!='') 		$wine_spec .= '<p><strong>Distillation: </strong>'.$distilation[$saved_distilation].'</p>';					
			if(isset($saved_distMethod) && $saved_distMethod!='') 			$wine_spec .= '<p><strong>Distillery Method: </strong>'.$distMethod[$saved_distMethod].'</p>';		
			if(isset($saved_aging) && $saved_aging!='')  					$wine_spec .= '<p><strong>Aging: </strong>'.$aging[$saved_aging].'</p>';		 
			if(isset($saved_base) && $saved_base!='') 						$wine_spec .= '<p><strong>Base: </strong>'.$base[$saved_base].'</p>';				
			if(isset($saved_distill_No) && $saved_distill_No!='') 			$wine_spec .= '<p><strong>Distillation No: </strong>'.$distill_No[$saved_distill_No].'</p>';
			if(isset($saved_variety) && $saved_variety!='') 				$wine_spec .= '<p><strong>Variety: </strong>'.$variety[$saved_variety].'</p>';			
			
			if(isset($saved_appellation) && $saved_appellation!='') 		$wine_spec .= '<p><strong>Appellation: </strong>'.$appellation[$saved_appellation].'</p>';
			if(isset($saved_winyard) && $saved_winyard!='') 		$wine_spec .= '<p><strong>Winyard: </strong>'.$winyard[$saved_winyard].'</p>';
			if(isset($saved_winery) && $saved_winery!='') 			$wine_spec .= '<p><strong>Winery: </strong>'.$winery[$saved_winery].'</p>';
			if(isset($saved_vintage) && $saved_vintage!='') 		$wine_spec .= '<p><strong>Vintage: </strong>'.$vintage[$saved_vintage].'</p>';
																							
			if(isset($saved_gift) && $saved_gift!='') 	 					$wine_spec .= '<p><strong>Gift Wrap: </strong>'.$gift[$saved_gift].'</p>';
			//if(isset($saved_ships_to) && $saved_ships_to!='') 	 			$wine_spec .= '<p><strong>Ships To: </strong>'.$ships_to[$saved_ships_to].'</p>';
			//if(isset($saved_delivers_to) && $saved_delivers_to!='') 	 	$wine_spec .= '<p><strong>Delivers To: </strong>'.$delivers_to[$saved_delivers_to].'</p>';
			if(isset($saved_fruit) && $saved_fruit!='') 	 	            $wine_spec .= '<p><strong>Fruit: </strong>'.$fruits[$saved_fruit].'</p>';
			if(isset($rCountry[$saved_mfg_country]) && $rCountry[$saved_mfg_country]!='')   $wine_spec .= '<p><strong>Brand: </strong>'. $rCountry[$saved_mfg_country].'</p>';
			if(isset($rRegion[$saved_mfg_region]) && $rRegion[$saved_mfg_region]!='')   $wine_spec .= '<p><strong>Brand: </strong>'. $rRegion[$saved_mfg_region].'</p>';
			if(isset($saved_organic) && $saved_organic!='') 	 	        $wine_spec .= '<p><strong>Organic: </strong>'.$organic[$saved_organic].'</p>';
			if(isset($saved_attribute) && $saved_attribute!='') 	 		$wine_spec .= '<p><strong>Attribute: </strong>'.$attribute[$saved_attribute].'</p>';
			if(isset($saved_packaging) && $saved_packaging!='') 	 		$wine_spec .= '<p><strong>Packaging: </strong>'.$packaging[$saved_packaging].'</p>';
			if(isset($saved_bottling_date) && $saved_bottling_date!='') 	$wine_spec .= '<p><strong>Bottling Date: </strong>'.$saved_bottling_date.'</p>';
			if(isset($saved_whiskyAge) && $saved_whiskyAge!='') 	        $wine_spec .= '<p><strong>Whisky Age: </strong>'.$saved_whiskyAge.'</p>';
			if(isset($saved_alcohal) && $saved_alcohal!='') 	            $wine_spec .= '<p><strong>Alcohal: </strong>'.$saved_alcohal.'</p>';
			if(isset($saved_distill_date) && $saved_distill_date!='') 	 	$wine_spec .= '<p><strong>Distill Date: </strong>'.$saved_distill_date.'</p>';
			if(isset($saved_rating) && $saved_rating!='' && $saved_rating!=0) $wine_spec .= '<p><strong>Rating: </strong>'.$rating[$saved_rating].'</p>';
			
															
			$wine_spec .='</div>';
			return $wine_spec;
}

	/********************************************************************************************/
	
	/**
	 * Adds a new tab to the Product Data postbox in the admin product interface
	 */
	public function product_write_panel_tab() {
		echo "<li class=\"product_tabs_lite_tab\"><a href=\"#woocommerce_product_tabs_lite\">" . __( 'Specifications', self::TEXT_DOMAIN ) . "</a></li>";
	}

	/**
	 * Adds the panel to the Product Data postbox in the product interface
	 */
	public function product_write_panel() {
		global $post;
		// the product
		
		

		if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
			?>
			<style type="text/css">
				#woocommerce-product-data ul.product_data_tabs li.product_tabs_lite_tab a { padding:5px 5px 5px 28px;background-repeat:no-repeat;background-position:5px 7px; }
			</style>
			<?php
		}

		// pull the custom tab data out of the database
		$sp_data = maybe_unserialize( get_post_meta( $post->ID) );
		//print('<pre>'.print_r($sp_data['bottle_size'][0], true).'</pre>');
		
		if ( empty( $sp_data ) ) {
			$sp_data[] = array( 'bottle_size' => '', 'brand' => '', 'distillery' => '', 'alcohal' => '', 'rating' => '', 'category' => '', 'organic' => '' );
		}
		
		//$wine_type = array('Anise' =>1, 'Beer' =>2, 'Brandy' =>3, 'Gin' =>4, 'Sake' =>5, 'Soju' => 6, 'Sugar' => 7, 'Taquila' => 8, 'Vodka' => 9, 'Whisky' => 10, 'Wine' => 11);
		$category = get_the_terms($post->ID, 'product_cat');
				
		self::gen_custom_field_attrib($sp_data, $category[0]->name);
		
	
	}


		/**
	 * Saves the data inputed into the product boxes, as post meta data
	 * identified by the name 'frs_woo_product_tabs'
	 *
	 * @param int $post_id the post (product) identifier
	 * @param stdClass $post the post (product)
	 */
	public function product_save_data( $post_id, $post ) {

		$sp_bottle_size = isset($_POST['_wc_custom_product_tabs_lite_bottle_size'])?$_POST['_wc_custom_product_tabs_lite_bottle_size']:'';
		$sp_brand = isset($_POST['_wc_custom_product_tabs_lite_brand'] )?$_POST['_wc_custom_product_tabs_lite_brand']:'';
		$sp_distillery = isset($_POST['_wc_custom_product_tabs_lite_distillery'] )?$_POST['_wc_custom_product_tabs_lite_distillery']:'';
		$sp_bottler = isset($_POST['_wc_custom_product_tabs_lite_bottler'] )?$_POST['_wc_custom_product_tabs_lite_bottler']:'';
		$sp_alcohal = isset($_POST['_wc_custom_product_tabs_lite_alcohal'] )?$_POST['_wc_custom_product_tabs_lite_alcohal']:'';
		$sp_rating = isset($_POST['_wc_custom_product_tabs_lite_rating'] )?$_POST['_wc_custom_product_tabs_lite_rating']:'';
		$sp_category = isset($_POST['_wc_custom_product_tabs_lite_category'] )?$_POST['_wc_custom_product_tabs_lite_category']:'';
		$sp_flavour = isset($_POST['_wc_custom_product_tabs_lite_flavour'] )?$_POST['_wc_custom_product_tabs_lite_flavour']:'';
		$sp_organic = isset($_POST['_wc_custom_product_tabs_lite_organic'] )?$_POST['_wc_custom_product_tabs_lite_organic']:'';
		$sp_cooking = isset($_POST['_wc_custom_product_tabs_lite_cooking'] )?$_POST['_wc_custom_product_tabs_lite_cooking']:'';
		$sp_distilation = isset($_POST['_wc_custom_product_tabs_lite_distilation'] )?$_POST['_wc_custom_product_tabs_lite_distilation']:'';
		$sp_distMethod = isset($_POST['_wc_custom_product_tabs_lite_distMethod'] )?$_POST['_wc_custom_product_tabs_lite_distMethod']:'';
		$sp_distill_No = isset($_POST['_wc_custom_product_tabs_lite_distill_No'] )?$_POST['_wc_custom_product_tabs_lite_distill_No']:'';
		$sp_variety = isset($_POST['_wc_custom_product_tabs_lite_variety'] )?$_POST['_wc_custom_product_tabs_lite_variety']:'';
		
		$sp_appellation = isset($_POST['_wc_custom_product_tabs_lite_appellation'] )?$_POST['_wc_custom_product_tabs_lite_appellation']:'';
		$sp_winyard = isset($_POST['_wc_custom_product_tabs_lite_winyard'] )?$_POST['_wc_custom_product_tabs_lite_winyard']:'';
		$sp_winery = isset($_POST['_wc_custom_product_tabs_lite_winery'] )?$_POST['_wc_custom_product_tabs_lite_winery']:'';
		$sp_vintage = isset($_POST['_wc_custom_product_tabs_lite_vintage'] )?$_POST['_wc_custom_product_tabs_lite_vintage']:'';
		
		$sp_gift = isset($_POST['_wc_custom_product_tabs_lite_gift'] )?$_POST['_wc_custom_product_tabs_lite_gift']:'';
		$sp_grade = isset($_POST['_wc_custom_product_tabs_lite_grade'] )?$_POST['_wc_custom_product_tabs_lite_grade']:'';
		$sp_brewery = isset($_POST['_wc_custom_product_tabs_lite_brewery'] )?$_POST['_wc_custom_product_tabs_lite_brewery']:'';
		$sp_sochu_type= isset($_POST['_wc_custom_product_tabs_lite_sochu_type'] )?$_POST['_wc_custom_product_tabs_lite_sochu_type']:'';
		$sp_sochu_variety= isset($_POST['_wc_custom_product_tabs_lite_sochu_variety'] )?$_POST['_wc_custom_product_tabs_lite_sochu_variety']:'';
		
		$sp_brew_method = isset($_POST['_wc_custom_product_tabs_lite_brew_method'] )?$_POST['_wc_custom_product_tabs_lite_brew_method']:'';
		$sp_brew_loc = isset($_POST['_wc_custom_product_tabs_lite_brew_loc'] )?$_POST['_wc_custom_product_tabs_lite_brew_loc']:'';
		
		$sp_aging = isset($_POST['_wc_custom_product_tabs_lite_aging'] )?$_POST['_wc_custom_product_tabs_lite_aging']:'';
		$sp_base = isset($_POST['_wc_custom_product_tabs_lite_base'] )?$_POST['_wc_custom_product_tabs_lite_base']:'';
		
		$sp_whiskyAge = isset($_POST['_wc_custom_product_tabs_lite_whiskyAge'] )?$_POST['_wc_custom_product_tabs_lite_whiskyAge']:'';
		$sp_distill_date = isset($_POST['_wc_custom_product_tabs_lite_distill_date'] )?$_POST['_wc_custom_product_tabs_lite_distill_date']:'';
		$sp_bottling_date = isset($_POST['_wc_custom_product_tabs_lite_bottling_date'] )?$_POST['_wc_custom_product_tabs_lite_bottling_date']:'';
		$sp_fruit = isset($_POST['_wc_custom_product_tabs_lite_fruit'] )?$_POST['_wc_custom_product_tabs_lite_fruit']:'';
		$sp_mfg_country = isset($_POST['_wc_custom_product_tabs_lite_mfg_country'] )?$_POST['_wc_custom_product_tabs_lite_mfg_country']:'';
		$sp_mfg_region = isset($_POST['_wc_custom_product_tabs_lite_mfg_region'] )?$_POST['_wc_custom_product_tabs_lite_mfg_region']:'';
		
		$sp_attribute = isset($_POST['_wc_custom_product_tabs_lite_attribute'] )?$_POST['_wc_custom_product_tabs_lite_attribute']:'';
		$sp_packaging = isset($_POST['_wc_custom_product_tabs_lite_packaging'] )?$_POST['_wc_custom_product_tabs_lite_packaging']:'';
		/*$sp_ships_to = isset($_POST['_wc_custom_product_tabs_lite_ships_to'] )?$_POST['_wc_custom_product_tabs_lite_ships_to']:'';
		$sp_delivers_to = isset($_POST['_wc_custom_product_tabs_lite_delivers_to'] )?$_POST['_wc_custom_product_tabs_lite_delivers_to']:'';*/
		
		$sp_product_rating = isset($_POST['_wc_custom_product_tabs_lite_product_rating'] )?$_POST['_wc_custom_product_tabs_lite_product_rating']:'';
		
		$updated_category= isset($_POST['radio_tax_input'])?$_POST['radio_tax_input']:'';
		$saved_category = get_the_terms($post->ID, 'product_cat');
		
		$tab_mixed = array( 'bottle_size'=>$sp_bottle_size,
							'brand'=>$sp_brand,
							'distillery'=>$sp_distillery,
							'alcohal'=>$sp_alcohal,
							'rating'=>$sp_rating,
							'category'=>$sp_category,
							'flavour'=>$sp_flavour,
							'organic'=>$sp_organic,
							'cooking'=>$sp_cooking,
							'distilation'=>$sp_distilation,
							'bottler'=>$sp_bottler,
							'distMethod'=> $sp_distMethod,
							'distill_No'=> $sp_distill_No,
							'aging'=> $sp_aging,
							'base'=> $sp_base,
							'variety'=> $sp_variety,
							'appellation'=> $sp_appellation,
							'winyard'=> $sp_winyard,
							'winery'=> $sp_winery,
							'vintage'=> $sp_vintage,
							'gift'=> $sp_gift,
							'grade'=> $sp_grade,
							'brewery'=> $sp_brewery,
							'sochu_type'=> $sp_sochu_type,
							'sochu_variety'=> $sp_sochu_variety,
							'brew_method'=> $sp_brew_method,
							'brew_loc'=> $sp_brew_loc,
							'whiskyAge'=> $sp_whiskyAge,
							'distill_date'=> $sp_distill_date,
							'bottling_date'=> $sp_bottling_date,
							'fruit'=> $sp_fruit,
							'mfg_country'=> $sp_mfg_country,
							'mfg_region'=> $sp_mfg_region,
							'attribute'=> $sp_attribute,
							'packaging'=> $sp_packaging,
							/*'ships_to'=> $sp_ships_to,
							'delivers_to'=> $sp_delivers_to,*/
							'product_rating'=> $sp_product_rating
								);

		if (((empty( $sp_bottle_size ) && empty( $sp_brand ) && empty( $sp_distillery ) && empty( $sp_alcohal ) && empty( $sp_rating ) && empty( $sp_product_rating ) && empty( $sp_category ) && empty( $sp_organic ))) || (($saved_category[0]->term_id) && ($saved_category[0]->term_id)!=$updated_category['product_cat'][0])) {
			// clean up if the custom tabs are removed
			foreach($tab_mixed as $tk=>$tv)
			{
				delete_post_meta( $post_id, $tk);
			}
			foreach($tab_mixed as $tk=>$tv)
			{
				if(isset($tv) && $tv!='') update_post_meta( $post_id, $tk, $tv);
			}
			$tab_mixed= array();
			//echo '****##****';
		}
		//else if(isset($saved_category[0]->term_id) && ($saved_category[0]->term_id)!=$updated_category['product_cat'][0]) {
		else {			
			foreach($tab_mixed as $tk=>$tv)
			{
				if(isset($tv) && $tv!='') update_post_meta( $post_id, $tk, $tv);
			}
		}
		//print('<pre>'.print_r($tab_mixed, true).'</pre>');
		//exit;
	}


	private function woocommerce_wp_textarea_input( $field ) {
		global $thepostid, $post;

		if ( ! $thepostid ) $thepostid = $post->ID;
		if ( ! isset( $field['placeholder'] ) ) $field['placeholder'] = '';
		if ( ! isset( $field['class'] ) ) $field['class'] = 'short';
		if ( ! isset( $field['value'] ) ) $field['value'] = get_post_meta( $thepostid, $field['id'], true );

		echo '<p class="form-field ' . $field['id'] . '_field"><label style="display:block;" for="' . $field['id'] . '">' . $field['label'] . '</label><textarea class="' . $field['class'] . '" name="' . $field['id'] . '" id="' . $field['id'] . '" placeholder="' . $field['placeholder'] . '" rows="2" cols="20"' . (isset( $field['style'] ) ? ' style="' . $field['style'] . '"' : '') . '>' . esc_textarea( $field['value'] ) . '</textarea> ';

		if ( isset( $field['description'] ) && $field['description'] ) {
			echo '<span class="description">' . $field['description'] . '</span>';
		}

		echo '</p>';
	}

	/**
	 * Checks if WooCommerce is active
	 *
	 * @since  1.0
	 * @return bool true if WooCommerce is active, false otherwise
	 */
	public static function is_woocommerce_active() {

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}
		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}


	/** Lifecycle methods ******************************************************/


	/**
	 * Run every time.  Used since the activation hook is not executed when updating a plugin
	 */
	private function install() {

		global $wpdb;

		$installed_version = get_option( self::VERSION_OPTION_NAME );

		// installed version lower than plugin version?
		/*if ( -1 === version_compare( $installed_version, self::VERSION ) ) {
			// new version number
			update_option( self::VERSION_OPTION_NAME, self::VERSION );
		}*/
	}


	public static function gen_custom_field_attrib($sp_data, $cat_name){
			global $wpdb;
			// display the custom field
			//echo '<center>'.$cat_name.'</center>';
			echo '<div id="woocommerce_product_tabs_lite" class="panel wc-metaboxes-wrapper woocommerce_options_panel"><div class="extraWrap">';
						
			switch($cat_name)
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
												
					woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size', 'options'=>$bottle_size, 'label' => __( 'Bottle Size', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0]));
				
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
						
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_brand', 'options'=>$jointBrand, 'label' => __( 'Brand', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['brand'][0]));
			
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
			
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_category', 'options'=>$jointCat, 'label' => __( 'Category', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['category'][0] ) );
			
			woocommerce_wp_text_input( array( 'id' => '_wc_custom_product_tabs_lite_distillery', 'label' => __( 'Distillery', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['distillery'][0] ) );
			
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
											 
			 woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size', 'options'=>$bottle_size, 'label' => __( 'Bottle Size', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0]));
			 
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
						
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_brand', 'options'=>$jointBrand, 'label' => __( 'Brand', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['brand'][0]));
			
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
			
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_category', 'options'=>$jointCat, 'label' => __( 'Category', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['category'][0] ) );
			 
			 
			 				$brewery= array(''=>'Select Brewery');
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_brewery', 'options'=>$brewery, 'label' => __( 'Brewery', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['brewery'][0]) );
			
							$brew_loc= array(''=>'Select Brewery Location',
												 1=>'England',
												 2=>'Ireland',
												 3=>'Scotland',
												 4=>'Wales',
												 5=>'Germany',
												 6=>'Checkoslavakia',
												 7=>'Usa',
												 8=>'India'); 
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_brew_loc', 'options'=>$brew_loc, 'label' => __( 'Brewery Location', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['brew_loc'][0]) );
			
							$attribute= array(''=>'Select Attribute', 1=>'Premium',2=>'Imported', 3=>'Craft');
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_attribute', 'options'=>$attribute, 'label' => __( 'Attribute', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['attribute'][0]) );
			
							$packaging= array(''=>'Select Packaging', 1=>'Bottle',2=>'Can', 3=>'Keg');
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_packaging', 'options'=>$packaging, 'label' => __( 'Packaging', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['packaging'][0]) );
			 
					break;
				case 'Brandy':
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
												
					woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size', 'options'=>$bottle_size, 'label' => __( 'Bottle Size', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' =>$sp_data['bottle_size'][0]));
				
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
										
					woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_brand', 'options'=>$jointBrand, 'label' => __( 'Brand', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['brand'][0] ) );
				
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
										
				woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_flavour', 'options'=>$jointFlavour, 'label' => __( 'Flavour Type', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['flavour'][0] ) );		
						
						
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
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_category', 'options'=>$jointCat, 'label' => __( 'Category', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['category'][0] ) );
			
					$distillery= array(''=>'Select Distillery');
										
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_distillery', 'options'=>$distillery, 'label' => __( 'Distillery', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['distillery'][0]) );
				
					$fruits= array('' => 'Select Fruit','apple'=>'Apple', 'apricot'=>'Apricot', 'blueberry'=>'Blueberry', 'cherry'=>'Cherry', 'peach'=>'Peach');
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_fruit',  'options'=>$fruits, 'label' => __( 'Fruit', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['fruit'][0] ) );
			
							
						$mfg_country= array( 0=> 'Select Country');
						$mId = array(''); 
						//$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 28 ORDER BY country_title");
						$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
						if(!empty($resMfgCountry)){ 
     						foreach($resMfgCountry as $r) {	
								array_push($mfg_country, $r->country_title);
								array_push($mId, $r->id); 	
							}
								$jointCountry= array_combine($mId, $mfg_country);
						}
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_country',  'options'=>$jointCountry, 'label' => __( 'Country', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_country'][0]) );
			
						$mfg_region= array( 0=> 'Select Region'); 
						$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region WHERE category_id = 28 ORDER BY region_title");
						if(!empty($resMfgRegion)){ 
     						foreach($resMfgRegion as $r) {	
								array_push($mfg_region, $r->region_title); 	
							}
						}
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_region',  'options'=>$mfg_region, 'label' => __( 'Region', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_region'][0]) );	
			
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
						woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_brand', 'options'=>$jointBrand, 'label' => __( 'Brand', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['brand'][0]) );
								
								$style= array (''=> 'Select Style', 
												1 => 'Distilled Gin',
												2 => 'Gin',	
												3 => 'Juniper Flavored Spirit',		
												4 => 'London Gin');
												
						$distillery= array(''=>'Select Distillery');
										
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_distillery', 'options'=>$distillery, 'label' => __( 'Distillery', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['distillery'][0]) );
						
						woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_style', 'options'=>$style, 'label' => __( 'Style', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['style'][0]) );
								
								$production= array (''=> 'Select Production Method', 
													1 => 'Column Distilled Gin',	
													2 => 'Compound Gin',		
													3 => 'Post distilled gin');
						woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_production', 'options'=>$production, 'label' => __( 'Production Method', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['production'][0]) );
						
				
								$mfg_country= array( 0=> 'Select Country');
								$mId = array('');
								//$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 29 ORDER BY country_title");
								$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
								if(!empty($resMfgCountry)){ 
									foreach($resMfgCountry as $r) {	
										array_push($mfg_country, $r->country_title);
										array_push($mId, $r->id);	
									}
										$jointCountry= array_combine($mId, $mfg_country);
								}
						
						woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_country',  'options'=>$jointCountry, 'label' => __( 'Country', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_country'][0]) );
						
									$mfg_region= array( 0=> 'Select Region'); 
									$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region WHERE category_id = 29 ORDER BY region_title");
									if(!empty($resMfgRegion)){ 
										foreach($resMfgRegion as $r) {	
											array_push($mfg_region, $r->region_title); 	
										}
									}
											
						woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_region',  'options'=>$mfg_region, 'label' => __( 'Region', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_region'][0]) );
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
													
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size',  'options'=>$bottle_size, 'label' => __( 'Bottle Size', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0]));
			
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
										
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_brand', 'options'=>$jointBrand, 'label' => __( 'Brand', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['brand'][0]));
								
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
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_category', 'options'=>$jointCat, 'label' => __( 'Category', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['category'][0]) );
						
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
			
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_flavour', 'options'=>$jointFlavour, 'label' => __( 'Flavour Type', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['flavour'][0]) );
			
						$mfg_country= array( 0=> 'Select Country');
						$mId = array('');
						//$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 11 ORDER BY country_title");
						$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
						if(!empty($resMfgCountry)){ 
							foreach($resMfgCountry as $r) {	
								array_push($mfg_country, $r->country_title);
								array_push($mId, $r->id);	
							}
								$jointCountry= array_combine($mId, $mfg_country);
						}
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_country',  'options'=>$jointCountry, 'label' => __( 'Country', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_country'][0]) );
			
						$mfg_region= array( 0=> 'Select Region'); 
						$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region WHERE category_id = 11 ORDER BY region_title");
						if(!empty($resMfgRegion)){ 
							foreach($resMfgRegion as $r) {	
								array_push($mfg_region, $r->region_title); 	
							}
						}
											
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_region',  'options'=>$mfg_region, 'label' => __( 'Region', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_region'][0]) );
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
						woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size',  'options'=>$bottle_size, 'label' => __( 'Bottle Size', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0]) );
						
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
						
						woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_brand', 'options'=>$jointBrand, 'label' => __( 'Brand', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['brand'][0] ) );
						
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
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_category', 'options'=>$jointCat, 'label' => __( 'Category', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['category'][0] ) );
			
							$grade= array(''=>'Select Grade',
										'daiginjo'=> 'Daiginjo', 
										'ginjo'=> 'Ginjo', 
										'honjozo'=> 'Honjozo', 
										'junmai'=> 'Junmai');
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_grade', 'options'=>$grade, 'label' => __( 'Grade', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['grade'][0] ) );
			
							$brewery= array( 0=>'Select Brewery',
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
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_brewery', 'options'=>$brewery, 'label' => __( 'Brewery', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['brewery'][0] ) );
			
								$brew_method= array(''=>'Select Brewery Method',
											1 => 'Kimoto',
											2 => 'Sokugo',
											3 => 'Yamahai');
			
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_brew_method', 'options'=>$brew_method, 'label' => __( 'Brewery Method', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['brew_method'][0] ) );
											
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
						woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size',  'options'=>$bottle_size, 'label' => __( 'Bottle Size', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0] ) );
						
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
										
								woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_brand', 'options'=>$jointBrand, 'label' => __( 'Brand', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['brand'][0] ) );
								
							$brewery= array(''=>'Select Brewery',
											1 => 'Anami Oshima',
											2 => 'Asahi',
											3 => 'Denen',
											4 => 'Hamada',
											5 => 'Jikuya');
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_brewery', 'options'=>$brewery, 'label' => __( 'Brewery', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['brewery'][0]) );
			
					$sochu_type= array(''=>'Select Type',
											1 => 'Korui',
											2 => 'Otsurui');
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_sochu_type', 'options'=>$sochu_type, 'label' => __( 'Type of Sochu', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['sochu_type'][0]) );
			
					$sochu_variety= array(''=>'Select Variety',
											1 => 'Genshu',
											2 => 'Hanatare',
											3 => 'Koshu',
											4 => 'Kame/Tsubo Shikomi');
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_sochu_variety', 'options'=>$sochu_variety, 'label' => __( 'Variety of Sochu', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['sochu_variety'][0]) );
			
						
						$mfg_country= array( 0=> 'Select Country');
						$mId = array('');
						//$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 31 ORDER BY country_title");
						$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
						if(!empty($resMfgCountry)){ 
							foreach($resMfgCountry as $r) {	
								array_push($mfg_country, $r->country_title);
								array_push($mId, $r->id); 	
							}
								$jointCountry= array_combine($mId, $mfg_country);
						}
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_country',  'options'=>$jointCountry, 'label' => __( 'Country', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_country'][0]) );
			
						$mfg_region= array( 0=> 'Select Region'); 
						$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region WHERE category_id = 31 ORDER BY region_title");
						if(!empty($resMfgRegion)){ 
							foreach($resMfgRegion as $r) {	
								array_push($mfg_region, $r->region_title); 	
							}
						}
											
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_region',  'options'=>$mfg_region, 'label' => __( 'Region', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_region'][0]) );
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
						woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size',  'options'=>$bottle_size, 'label' => __( 'Bottle Size', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0] ) );
						
						
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
							
						woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_brand', 'options'=>$jointBrand, 'label' => __( 'Brand', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['brand'][0]) );
						
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
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_category', 'options'=>$jointCat, 'label' => __( 'Category', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['category'][0]) );
			
						$distillery= array(''=>'Select Distillery',
										1 => 'Agave Conquista',
										2 => 'Agave Tequilana',
										3 => 'Agaveros Unidos de Amatitan', 
										4 => 'Agaveros y Tequiloeros Unidos de Los Altos', 
										5 => 'Agroindustrias Casa Ramirez');
										
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_distillery', 'options'=>$distillery, 'label' => __( 'Distillery', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['distillery'][0]) );
			
						$cooking= array(''=>'Select Cooking Type',
										1 => 'Brick',
										2 => 'Ceramic',
										3 => 'Clay', 
										4 => 'Diffuser', 
										5 => 'Stainless steel');
										
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_cooking', 'options'=>$cooking, 'label' => __( 'Cooking', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['cooking'][0]) );
			
					$distilation= array(''=>'Select Distilation',
										1 => 'Double',
										2 => 'Triple',
										3 => 'Quadruple', 
										4 => '5x');
										
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_distilation', 'options'=>$distilation, 'label' => __( 'Distilation', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['distilation'][0]) );
			
						$distMethod= array(''=>'Select Cooking Type',
										1 => 'Pot Still-Stainless Steel',
										2 => 'Column Still',
										3 => 'Pot Still-Copper', 
										4 => 'diffuser');
										
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_distMethod', 'options'=>$distMethod, 'label' => __( 'Distillation Method', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['distMethod'][0]) );
			
						$aging= array(''=>'Select Cooking Type',
										1 => 'American Oak',
										2 => 'Blend',
										3 => 'Bordeaux',
										4 => 'Bourbon',
										5 => 'Congan');
			
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_aging', 'options'=>$aging, 'label' => __( 'Barrel Aging Type', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['aging'][0]) );
						
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
													
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size',  'options'=>$bottle_size, 'label' => __( 'Bottle Size', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0]) );
			
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
										
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_brand', 'options'=>$jointBrand, 'label' => __( 'Brand', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['brand'][0]) );
						
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
										
				woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_flavour', 'options'=>$jointFlavour, 'label' => __( 'Flavour Type', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['flavour'][0]) );		
						
						
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
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_category', 'options'=>$jointCat, 'label' => __( 'Category', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['category'][0]) );
			
						$distillery= array(''=>'Select Distillery',
										1 => 'Anguilla',
										2 => '-Anguilla Rum Company blender / bottler',
										3 => '-Antigua and Barbuda', 
										4 => '-Antigua Distillery Limited distiller', 
										5 => '-Barrettos blender / bottler');
										
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_distillery', 'options'=>$distillery, 'label' => __( 'Distillery', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['distillery'][0]) );
						
						$mfg_country= array( 0=> 'Select Country');
						$mId = array('');
						//$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 32 ORDER BY country_title");
						$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
						if(!empty($resMfgCountry)){ 
							foreach($resMfgCountry as $r) {	
								array_push($mfg_country, $r->country_title);
								array_push($mId, $r->id);	
							}
								$jointCountry= array_combine($mId, $mfg_country);
						}
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_country',  'options'=>$jointCountry, 'label' => __( 'Country', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_country'][0]) );
			
						$mfg_region= array( 0=> 'Select Region'); 
						$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region WHERE category_id = 32 ORDER BY region_title");
						if(!empty($resMfgRegion)){ 
							foreach($resMfgRegion as $r) {	
								array_push($mfg_region, $r->region_title); 	
							}
						}
											
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_region',  'options'=>$mfg_region, 'label' => __( 'Region', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_region'][0]) );
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
													
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size',  'options'=>$bottle_size, 'label' => __( 'Bottle Size', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0]) );
			
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
										
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_brand', 'options'=>$jointBrand, 'label' => __( 'Brand', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['brand'][0]) );
			
						$distillery= array(''=>'Select Distillery');
										
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_distillery', 'options'=>$distillery, 'label' => __( 'Distillery', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['distillery'][0]) );
				
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
						
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_flavour', 'options'=>$jointFlavour, 'label' => __( 'Flavour Type', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['flavour'][0]) );
			
					$base= array(''=>'Select Base',
										1 => 'Barley',
										2 => 'Cereal Grains',
										3 => 'Corn',
										4 => 'Fig',
										5 => 'Fruits');
			
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_base', 'options'=>$base, 'label' => __( 'Base', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['base'][0]) );
			
					$distill_No= array(''=>'Select Distillary No',
										4 => '>4',
										5 => '>5',
										6 => '>6',
										7 => '>7',
										8 => '>8');
			
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_distill_No', 'options'=>$distill_No, 'label' => __( 'No of Distillation', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['distill_No'][0]) );
			
						$mfg_country= array( 0=> 'Select Country');
						$mId = array('');
						//$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 34 ORDER BY country_title");
						$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
						if(!empty($resMfgCountry)){ 
							foreach($resMfgCountry as $r) {	
								array_push($mfg_country, $r->country_title);
								array_push($mId, $r->id);	
							}
								$jointCountry= array_combine($mId, $mfg_country);
						}
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_country',  'options'=>$jointCountry, 'label' => __( 'Country', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_country'][0]) );
			
						$mfg_region= array( 0=> 'Select Region'); 
						$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region WHERE category_id = 34 ORDER BY region_title");
						if(!empty($resMfgRegion)){ 
							foreach($resMfgRegion as $r) {	
								array_push($mfg_region, $r->region_title); 	
							}
						}
											
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_region',  'options'=>$mfg_region, 'label' => __( 'Region', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_region'][0]) );
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
												
					woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size', 'options'=>$bottle_size, 'label' => __( 'Bottle Size', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' =>$sp_data['bottle_size'][0]));
				
						$brands = array( 0=> 'Select Brand');
						$bId = array(''); 
						$resBrands = $wpdb->get_results("SELECT * FROM custom_brand WHERE category_id = 24 ORDER BY brand_title");	
						if(!empty($resBrands)){ 
     						foreach($resBrands as $r){	
								array_push($brands, $r->brand_title);
								array_push($bId, $r->id);
							}
								$jointBrand= array_combine($bId, $brands);
						}
										
					woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_brand', 'options'=>$jointBrand, 'label' => __( 'Brand', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['brand'][0] ) );
				
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
										
				woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_flavour', 'options'=>$jointFlavour, 'label' => __( 'Flavour Type', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['flavour'][0] ) );		
						
						
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
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_category', 'options'=>$jointCat, 'label' => __( 'Category', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['category'][0] ) );
			
					$distillery= array(''=>'Select Distillery');
										
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_distillery', 'options'=>$distillery, 'label' => __( 'Distillery', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['distillery'][0]) );
			
					$bottler= array(''=>'Select Bottler');
										
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottler', 'options'=>$bottler, 'label' => __( 'Bottler', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['bottler'][0]) );
			
			woocommerce_wp_text_input( array( 'id' => '_wc_custom_product_tabs_lite_whiskyAge',  'label' => __( 'Whisky Age', self::TEXT_DOMAIN ), 'description' => __( 'Range(1-100) year', self::TEXT_DOMAIN ), 'value' => $sp_data['whiskyAge'][0]));
			
			woocommerce_wp_text_input( array( 'id' => '_wc_custom_product_tabs_lite_distill_date',  'label' => __( 'Distillation  Date', self::TEXT_DOMAIN ), 'description' => __( '(In Year)', self::TEXT_DOMAIN ), 'value' => $sp_data['distill_date'][0]));
			
			woocommerce_wp_text_input( array( 'id' => '_wc_custom_product_tabs_lite_bottling_date',  'label' => __( 'Bottling Date', self::TEXT_DOMAIN ), 'description' => __( '(In Year)', self::TEXT_DOMAIN ), 'value' => $sp_data['bottling_date'][0]));
					
						$mfg_country= array( 0=> 'Select Country');
						$mId = array('');
						//$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 24 ORDER BY country_title");
						$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
						if(!empty($resMfgCountry)){ 
							foreach($resMfgCountry as $r) {	
								array_push($mfg_country, $r->country_title);
								array_push($mId, $r->id); 	
							}
								$jointCountry= array_combine($mId, $mfg_country);
						}
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_country',  'options'=>$jointCountry, 'label' => __( 'Country', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_country'][0]) );
			
						$mfg_region= array( 0=> 'Select Region'); 
						$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region WHERE category_id = 24 ORDER BY region_title");
						if(!empty($resMfgRegion)){ 
							foreach($resMfgRegion as $r) {	
								array_push($mfg_region, $r->region_title); 	
							}
						}
											
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_region',  'options'=>$mfg_region, 'label' => __( 'Region', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_region'][0]) );
			
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
												
					woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size', 'options'=>$bottle_size, 'label' => __( 'Bottle Size', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0]));
				
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
						
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_brand', 'options'=>$jointBrand, 'label' => __( 'Brand', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['brand'][0]));
			
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
			
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_category', 'options'=>$jointCat, 'label' => __( 'Category', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['category'][0] ) );
			
				$variety= array('' => 'Select Variety', 1 => 'Charrdonay', 2 => 'Merlot');
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_variety', 'options'=>$variety, 'label' => __( 'Variety/Grape', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['variety'][0] ) );
			
				$appellation= array('' => 'Select Appellation');
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_appellation', 'options'=>$appellation, 'label' => __( 'Appellation', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['appellation'][0] ) );
			
				$winyard= array('' => 'Select Winyard');
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_winyard', 'options'=>$winyard, 'label' => __( 'Winyard', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['appellation'][0] ) );
			
				$winery= array('' => 'Select Winery');
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_winery', 'options'=>$winery, 'label' => __( 'Winery', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['winery'][0] ) );
						
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
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_vintage', 'options'=>$vintage, 'label' => __( 'Vintage', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['vintage'][0] ) );
													
						$mfg_country= array( 0=> 'Select Country');
						$mId = array('');
						//$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 9 ORDER BY country_title");
						$resMfgCountry = $wpdb->get_results("SELECT *  FROM  `custom_country` GROUP BY country_title ORDER BY country_title");
						if(!empty($resMfgCountry)){ 
							foreach($resMfgCountry as $r) {	
								array_push($mfg_country, $r->country_title);
								array_push($mId, $r->id); 	
							}
								$jointCountry= array_combine($mId, $mfg_country);
						}
						
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_country',  'options'=>$jointCountry, 'label' => __( 'Country', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_country'][0]) );
			
						$mfg_region= array( 0=> 'Select Region'); 
						$resMfgRegion = $wpdb->get_results("SELECT * FROM custom_region WHERE category_id = 9 ORDER BY region_title");
						if(!empty($resMfgRegion)){ 
							foreach($resMfgRegion as $r) {	
								array_push($mfg_region, $r->region_title); 	
							}
						}
											
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_mfg_region',  'options'=>$mfg_region, 'label' => __( 'Region', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['mfg_region'][0]) );
					break;
				default:
			}
			
			woocommerce_wp_text_input( array( 'id' => '_wc_custom_product_tabs_lite_alcohal', 'label' => __( 'Alc./Vol.', self::TEXT_DOMAIN ), 'description' => __( '(Amount in % e.g. 45)', self::TEXT_DOMAIN ), 'value' => $sp_data['alcohal'][0]) );
			
					$rating= array(0=>'Select Rating', 1=>1, 2=>2, 3=>3, 4=>4, 5=>5);
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_rating',  'options'=>$rating, 'label' => __( 'Rating', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['rating'][0]) );
						
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_organic', 'options'=>array(1=>'Yes', 0=>'No'), 'label' => __( 'Org Certification', self::TEXT_DOMAIN ), 'label' => __( 'Org Certifications', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['organic'][0]) );
				
			$gift= array(''=>'Gift Wrapping', 1 => 'True', 0 => 'False');
			
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_gift', 'options'=>$gift, 'label' => __( 'Gift Wrapping', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['gift'][0] ) );
				
			/*woocommerce_wp_radio( array( 'name' => '_wc_custom_product_tabs_lite_ships_to', 'options'=>array(0=>'Any Place', 1=>'Within State', 2=>'Within Country' ), 'label' => __( 'Ships to', self::TEXT_DOMAIN ), 'label' => __( 'Ships to', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['ships_to'][0]) );
				
			woocommerce_wp_radio( array( 'name' => '_wc_custom_product_tabs_lite_delivers_to', 'options'=>array(0=>'Any Place', 1=>'Within State', 2=>'Within Country' ), 'label' => __( 'Delivers to', self::TEXT_DOMAIN ), 'label' => __( 'Delivers to', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['delivers_to'][0]) );*/
				echo '</div></div>';
				
	}	

}
//WooCommerceCustomProductTabsLite:: displaySpecification();
