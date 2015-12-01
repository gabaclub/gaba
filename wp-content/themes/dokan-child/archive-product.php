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
get_header();
get_sidebar( 'shop' );
global $sellerArr;
	//echo '<pre>'.$GLOBALS['wp_query']->request.'</pre>';
?>
   <div id="primary" class="content-area col-md-9 mainPrimary">
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
        
        
       <?php if (have_posts()) :   ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php woocommerce_product_subcategories(); ?>

				<?php // while ( have_posts() ) : the_post(); ?>
                 <?php while(have_posts()): the_post(); ?>
                 
                	 <?php 
					 		$idSellers[]= get_the_author_ID();  
					 		//array_push($authProd, get_the_author_ID());
							//array_push($Prod, get_the_ID());
					 ?>

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
	<?php	/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action('woocommerce_after_main_content');
	?>
		<div class="store_locator">
			<h1 class="page-title">Store Locations:</h1>
            <div id="warnings_panel" class="warnings_panel"></div>
            <div id="dokan-store-location-searched" style="height:400px;"></div>
          	<div id="directions-panel" style="display:none;" ></div>
         </div>
	</div><!-- #content .site-content -->
</div>
<style>
.woocommerce .store_locator img, .woocommerce-page .store_locator img {
     height: auto; 
    max-width: none;
}
</style>    
<?php include_once("googleMaps.php"); ?>
<script type="text/javascript">
jQuery(document).ready(function(e) {
		var radius=  '<?php  if(isset($_REQUEST['radius']) && $_REQUEST['radius']!=''){ echo  $_REQUEST['radius']; } else echo 10000;  ?>';
		var addr=  '<?php  if(isset($_REQUEST['zipcode']) && $_REQUEST['zipcode']!=''){ echo $_REQUEST['zipcode']; }  ?>';
		
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
<script type="text/javascript">
<?php if(isset($_REQUEST['zipcode']) && $_REQUEST['zipcode']!='') $prZip= $_REQUEST['zipcode']; else echo $prZip=''; ?>
		var radius=  '<?php  if(isset($_REQUEST['radius']) && $_REQUEST['radius']!=''){ echo  $_REQUEST['radius']; } else echo '';  ?>';
		var addr=  '<?php  if(isset($_REQUEST['zipcode']) && $_REQUEST['zipcode']!=''){ echo $_REQUEST['zipcode']; }  ?>';
		<?php 
					if(isset($prZip) && $prZip!='')
					{
						$pLatLong= getLnt($prZip);  
					}
					else{
						$pLatLong['lat']=-73.9661407;  
						$pLatLong['lng']=40.7841484;
					}
		?>
	var map;
	var directionsDisplay;
	var stepDisplay;
	var nearerDist;
	var distance=[];
	var radiusSeller=[];
	
	var markers = new Array();
	var sellers = [];
	var locationList = [];
	var gmap;
	var directionsService = new google.maps.DirectionsService;
	var startAdrs;
	var geocoder;
	var end='United States';
	var distanceInstant;
	var m1;
	var k;
	
	initialize();
	
	sellers = <?php echo json_encode(getSellerInfo(array_unique($idSellers))); ?>;
	console.log(sellers);
	$= jQuery.noConflict();
	$map_area = jQuery('#dokan-store-location-searched');
	
	//alert(<?php // echo getLnt($_REQUEST['zipcode']); ?>+'Hii');
	var manhattan = new google.maps.LatLng(<?php echo $pLatLong['lat']; ?>, <?php echo $pLatLong['lng']; ?>);
	
	gmap = new google.maps.Map( $map_area[0], mapOptions);
	
	stepDisplay = new google.maps.InfoWindow();
	
	for(i=0;i<sellers.length;i++)
	{
		var tempSeller=sellers[i];
		console.log(tempSeller.long);
		distance[i] = hesapla(<?php echo $pLatLong['lng']; ?>, <?php echo $pLatLong['lat']; ?>, tempSeller.long, tempSeller.lat);
	}
	
	console.log(distance);
	distance.sort(function(a, b) { return a - b });
	
	k=1; // just to set array key for radLoc
	for(i=0;i<sellers.length;i++)
	{
		var seller=sellers[i];
		var curpoint = new google.maps.LatLng(seller.lat, seller.long);
		console.log(seller.lat);
		console.log(seller.long);
		distanceInstant = hesapla(<?php echo $pLatLong['lng']; ?>, <?php echo $pLatLong['lat']; ?>, seller.long, seller.lat);
		console.log(distance[i]);
		<?php 
		if(isset($_REQUEST['zipcode']) && $_REQUEST['zipcode']!='')
		{ ?>
					if(distance[i]>=0 && radius>=0)
					{
						end= curpoint;
						//console.log('radius searched: '+radius+' Seller Id '+seller.id+' Distance '+distanceInstant+' Store '+seller.store_name);
						addMarker(curpoint,seller.store_url,seller.store_name, seller.address, seller.featured);
					}
					else if(radius==''|| radius==0)
					{
						console.log('radius null found');
						end= curpoint;
						addMarker(curpoint,seller.store_url,seller.store_name, seller.address, seller.featured);
					}
					else
					{
						if(distance[0]>radius)
						{
							jQuery('#warnings_panel').html('<b>We were unable to locate any stores as per your search parameters.\n Please retry the search or you can browse our other stores as indicated below.</b>');
							jQuery('#warnings_panel').css('display','block');
						}
					}
		<?php }else{ ?>
					console.log(curpoint+'**##');
				 	addMarker(curpoint,seller.store_url,seller.store_name, seller.address, seller.featured);
		<?php } ?>
	}

	<?php
	if(isset($_REQUEST['zipcode']) && $_REQUEST['zipcode']!='')
	{ ?>
			locMarker = new google.maps.LatLng(<?php echo $pLatLong['lat']; ?>, <?php echo $pLatLong['lng']; ?>);
			addLocatorMarker(locMarker);
			gmap.setCenter(locMarker);
			gmap.setZoom(10);
	<?php }else{ ?>
			gmap.setCenter(m1.position);
			gmap.setZoom(16);
	<?php } ?>
	<?php /*?><?php 
		if(isset($_REQUEST['zipcode']) && $_REQUEST['zipcode']!='')
		{ ?>
				if(distance[0]<=radius) calcRoute(); 
	<?php } ?><?php */?>
function addLocatorMarker(location) {
	var custIcon= '<?php echo get_stylesheet_directory_uri(); ?>/assets/images/current-loc.png';
	marker = new google.maps.Marker({
		position: location,
		icon:custIcon,
		map: gmap
	});
	
	addInfoWindow(marker, '<div>Your Location:<div>Latitude:'+<?php echo $pLatLong['lat']; ?>+'</div><div>Longitude:'+<?php echo $pLatLong['lng']; ?>+'</div></div>');
}
function calcRoute() {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(null);
  }

  // Now, clear the array itself.
  markers = [];

	if(startAdrs){
		  start= startAdrs;
		  //alert(start);
	}else{
		  var start = '<?php echo $prZip; ?>';
	}
	
  var request = {
      origin: start,
      destination: end,
      travelMode: google.maps.TravelMode.DRIVING
  };
  directionsService.route(request, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
      var warnings = document.getElementById('warnings_panel');
      warnings.innerHTML = '<b>' + response.routes[0].warnings + '</b>';
	  //console.log(response);
      directionsDisplay.setDirections(response);
      //showSteps(response);
	  //$("#button-show").css('display', 'block');
    }
	
  });
}
	jQuery(function($) 
	{
		//console.log(markers);	 
		var ind=0;
		for (var j = 0; j < markers.length; j++) 
		{
			setTimeout(function(){
				markers[ind++].setMap(gmap);
			},j* 80);
		}
	})
	
	function findCurLoc()
	{
		navigator.geolocation.getCurrentPosition(successFunction, errorFunction);
	}
	
	function addMarker(loc, url, tit, addr, featured) 
	{
		if(featured)
		{
			custIcon= '<?php echo get_stylesheet_directory_uri(); ?>/assets/images/bulb_black.png';
		}
		else{
			custIcon= '<?php  echo get_stylesheet_directory_uri(); ?>/assets/images/liquor.png';
		}
		m1 = new google.maps.Marker({
			position: loc,
			map: gmap,
			icon:custIcon,
			draggable: false,
			animation: google.maps.Animation.DROP,
			title:tit
		});
		<?php if(isset($_REQUEST['zipcode']) && $_REQUEST['zipcode']!='')
			  { ?>
			  var startPoint = '<?php echo $_REQUEST['zipcode']; ?>';
				addInfoWindow(m1, '<div>Store Name:'+tit+'</div><div>Address:'+addr+'</div><div><a href="'+url+'" target="_blank">Visit Store</a></div><div><a href="https://maps.google.com?saddr='+startPoint+'&daddr='+end+'" target="_blank">Show Route</a></div>');
		<?php 
			  }else{
		      ?>
				addInfoWindow(m1, '<div>Store Name:'+tit+'</div><div>Address:'+addr+'</div><div><a href="'+url+'">Visit Store</a></div>');
		<?php } ?>
		markers.push(m1);
	}
	
	function addInfoWindow(marker, message) {
		var infoWindow = new google.maps.InfoWindow({
			content: message
		});
	
		google.maps.event.addListener(marker, 'click', function () {
			infoWindow.open(gmap, marker);
		});
	}
	
function codeLatLng(lat, lng, callback) {
		var startAdrs;
		var latlng = new google.maps.LatLng(lat, lng);
		geocoder.geocode({'latLng': latlng}, function(results, status) {
	  	if (status == google.maps.GeocoderStatus.OK) {
		  callback(results[0].formatted_address);
		 // console.log(startAdrs+'****');
		}
		else{
			callback('Location detection failed');
		}
	});
}

function hesapla(meineLongitude, meineLatitude, long1, lat1) {
        erdRadius = 6371;
        meineLongitude = meineLongitude * (Math.PI / 180);
        meineLatitude = meineLatitude * (Math.PI / 180);
        long1 = long1 * (Math.PI / 180);
        lat1 = lat1 * (Math.PI / 180);
        x0 = meineLongitude * erdRadius * Math.cos(meineLatitude);
        y0 = meineLatitude * erdRadius;
        x1 = long1 * erdRadius * Math.cos(lat1);
        y1 = lat1 * erdRadius;
        dx = x0 - x1;
        dy = y0 - y1;
        d = Math.sqrt((dx * dx) + (dy * dy));
       /* if (d < 1) {
            return Math.round(d * 1000);
        } else {*/
            //return Math.round(d * 10) / 10;
			return (Math.round(d * 10) / 10)/1.609344;
       /* }*/
}
jQuery(document).ready(function(e) {
	var text = jQuery('nav.breadcrumb li:last-child').html();
    text = text.replace("“”","");
    jQuery('nav.breadcrumb li:last-child').html(text);
});
</script>
<?php  get_footer(); ?>