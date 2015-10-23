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
			$tab_mixed = array( 'bottle_size',
								'brand',
								'distillery',
								'alcohal',
								'rating',
								'category',
								'flavour',
								'organic',
								'cooking',
								'distilation',
								'distMethod',
								'distill_No',
								'aging',
								'base',
								'variety',
								'gift',
								'grade',
								'brewery',
								'brew_method',
								'whiskyAge',
								'distill_date',
								'bottling_date',
								'fruit',
								'mfg_country',
								'mfg_region',
								'ships_to',
								'delivers_to',
								'product_rating');
								
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

		// frontend stuff
		//add_filter( 'woocommerce_product_tabs', array( $this, 'add_custom_product_tabs' ) );

		// allow the use of shortcodes within the tab content
//		add_filter( 'woocommerce_custom_product_tabs_lite_content', 'do_shortcode' );
	}
	

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
		$sp_gift = isset($_POST['_wc_custom_product_tabs_lite_gift'] )?$_POST['_wc_custom_product_tabs_lite_gift']:'';
		$sp_grade = isset($_POST['_wc_custom_product_tabs_lite_grade'] )?$_POST['_wc_custom_product_tabs_lite_grade']:'';
		$sp_brewery = isset($_POST['_wc_custom_product_tabs_lite_brewery'] )?$_POST['_wc_custom_product_tabs_lite_brewery']:'';
		$sp_brew_method = isset($_POST['_wc_custom_product_tabs_lite_brew_method'] )?$_POST['_wc_custom_product_tabs_lite_brew_method']:'';
		
		$sp_aging = isset($_POST['_wc_custom_product_tabs_lite_aging'] )?$_POST['_wc_custom_product_tabs_lite_aging']:'';
		$sp_base = isset($_POST['_wc_custom_product_tabs_lite_base'] )?$_POST['_wc_custom_product_tabs_lite_base']:'';
		
		$sp_whiskyAge = isset($_POST['_wc_custom_product_tabs_lite_whiskyAge'] )?$_POST['_wc_custom_product_tabs_lite_whiskyAge']:'';
		$sp_distill_date = isset($_POST['_wc_custom_product_tabs_lite_distill_date'] )?$_POST['_wc_custom_product_tabs_lite_distill_date']:'';
		$sp_bottling_date = isset($_POST['_wc_custom_product_tabs_lite_bottling_date'] )?$_POST['_wc_custom_product_tabs_lite_bottling_date']:'';
		$sp_fruit = isset($_POST['_wc_custom_product_tabs_lite_fruit'] )?$_POST['_wc_custom_product_tabs_lite_fruit']:'';
		$sp_mfg_country = isset($_POST['_wc_custom_product_tabs_lite_mfg_country'] )?$_POST['_wc_custom_product_tabs_lite_mfg_country']:'';
		$sp_mfg_region = isset($_POST['_wc_custom_product_tabs_lite_mfg_region'] )?$_POST['_wc_custom_product_tabs_lite_mfg_region']:'';
		$sp_ships_to = isset($_POST['_wc_custom_product_tabs_lite_ships_to'] )?$_POST['_wc_custom_product_tabs_lite_ships_to']:'';
		$sp_delivers_to = isset($_POST['_wc_custom_product_tabs_lite_delivers_to'] )?$_POST['_wc_custom_product_tabs_lite_delivers_to']:'';
		
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
							'distMethod'=> $sp_distMethod,
							'distill_No'=> $sp_distill_No,
							'aging'=> $sp_aging,
							'base'=> $sp_base,
							'variety'=> $sp_variety,
							'gift'=> $sp_gift,
							'grade'=> $sp_grade,
							'brewery'=> $sp_brewery,
							'brew_method'=> $sp_brew_method,
							'whiskyAge'=> $sp_whiskyAge,
							'distill_date'=> $sp_distill_date,
							'bottling_date'=> $sp_bottling_date,
							'fruit'=> $sp_fruit,
							'mfg_country'=> $sp_mfg_country,
							'mfg_region'=> $sp_mfg_region,
							'ships_to'=> $sp_ships_to,
							'delivers_to'=> $sp_delivers_to,
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
												
					woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size', 'options'=>$bottle_size, 'label' => __( 'Bottle Size*', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0]));
				
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
												
					woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size', 'options'=>$bottle_size, 'label' => __( 'Bottle Size*', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' =>$sp_data['bottle_size'][0]));
				
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
				
					$fruits= array('' => 'Select Fruit','apple'=>'Apple', 'apricot'=>'Apricot', 'blueberry'=>'Blueberry', 'cherry'=>'Cherry', 'peach'=>'Peach');
			woocommerce_wp_select(array( 'id' => '_wc_custom_product_tabs_lite_fruit',  'options'=>$fruits, 'label' => __( 'Fruit', self::TEXT_DOMAIN ), 'description' => '', 'value' => $sp_data['fruit'][0] ) );
			
							
						$mfg_country= array( 0=> 'Select Country');
						$mId = array(''); 
						$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 28 ORDER BY country_title");
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
								woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_production', 'options'=>$production, 'label' => __( 'Production Method', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['production'][0]) );
								
								$production= array (''=> 'Select Production Method', 
													1 => 'Column Distilled Gin',	
													2 => 'Compound Gin',		
													3 => 'Post distilled gin');
						woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_production', 'options'=>$production, 'label' => __( 'Production Method', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['production'][0]) );
						
				
								$mfg_country= array( 0=> 'Select Country');
								$mId = array('');
								$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 29 ORDER BY country_title");
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
				case 'Liquer':
						
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
													
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size',  'options'=>$bottle_size, 'label' => __( 'Bottle Size*', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0]));
			
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
						$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 11 ORDER BY country_title");
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
						woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size',  'options'=>$bottle_size, 'label' => __( 'Bottle Size*', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0]) );
						
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
			
							$brewery= array(''=>'Select Brewery',
											0 => 'Akita Seishu Shuzo',
											1 => 'Asahi Shuzo',
											2 => 'Asamai Shuzo',
											3 => 'Gochoda Shuzo',
											4 => 'Hakkai Jozo',
											5 => 'Imada Shuzo',
											6 => 'Ikegami Shuzo',
											7 => 'Ishikawa Shuzo',
											8 => 'Kaetsu Shuzo',
											9 => 'Kamotsuru Shuzo',
											10 => 'Katokichibee Shoten',
											11 => 'Kikumasamune Shuzo',
											12 => 'Kikusui Shizo',
											13 => 'Masuichi Ichimura Shuzo',
											14 => 'Miyao Shuzo',
											15 => 'Miyozakura Shuzo',
											16 => 'Muromachi Shuzo',
											17 => 'Nanbubijin Co. Ltd.',
											18 => 'Ryujin Shuzo',
											19 => 'akeroku Shuzo',
											20 => 'Shata Shuzo',
											21 => 'Sudo Honke Shuzo',
											22 => 'Tabata Shuzo',
											23 => 'Takara Shuzo',
											24 => 'Takasago Shuzo',
											25 => 'Takenotsuyu Shuzo',
											26 => 'Tamanohikari Shuzo',
											27 => 'Taruhei Shuzo',
											28 => 'Totsuka Shuzo',
											29 => 'Toyosawa Shuzo',
											30 => 'Uehara Shuzo');
						
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
													1800 => '1800 ml (1.8L)',	
													2000 => '2000 ml (2L)');
						woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size',  'options'=>$bottle_size, 'label' => __( 'Bottle Size*', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0] ) );
						
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
			
						
						$mfg_country= array( 0=> 'Select Country');
						$mId = array('');
						$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 31 ORDER BY country_title");
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
													1750 => '1750 ml (1.75L)');
						woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size',  'options'=>$bottle_size, 'label' => __( 'Bottle Size*', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0] ) );
						
						
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
													
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size',  'options'=>$bottle_size, 'label' => __( 'Bottle Size*', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0]) );
			
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
						$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 32 ORDER BY country_title");
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
													
			woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size',  'options'=>$bottle_size, 'label' => __( 'Bottle Size*', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0]) );
			
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
						$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 34 ORDER BY country_title");
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
												
					woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size', 'options'=>$bottle_size, 'label' => __( 'Bottle Size*', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' =>$sp_data['bottle_size'][0]));
				
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
			
			woocommerce_wp_text_input( array( 'id' => '_wc_custom_product_tabs_lite_whiskyAge',  'label' => __( 'Whisky Age', self::TEXT_DOMAIN ), 'description' => __( 'Range(1-100) year', self::TEXT_DOMAIN ), 'value' => $sp_data['whiskyAge'][0]));
			
			woocommerce_wp_text_input( array( 'id' => '_wc_custom_product_tabs_lite_distill_date',  'label' => __( 'Distillation  Date', self::TEXT_DOMAIN ), 'description' => __( '(In Year)', self::TEXT_DOMAIN ), 'value' => $sp_data['distill_date'][0]));
			
			woocommerce_wp_text_input( array( 'id' => '_wc_custom_product_tabs_lite_bottling_date',  'label' => __( 'Bottling Date', self::TEXT_DOMAIN ), 'description' => __( '(In Year)', self::TEXT_DOMAIN ), 'value' => $sp_data['bottling_date'][0]));
					
						$mfg_country= array( 0=> 'Select Country');
						$mId = array('');
						$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 24 ORDER BY country_title");
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
												
					woocommerce_wp_select( array( 'id' => '_wc_custom_product_tabs_lite_bottle_size', 'options'=>$bottle_size, 'label' => __( 'Bottle Size*', self::TEXT_DOMAIN ), 'description' => __( '(In ml) ', self::TEXT_DOMAIN ), 'value' => $sp_data['bottle_size'][0]));
				
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
						
													
						$mfg_country= array( 0=> 'Select Country');
						$mId = array('');
						$resMfgCountry = $wpdb->get_results("SELECT * FROM custom_country WHERE category_id = 9 ORDER BY country_title");
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
				
			woocommerce_wp_radio( array( 'name' => '_wc_custom_product_tabs_lite_ships_to', 'options'=>array(0=>'Any Place', 1=>'Within State', 2=>'Within Country' ), 'label' => __( 'Ships to', self::TEXT_DOMAIN ), 'label' => __( 'Ships to', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['ships_to'][0]) );
				
			woocommerce_wp_radio( array( 'name' => '_wc_custom_product_tabs_lite_delivers_to', 'options'=>array(0=>'Any Place', 1=>'Within State', 2=>'Within Country' ), 'label' => __( 'Delivers to', self::TEXT_DOMAIN ), 'label' => __( 'Delivers to', self::TEXT_DOMAIN ), 'description' => __( '', self::TEXT_DOMAIN ), 'value' => $sp_data['delivers_to'][0]) );
				echo '</div></div>';
				
	}	

}