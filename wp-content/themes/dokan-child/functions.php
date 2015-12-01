<?php
add_action( 'wp_enqueue_scripts', 'new_styles' );
function new_styles()
{ 
	wp_enqueue_style( 'style-child', get_stylesheet_directory_uri().'/style.css');
}

add_action( 'admin_enqueue_scripts', 'wpb_spec_scripts'); 	
function wpb_spec_scripts() {
 	wp_enqueue_script('my_spec_script', get_stylesheet_directory_uri().'/js/ajax-spec.js');
}

add_action( 'wp_enqueue_scripts', 'wp_spec_scripts'); 	
function wp_spec_scripts() {
 	wp_enqueue_script('dokan_spec_script', get_stylesheet_directory_uri().'/js/dokan-ajax-spec.js');
}


add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );

// Load our function when hook is set

add_action( 'pre_get_posts', 'rc_modify_query_get_posts_by_date' );
// Modify the current query

function rc_modify_query_get_posts_by_date( $query ) {

global $wpdb;
   
	  	if( ! is_admin() && is_search() ) 
	  	{

			$authors=array();
			$metaQry= array();
			$querystr = "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = 'dokan_profile_settings'";
			$result = $wpdb->get_results($querystr, OBJECT);
			foreach ( $result as $user )
			{
			   $authors[]= $user->user_id;
			}
				$sellerExist= getAuthorIds($authors);
			/*********************************** Filtering for discounted Items Search ********************************/
			/*if(isset($_REQUEST['discount']) && $_REQUEST['discount']==true)
			{
				$query->set( 'meta_query', array(
											  array( // Simple products type
															'key' => '_sale_price',
															'value' => 0,
															'compare' => '>',
															'type' => 'numeric'
															),
												array( // Variable products type
												'key' => '_min_variation_sale_price',
												'value' => 0,
												'compare' => '>',
												'type' => 'numeric'
												),
										)
                              );
			}*/
			/*********************************** Filtering for discounted Items Search ********************************/
			if(isset($_REQUEST['FLAG']) && $_REQUEST['FLAG']=='QUICK_SEARCH')
			{
					if(isset($_REQUEST['discount']) && $_REQUEST['discount']==true)
					{
									$metaQry[] =	array( // Simple products type
																	'key' => '_sale_price',
																	'value' => 0,
																	'compare' => '>',
																	'type' => 'numeric'
														);
									$metaQry[] =	array( // Variable products type
																'key' => '_min_variation_sale_price',
																'value' => 0,
																'compare' => '>',
																'type' => 'numeric'
														);
					}else{
									$metaQry[] =	array( // Simple products type
																	'key' => '_sale_price',
																	'value' => 0,
																	'compare' => '=',
																	'type' => 'numeric'
														);
									$metaQry[] =	array( // Simple products type
																	'key' => '_min_variation_sale_price',
																	'compare' => 'NOT EXISTS',
														);
					}
			}else{
					if(isset($_REQUEST['discount']) && $_REQUEST['discount']==true)
					{
									$metaQry[] =	array( // Simple products type
																	'key' => '_sale_price',
																	'value' => 0,
																	'compare' => '>',
																	'type' => 'numeric'
														);
									$metaQry[] =	array( // Variable products type
																'key' => '_min_variation_sale_price',
																'value' => 0,
																'compare' => '>',
																'type' => 'numeric'
														);
					}
			}
			/************************************* Filtering for zipcode Search ********************************/
			if((isset($_REQUEST['zipcode']) && $_REQUEST['zipcode']!='') && (isset($_REQUEST['radius']) && $calDist<= $_REQUEST['radius']) && (!isset($_REQUEST['delivers_to']) || $_REQUEST['delivers_to']=='') && (!isset($_REQUEST['item_loc']) || $_REQUEST['item_loc']==''))
			{	
						if(empty($sellerExist))
						{
							$query->set( 'author__in', array(-10) );
						}else{
							$query->set( 'author__in', $sellerExist);
						}
			}else if((isset($_REQUEST['ads_state']) && $_REQUEST['ads_state']!='') && (!isset($_REQUEST['delivers_to']) || $_REQUEST['delivers_to']=='') && (!isset($_REQUEST['item_loc']) || $_REQUEST['item_loc']==''))
			{	

						$sellerShips= checkForShippingState($authors);
						if(empty($sellerShips))
						{
							 $query->set( 'author__in', array(-10));
						}else{
							 $query->set( 'author__in', $sellerShips);
						}
			}else if((isset($_REQUEST['del_loc']) && $_REQUEST['del_loc']!='') && (!isset($_REQUEST['ads_state']) || $_REQUEST['ads_state']=='') && (!isset($_REQUEST['item_loc']) || $_REQUEST['item_loc']==''))
			{	
						$pLatLong= getLnt($_REQUEST['del_loc']);
						$sellerDelDist=  getDeliveryDistanceSeller($authors, $pLatLong);
						//print_r($sellerDelDist);
						//exit;
						if(empty($sellerDelDist))
						{
							$query->set( 'author__in', array(-10) );
						}else{
							$query->set( 'author__in', $sellerDelDist);
						}
			}elseif((isset($_REQUEST['del_loc']) && $_REQUEST['del_loc']!='') && (isset($_REQUEST['ads_state']) && $_REQUEST['ads_state']!='') && (!isset($_REQUEST['item_loc']) || $_REQUEST['item_loc']==''))
			{
						$pLatLong= (isset($_REQUEST['del_loc']) && $_REQUEST['del_loc']!='')?getLnt($_REQUEST['del_loc']):'';
						$sellerShips= checkForShippingState($authors);
						$sellerDelDist=  getDeliveryDistanceSeller($authors, $pLatLong);
						$combArr= array_merge($sellerShips, $sellerDelDist);
						if(empty($combArr))
						{
							$query->set( 'author__in', array(-10) );
						}else{
							$query->set( 'author__in', $combArr);
						}
			}elseif(isset($_REQUEST['item_loc']) && $_REQUEST['item_loc']!='')
			{
						$itemsLocation= checkForItemLocation($authors, $_REQUEST['item_loc']);
						//print_r($itemsLocation);
						//exit;
						if(empty($itemsLocation))
						{
								
							$query->set( 'author__in', array(-10));
						}else{
							$query->set( 'author__in', $itemsLocation);
						}
			}
			else{
						$query->set( 'author__in', $authors);
			}
			/***************************** Filtering for Bottle Size Search ********************************/
			if(isset($_REQUEST['bottle_size']) && $_REQUEST['bottle_size']!='')
			{ 
						$metaQry[] =  array( // Simple products type
												'key' => 'bottle_size',
												'value' => $_REQUEST['bottle_size'],
												'compare' => '=',
												'type' => 'numeric'
												);
			}
			/************************************ Filtering for Brand Search ***********************************/
			if((isset($_REQUEST['brand']) && ($_REQUEST['brand']!='' && $_REQUEST['brand']!=0)))
			{
						$metaQry[] =	array( // Simple products type
														'key' => 'brand',
														'value' => $_REQUEST['brand'],
														'type' => 'numeric'
										);
			}
			/********************************** Filtering for Category Search **********************************/
			if((isset($_REQUEST['category']) && ($_REQUEST['category']!='' && $_REQUEST['category']!=0)))
			{
						$metaQry[] = array( // Simple products type
														'key' => 'category',
														'value' => $_REQUEST['category'],
														'type' => 'numeric'
											);
			}
			/********************************** Filtering for Category Search **********************************/
			if((isset($_REQUEST['rating_min']) && ($_REQUEST['rating_min']>=0))  && (isset($_REQUEST['rating']) && ($_REQUEST['rating']<5 && $_REQUEST['rating']!='')))
			{
					$metaQry[] = array( // Simple products type
												'key' => 'rating',
												'value' => $_REQUEST['rating_min'],
												'compare' => '>=',
												'type' => 'numeric'
										);
					$metaQry[] = array( // Simple products type
												'key' => 'rating',
												'value' => $_REQUEST['rating'],
												'compare' => '<=',
												'type' => 'numeric'
										);
			}
			else if((isset($_REQUEST['rating_min']) && ($_REQUEST['rating_min']>=0))  && (isset($_REQUEST['rating']) && $_REQUEST['rating']==5))
			{
					$metaQry[] = array( // Simple products type
												'key' => 'rating',
												'value' => $_REQUEST['rating'],
												'compare' => '<=',
												'type' => 'numeric'
										);
			}
			else if((isset($_REQUEST['rating'])) && ($_REQUEST['rating']!='' && $_REQUEST['rating']!=0)){
				$metaQry[] = array( // Simple products type
												'key' => 'rating',
												'value' => $_REQUEST['rating'],
												'compare' => '=',
												'type' => 'numeric'
										);
			}
			/*********************************** Filtering for Flavour Search ********************************/
			if((isset($_REQUEST['flavour']) && ($_REQUEST['flavour']!='' && $_REQUEST['flavour']!=0)))
			{
						$metaQry[] =  array( // Simple products type
													'key' => 'flavour',
													'value' => $_REQUEST['flavour'],
													'type' => 'numeric'
										);
			}
			/*********************************** Filtering for Fruit Search ********************************/
			if((isset($_REQUEST['fruit']) && ($_REQUEST['fruit']!='' && $_REQUEST['fruit']!=0)))
			{
						$metaQry[] =  array( // Simple products type
													'key' => 'fruit',
													'value' => $_REQUEST['fruit'],
													'type' => 'numeric'
											);
			}
			/*********************************** Filtering for Production Search ********************************/
			if((isset($_REQUEST['production']) && ($_REQUEST['production']!='' && $_REQUEST['production']!=0)))
			{
						$metaQry[] =  array( // Simple products type
													'key' => 'production',
													'value' => $_REQUEST['production'],
													'type' => 'numeric'
										);
			}
			/*********************************** Filtering for Grade Search ********************************/
			if((isset($_REQUEST['grade']) && ($_REQUEST['grade']!='' && $_REQUEST['grade']!=0)))
			{
						$metaQry[] = array( // Simple products type
													'key' => 'grade',
													'value' => $_REQUEST['grade'],
													'type' => 'numeric'
										);
			}
			/*********************************** Filtering for Brewery Search ********************************/
			if((isset($_REQUEST['brewery']) && ($_REQUEST['brewery']!='' && $_REQUEST['brewery']!=0)))
			{
						$metaQry[] =	array( // Simple products type
													'key' => 'brewery',
													'value' => $_REQUEST['brewery'],
													'type' => 'numeric'
											);
			}
			/*********************************** Filtering for Sochu Type Search ********************************/
			if((isset($_REQUEST['sochu_type']) && ($_REQUEST['sochu_type']!='' && $_REQUEST['sochu_type']!=0)))
			{
						$metaQry[] =	array( // Simple products type
													'key' => 'sochu_type',
													'value' => $_REQUEST['sochu_type'],
													'type' => 'numeric'
											);
			}
			/*********************************** Filtering for Sochu Variety Search ********************************/
			if((isset($_REQUEST['sochu_variety']) && ($_REQUEST['sochu_variety']!='' && $_REQUEST['sochu_variety']!=0)))
			{
						$metaQry[] =	array( // Simple products type
													'key' => 'sochu_variety',
													'value' => $_REQUEST['sochu_variety'],
													'type' => 'numeric'
											);
			}
			/********************************* Filtering for Brewery Method Search ****************************/
			if((isset($_REQUEST['brew_method']) && ($_REQUEST['brew_method']!='' && $_REQUEST['brew_method']!=0)))
			{
						$metaQry[] =   array( // Simple products type
													'key' => 'brew_method',
													'value' => $_REQUEST['brew_method'],
													'type' => 'numeric'
										);
			}
			/********************************* Filtering for Brewery Location Search ****************************/
			if((isset($_REQUEST['brew_loc']) && ($_REQUEST['brew_loc']!='' && $_REQUEST['brew_loc']!=0)))
			{
						$metaQry[] =   array( // Simple products type
													'key' => 'brew_loc',
													'value' => $_REQUEST['brew_loc'],
													'type' => 'numeric'
										);
			}
			/************************************ Filtering for Base Search ***********************************/
			if((isset($_REQUEST['base']) && ($_REQUEST['base']!='' && $_REQUEST['base']!=0)))
			{
						$metaQry[] = 	array( // Simple products type
													'key' => 'base',
													'value' => $_REQUEST['base'],
													'type' => 'numeric'
										);
			}
			/*********************************** Filtering for Distillation No Search ****************************/
			if((isset($_REQUEST['distill_No']) && ($_REQUEST['distill_No']!='' && $_REQUEST['distill_No']!=0)))
			{
						$metaQry[] = 	array( // Simple products type
													'key' => 'distill_No',
													'value' => $_REQUEST['distill_No'],
													'type' => 'numeric'
										);
			}
			/*********************************** Filtering for Appellation Search ****************************/
			if((isset($_REQUEST['appellation']) && ($_REQUEST['appellation']!='' && $_REQUEST['appellation']!=0)))
			{
						$metaQry[] =	array( // Simple products type
													'key' => 'appellation',
													'value' => $_REQUEST['appellation'],
													'type' => 'numeric'
										);
			}
			/*********************************** Filtering for Variety Search ****************************/
			if((isset($_REQUEST['variety']) && ($_REQUEST['variety']!='' && $_REQUEST['variety']!=0)))
			{
						$metaQry[] =	array( // Simple products type
													'key' => 'variety',
													'value' => $_REQUEST['variety'],
													'type' => 'numeric'
										);
			}
			/*********************************** Filtering for Vinyard Search ****************************/
			if((isset($_REQUEST['vinyard']) && ($_REQUEST['vinyard']!='' && $_REQUEST['vinyard']!=0)))
			{
						$metaQry[] =	array( // Simple products type
													'key' => 'vinyard',
													'value' => $_REQUEST['vinyard'],
													'type' => 'numeric'
										);
			}
			/*********************************** Filtering for Winery Search ****************************/
			if((isset($_REQUEST['winery']) && ($_REQUEST['winery']!='' && $_REQUEST['winery']!=0)))
			{
						$metaQry[] =	array( // Simple products type
													'key' => 'winery',
													'value' => $_REQUEST['winery'],
													'type' => 'numeric'
										);
			}
			/*********************************** Filtering for Winery Search ****************************/
			if((isset($_REQUEST['vintage']) && ($_REQUEST['vintage']!='' && $_REQUEST['vintage']!=0)))
			{
						$metaQry[] =	array( // Simple products type
													'key' => 'vintage',
													'value' => $_REQUEST['vintage'],
													'type' => 'numeric'
										);
			}
			/*********************************** Filtering for Whisky Age Search ****************************/
			if((isset($_REQUEST['whiskyAge']) && ($_REQUEST['whiskyAge']!='' && $_REQUEST['whiskyAge']!=0)))
			{
						$metaQry[] =	  array( // Simple products type
														'key' => 'whiskyAge',
														'value' => $_REQUEST['whiskyAge'],
														'type' => 'numeric'
												);
						$metaQry[] =	 array( // Simple products type
														'key' => 'alcohal',
														'value' => '',
														'compare' => '<',
														'type' => 'numeric'
										);
			}
			/********************************* Filtering for Distillery Search *********************************/
			if(isset($_REQUEST['distillery']) && $_REQUEST['distillery']!='')
			{
						$metaQry[] =	array( // Simple products type
														'key' => 'distillery',
														'value' => $_REQUEST['distillery'],
														'compare' => '<=',
														'type' => 'numeric'
												);
						$metaQry[] =	array( // Simple products type
														'key' => 'distillery',
														'value' => '',
														'compare' => 'NOT IN',
														'type' => 'char'
											);
			}
			/***************************** Filtering for Product Bottler Search ********************************/
			if((isset($_REQUEST['bottler']) && ($_REQUEST['bottler']!='' && $_REQUEST['bottler']!=0)))
			{
							$metaQry[] =	array( // Simple products type
														'key' => 'bottler',
														'value' => $_REQUEST['bottler'],
														'type' => 'numeric'
											);
			}
			/*********************************** Filtering for Distillation Date ****************************/
			if((isset($_REQUEST['distill_date']) && ($_REQUEST['distill_date']!='' && $_REQUEST['distill_date']!=0)))
			{
						$metaQry[] =	array( // Simple products type
														'key' => 'distill_date',
														'value' => $_REQUEST['distill_date'],
														'compare' => '>',
														'type' => 'numeric'
												);
						$metaQry[] =	array( // Simple products type
														'key' => 'distill_date',
														'value' => $_REQUEST['distill_date'],
														'type' => 'numeric'
											);
			}
			/*********************************** Filtering for Bottling Date ****************************/
			if((isset($_REQUEST['bottling_date']) && ($_REQUEST['bottling_date']!='' && $_REQUEST['bottling_date']!=0)))
			{
						$metaQry[] =    array( // Simple products type
														'key' => 'bottling_date',
														'value' => $_REQUEST['bottling_date'],
														'compare' => '>',
														'type' => 'numeric'
												);
						$metaQry[] =	 array( // Simple products type
														'key' => 'bottling_date',
														'value' => $_REQUEST['bottling_date'],
														'type' => 'numeric'
										);
			}
			/***************************** Filtering for Organic Search ************************************/
			if(isset($_REQUEST['organic']) && $_REQUEST['organic']!='')
			{
							$metaQry[] =   array( // Simple products type
														'key' => 'organic',
														'value' => $_REQUEST['organic'],
														'type' => 'numeric'
											);
			}
			/***************************** Filtering for Attribute Search ************************************/
			if(isset($_REQUEST['attribute']) && $_REQUEST['attribute']!='')
			{
							$metaQry[] =   array( // Simple products type
														'key' => 'attribute',
														'value' => $_REQUEST['attribute'],
														'type' => 'numeric'
											);
			}
			/***************************** Filtering for Attribute Search ************************************/
			if(isset($_REQUEST['packaging']) && $_REQUEST['packaging']!='')
			{
							$metaQry[] =   array( // Simple products type
														'key' => 'packaging',
														'value' => $_REQUEST['packaging'],
														'type' => 'numeric'
											);
			}
			/***************************** Filtering for Gift Wrap Search ************************************/
			if(isset($_REQUEST['gift']) && $_REQUEST['gift']!='')
			{
							$metaQry[] =  array( // Simple products type
														'key' => 'gift',
														'value' => $_REQUEST['gift'],
														'type' => 'char'
											);
			}
			/***************************** Filtering for Color Search ************************************/
			if(isset($_REQUEST['color']) && $_REQUEST['color']!='')
			{
							$metaQry[] =  array( // Simple products type
														'key' => 'color',
														'value' => $_REQUEST['color'],
														'type' => 'numeric'
											);
			}
			/*********************************** Filtering for Country Search ****************************/
			if(isset($_REQUEST['mfg_country']) && $_REQUEST['mfg_country']!='')
			{
						$metaQry[] =	array( // Simple products type
														'key' => 'mfg_country',
														'value' => $_REQUEST['mfg_country'],
														'type' => 'numeric'
										);
			}
			/*********************************** Filtering for Region Search ****************************/
			if((isset($_REQUEST['mfg_region']) && ($_REQUEST['mfg_region']!='' && $_REQUEST['mfg_region']!=0)))
			{
						$metaQry[] =	array( // Simple products type
														'key' => 'mfg_region',
														'value' => $_REQUEST['mfg_region'],
														'type' => 'numeric'
										);
			}
			/***************************** Filtering for Alcohal amount Search ************************************/
			if((isset($_REQUEST['alcohal']) && $_REQUEST['alcohal']!='') && (($_REQUEST['alcohal_min']==0) || ($_REQUEST['alcohal_min']=='')))
			{
							$metaQry[]=	array( 'relation' => 'OR',
												array( // Simple products type
														'key' => 'alcohal',
														'value' => $_REQUEST['alcohal'],
														'compare' => '<=',
														'type' => 'numeric'
												),
												array(
													'key'     => 'alcohal',
													'value'   => '',
													'compare' => '='
											));
			}else if((isset($_REQUEST['alcohal']) && $_REQUEST['alcohal']!='') && (isset($_REQUEST['alcohal_min']) && $_REQUEST['alcohal_min']>0))
			{
				          $metaQry[]= array( // Simple products type
														'key' => 'alcohal',
														'value' => $_REQUEST['alcohal'],
														'compare' => '<=',
														'type' => 'numeric'
												);
							$metaQry[]=	array( // Simple products type
														'key' => 'alcohal',
														'value' => $_REQUEST['alcohal_min'],
														'compare' => '>=',
														'type' => 'numeric'
											);
			}
			/***********************************************************************************************/
			$query->set( 'meta_query', array('relation' => 'AND', $metaQry ) );
		}
		//print_r($query);
		return $query;
}

function getLnt($zip){
	$url = "http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($zip)."&sensor=false";
	$result_string = file_get_contents($url);
	$result = json_decode($result_string, true);
	$result1[]=$result['results'][0];
	$result2[]=$result1[0]['geometry'];
	$result3[]=$result2[0]['location'];
	return $result3[0];
}
function distance($lat1, $lon1, $lat2, $lon2, $unit) {
  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);
  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}
function getAuthorIds($author){		
		global $sellerArr;
		$sellerArr=array();
		if(isset($_REQUEST['zipcode']) && $_REQUEST['zipcode']!='') $prZip= $_REQUEST['zipcode']; else echo $prZip='';
		if(isset($prZip) && $prZip!='')
		{
			$pll= getLnt($prZip);  
		}
		else{
			$pll['lat']=-73.9661407;  
			$pll['lng']= 40.7841484;
		}
		$i=0;
		$isSellerFeatured='';
		foreach ($author as $user) {				 
			$store_info = dokan_get_store_info($user);
			$seller_enable = dokan_is_seller_enabled($user);
			$isSellerFeatured = get_user_meta($user, 'dokan_feature_seller', true);
			if ($seller_enable) {
				$map_location = isset( $store_info['location'] ) ? esc_attr( $store_info['location'] ) : '';
				$store_name = isset( $store_info['store_name'] ) ? esc_html( $store_info['store_name'] ) : __( 'N/A', 'dokan' );
				$store_url  = dokan_get_store_url($user);
				$seller[$i]['id'] = $user;
				$seller[$i]['store_name'] = $store_name;
				$seller[$i]['store_url'] = $store_url;
				$seller[$i]['featured'] = $isSellerFeatured;
				$seller[$i]['address'] = isset( $store_info['address'] ) ? esc_attr( $store_info['address'] ) : 'N/A';;
				$locations = explode( ',', $map_location );
				$seller[$i]['lat'] = (isset($locations[0]) && $locations[0]!='') ? $locations[0] : '0';
				$seller[$i]['long'] = isset($locations[1] ) ? $locations[1] : '0';
			}
				
				$calDist= distance($pll['lat'], $pll['lng'], $seller[$i]['lat'], $seller[$i]['long'], "M");
				if((isset($_REQUEST['zipcode']) && $_REQUEST['zipcode']!='') && (isset($_REQUEST['radius']) && $calDist<= $_REQUEST['radius']))
				{
					  $sellerArr[]= $seller[$i]['id'];
				}elseif((isset($_REQUEST['zipcode']) && $_REQUEST['zipcode']!='') && $_REQUEST['radius']=='')
				{
					  $sellerArr[]= $seller[$i]['id'];
				}else{
					 
				}
			$i++;
		}
		return array_unique($sellerArr);
}
function getSellerInfo($sellerArr){
		if(isset($_REQUEST['zipcode']) && $_REQUEST['zipcode']!='') $prZip= $_REQUEST['zipcode']; else echo $prZip='';
		if(isset($prZip) && $prZip!='')
		{
			$pll= getLnt($prZip);  
		}
		else{
			$pll['lat']=-73.9661407;  
			$pll['lng']= 40.7841484;
		}
		$i=0;
		$isSellerFeatured='';
		$seller = array();
		foreach ($sellerArr as $user) {				 
			$store_info = dokan_get_store_info($user);
			$seller_enable = dokan_is_seller_enabled($user);
			$isSellerFeatured = get_user_meta($user, 'dokan_feature_seller', true);
			if ($seller_enable) {
				$map_location = isset( $store_info['location'] ) ? esc_attr( $store_info['location'] ) : '';
				$store_name = isset( $store_info['store_name'] ) ? esc_html( $store_info['store_name'] ) : __( 'N/A', 'dokan' );
				$store_url  = dokan_get_store_url($user);
				$seller[$i]['id'] = $user;
				$seller[$i]['store_name'] = $store_name;
				$seller[$i]['store_url'] = $store_url;
				$seller[$i]['featured'] = $isSellerFeatured;
				$seller[$i]['address'] = isset( $store_info['address'] ) ? esc_attr( $store_info['address'] ) : 'N/A';;
				$locations = explode( ',', $map_location );
				$seller[$i]['lat'] = (isset($locations[0]) && $locations[0]!='') ? $locations[0] : '0';
				$seller[$i]['long'] = isset($locations[1] ) ? $locations[1] : '0';
				$i++;
			}				
		}
		return $seller;
}
function get_product_category_slug_by_id( $category_id ) {
	$term = get_term_by( 'id', $category_id, 'product_cat', 'ARRAY_A' );
	return $term['slug'];
}
add_filter( 'woocommerce_page_title', 'custom_woocommerce_page_title');
function custom_woocommerce_page_title( $page_title ) {
  
		if(isset($page_title) && (!isset($_REQUEST['s']) || $_REQUEST['s']=='')) 
		{
				if(!isset($_REQUEST['product_cat']) || $_REQUEST['product_cat']=='')
				{
					$page_title= "Search Results: ";
				}else{
					$page_title= "Search Results: ".'&ldquo;'.ucwords($_REQUEST['product_cat']).'&rdquo;';
				}
		}
		else if(isset($page_title) && (isset($_REQUEST['s']) && $_REQUEST['s']!='')) 
		{
				if(!isset($_REQUEST['product_cat']) || $_REQUEST['product_cat']=='')
				{
					$page_title= "Search Results: ".'&ldquo;'.$_REQUEST['s'].'&rdquo;';
				}
				else{
					$page_title= "Search Results: ".'&ldquo;'.$_REQUEST['s'].'&rdquo; + '.'&ldquo;'.ucwords($_REQUEST['product_cat']).'&rdquo;';
				}
		}
		 return $page_title;
}
/***************************************************************************************************************************/
function getDeliveryDistanceSeller($sellerArr, $pLatLong){
		$sellerByDelDist=array();
		foreach ($sellerArr as $user) {
			$delDist=0;				 
			$store_info = dokan_get_store_info($user);
			$seller_enable = dokan_is_seller_enabled($user);
			$isSellerFeatured = get_user_meta($user, 'dokan_feature_seller', true);
			$delDist = get_user_meta($user, '_dps_delivery_distance', true);
			
			$map_location = isset($store_info['location'])? esc_attr( $store_info['location'] ) : '';
			$locations = explode( ',', $map_location );
			$lat1 = (isset($locations[0]) && $locations[0]!='')? $locations[0] : 0;
			$lon1 = isset($locations[1] ) ? $locations[1] : 0;
			
			$lat2=$lon2=0;
			if(isset($_REQUEST['del_loc']) && $_REQUEST['del_loc']!='')
			{
				$lat2 = $pLatLong['lat'];
				$lon2 = $pLatLong['lng'];
				$schByDistance= distance($lat1, $lon1, $lat2, $lon2, '');
			}				
			if((isset($delDist) && $delDist>0) && (isset($schByDistance) && $schByDistance>0) && ($schByDistance<= $delDist))
			{
				   $sellerByDelDist[]= $user;
			}
		}
		return $sellerByDelDist;
}
/***************************************************************************************************************************/
function checkForShippingState($sellerArr){

		$sellerByShip=array();
		foreach ($sellerArr as $user) {			             
            $dps_country_rates = get_user_meta( $user, '_dps_country_rates', true );
            $dps_state_rates   = get_user_meta( $user, '_dps_state_rates', true ); 
			
			$dps_country = (isset( $dps_country_rates ) ) ? $dps_country_rates : array();
            $dps_state = (isset( $dps_state_rates[$_REQUEST['ads_country']]))?$dps_state_rates[$_REQUEST['ads_country']]:array();
			
			if(empty($dps_state))
			{
				$errors[] = '<a href="%s">%s (No Stores Available to the Shipping Location)</a>';
			}else{
				if( array_key_exists( $_REQUEST['ads_country'], $dps_country ) ) 
				{      
						if( $dps_state) 
						{
								if( array_key_exists($_REQUEST['ads_state'], $dps_state ) ) {
									$sellerByShip[]= $user;
								} elseif ( array_key_exists( 'everywhere', $dps_state ) ) {
									
								}    
						} 
			  	}
			}
		}
		return $sellerByShip;
}
/***************************************************************************************************************************/
function checkForItemLocation($sellerArr, $searchedLoc){

		$sellerByItemLoc=array();
		foreach ($sellerArr as $user) {				 
			$store_info = dokan_get_store_info($user);
			$seller_enable = dokan_is_seller_enabled($user);
			$isSellerFeatured = get_user_meta($user, 'dokan_feature_seller', true);
			
			if ($seller_enable) {				
				if (strpos($store_info['address'],$searchedLoc)!== false)
				{
						$sellerByItemLoc[]= $user; 
				} 
			}				
		}	
		//print_r($sellerByItemLoc);
		return $sellerByItemLoc;
}
/**************************************************************************************************************************/
add_filter('dokan_woo_breadcrumb','custom_dokan_woo_breadcrumb');
function custom_dokan_woo_breadcrumb( $args ) {
		exit;
}
/************************************************************************************************************************************/
//add_action( 'woocommerce_checkout_after_customer_details', 'wc_check_if_shipping_possible');
add_action( 'woocommerce_after_checkout_validation', 'wc_check_if_shipping_possible');
function wc_check_if_shipping_possible() {
	global $woocommerce;
	
	$packages = WC()->shipping->get_packages();
    $packages = reset( $packages );
	

	if ( !isset( $packages['contents'] ) ) {
            return;
     }
	
	$products = $packages['contents'];
	$destination_country = isset( $packages['destination']['country'] ) ? $packages['destination']['country'] : '';
	$destination_state = isset( $packages['destination']['state'] ) ? $packages['destination']['state'] : '';
	
	 	$errors = array();
        foreach ( $products as $key => $product) 
		{	
            $seller_id = get_post_field( 'post_author', $product['product_id'] );

            $dps_country_rates = get_user_meta( $seller_id, '_dps_country_rates', true );
            $dps_state_rates   = get_user_meta( $seller_id, '_dps_state_rates', true ); 
            
            $has_found = false;
            $dps_country = ( isset( $dps_country_rates ) ) ? $dps_country_rates : array();
            $dps_state = ( isset( $dps_state_rates[$destination_country] ) ) ? $dps_state_rates[$destination_country] : array();
			
			
			
			if(empty($dps_country) || empty($dps_state))
			{
				 $errors[] = sprintf( '<a href="%s">%s (This wine can not be ship to your location)</a>', get_permalink( $product['product_id'] ), get_the_title( $product['product_id'] ));
			}
			else{
				if( array_key_exists( $destination_country, $dps_country ) ) 
				{      
					if( $dps_state ) 
					{
							if( array_key_exists( $destination_state, $dps_state ) ) {
								$has_found = true;
							} elseif ( array_key_exists( 'everywhere', $dps_state ) ) {
								$has_found = true;
							}    
					} 
					else 
					{
						$has_found = true;
					}
				} 
				     if ( ! $has_found ) {
                		$errors[] = sprintf( '<a href="%s">%s</a>', get_permalink( $product['product_id'] ), get_the_title( $product['product_id'] ) );
            		}
			}                   
        }
	if (!empty($errors))
	{
		wc_add_notice( sprintf( __( '%s can not be shipped to your shipping location, Please remove it from the cart. <a href="'.site_url().'/cart/">View Cart</a>', 'woocommerce' ), 'The Following Product'), 'error' );

			foreach ( $errors as $errcnt) 
			{
					wc_add_notice( sprintf( __( '&bull; %s', 'woocommerce' ), $errcnt), 'error' );
			}
	}
}
/**********************************************************************************************************************/
/*add_action( 'woocommerce_after_shop_loop_item_title', 'wc_product_sold_count', 11 );
function wc_product_sold_count() {
global $product;
$units_sold = get_post_meta( $product->id, 'total_sales', true );
echo '' . sprintf( __( 'Units Sold: %s', 'woocommerce' ), $units_sold ) . '';
}*/
/********************************** Adding Date Picker for the advanced search field **********************************/
/*add_action('wp_enqueue_scripts', 'add_e2_date_picker'); 
function add_e2_date_picker(){
//jQuery UI date picker file
//wp_enqueue_script('jquery-ui-datepicker');
//jQuery UI theme css file
//wp_enqueue_style('e2b-admin-ui-css','http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css',false,"1.9.0",false);
}*/
?>