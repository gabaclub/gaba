<?php
	 $data['min_price'] = (isset($_REQUEST['min_price']) && $_REQUEST['min_price']!='')?$_REQUEST['min_price']:0;
	 $data['max_price'] = (isset($_REQUEST['max_price']) && $_REQUEST['max_price']!='')?$_REQUEST['max_price']:10000;
	 $data['cat'] = (isset($_REQUEST['product_cat']) && $_REQUEST['product_cat']!='')?$_REQUEST['product_cat']:'';
	 $search_term = (isset($_REQUEST['s']) && $_REQUEST['s']!='')?$_REQUEST['s']:'';
	 $zipcode = (isset($_REQUEST['zipcode']) && $_REQUEST['zipcode']!='')?$_REQUEST['zipcode']:'';
	 $radius = (isset($_REQUEST['radius']) && $_REQUEST['radius']!='')?$_REQUEST['radius']:'';
	 $discount = (isset($_REQUEST['discount']) && $_REQUEST['discount']!='')?$_REQUEST['discount']:'';
?>
<form role="search" method="get" id="quick_search" class="woocommerce-product-search" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
  <div class="product">
  <?php if(is_front_page())
  { ?>
    <label class="screen-reader-text" for="s">Search for:</label>
  <?php } ?>
    <input type="search" class="search-field" placeholder="Enter Product Keyword" value="<?php echo $search_term; ?>" name="s" title="Search for:">
  </div>
  <div class="product">
    <label class="screen-reader-text" for="s">Alcohol Type</label>
    <select class="radius" name="product_cat">
      <option value="">Any Category</option>
      <option value="anise" <?php if(isset($data['cat']) && $data['cat']=='anise') echo 'selected="selected"'; ?>>Anise</option>
      <option value="beer" <?php if(isset($data['cat']) && $data['cat']=='beer') echo 'selected="selected"'; ?>>Beer</option>
      <option value="brandy" <?php if(isset($data['cat']) && $data['cat']=='brandy') echo 'selected="selected"'; ?>>Brandy</option>
      <option class="champagne" <?php if(isset($data['cat']) && $data['cat']=='champagne') echo 'selected="selected"'; ?>>Champagne</option>
      <option value="gin" <?php if(isset($data['cat']) && $data['cat']=='gin') echo 'selected="selected"'; ?>>Gin</option>
      <option value="liquer" <?php if(isset($data['cat']) && $data['cat']=='liquer') echo 'selected="selected"'; ?>>Liquer</option>
      <option value="sake" <?php if(isset($data['cat']) && $data['cat']=='sake') echo 'selected="selected"'; ?>>Sake</option>
      <option value="soju" <?php if(isset($data['cat']) && $data['cat']=='soju') echo 'selected="selected"'; ?>>Soju/Sochu</option>
      <option value="spirit" <?php if(isset($data['cat']) && $data['cat']=='spirit') echo 'selected="selected"'; ?>>Sugar cane spirit</option>
      <option value="tequila" <?php if(isset($data['cat']) && $data['cat']=='tequila') echo 'selected="selected"'; ?>>Tequila</option>
      <option value="vodka" <?php if(isset($data['cat']) && $data['cat']=='vodka') echo 'selected="selected"'; ?>>Vodka</option>
      <option value="whisky" <?php if(isset($data['cat']) && $data['cat']=='whisky') echo 'selected="selected"'; ?>>Whisky</option>
      <option value="wine" <?php if(isset($data['cat']) && $data['cat']=='wine') echo 'selected="selected"'; ?>>Wine</option>
    </select>
  </div>
  <div class="product">
  	<?php // echo do_shortcode('[SLPLUS]'); ?>
    <label class="screen-reader-text" for="s">Your Location </label>
    <input type="search" class="search-field" placeholder="Zip…/ Address" value="<?php echo $zipcode; ?>" id="zipcode" name="zipcode">
  </div>
  <div class="product">
  	<label class="screen-reader-text" for="s">Nearest by</label>
    <select class="radius" name="radius" id="radius">
        <option value="">Any distance</option>
        <option value="0.5" <?php if(isset($radius) && $radius==0.5) echo 'selected="selected"'; ?>>< 0.5 miles</option>
        <option value="1" 	<?php if(isset($radius) && $radius==1) echo 'selected="selected"'; ?>>< 1 miles</option>
        <option value="2" 	<?php if(isset($radius) && $radius==2) echo 'selected="selected"'; ?>>< 2 miles</option>
        <option value="5" 	<?php if(isset($radius) && $radius==5) echo 'selected="selected"'; ?>>< 5 miles</option>
        <option value="10" 	<?php if(isset($radius) && $radius==10) echo 'selected="selected"'; ?>>< 10 miles</option>
        <option value="20" 	<?php if(isset($radius) && $radius==20) echo 'selected="selected"'; ?>>< 20 miles</option>
        <option value="50" 	<?php if(isset($radius) && $radius==50) echo 'selected="selected"'; ?>>< 50 miles</option>
        <option value="100" <?php if(isset($radius) && $radius==100) echo 'selected="selected"'; ?>>< 100 miles</option>
        <option value="500" <?php if(isset($radius) && $radius==500) echo 'selected="selected"'; ?>>< 500 miles</option>
    </select>
  </div>
  <div class="product priceBox">
    <p>
      <label for="amount">Price range: (US $)</label>
      <?php /*?><span id="upPriceLabel" style="color:#f6931f; font-weight:bold; float:right;"></span> <?php */?>
      </p>
      	<input type="radio" value="" id="disNull" name="discount" <?php if(!isset($discount) || $discount=='') echo 'checked'; ?>>
            <div class="inputPriceWrapper">
              <input type="text" id="min_price"  name="min_price" placeholder="min price" onKeyUp="validateMinNumeric()">
              <input type="text" id="max_price" name="max_price" placeholder="max price" onKeyUp="validateNumeric()">
            </div>
      <div class="sliderCover">
        <div id="slider-range"></div>
       </div>
       <div class="rdBtn_control">
        <input type="radio" value="true" id="disTrue" name="discount" <?php if(isset($discount) && $discount==true) echo 'checked'; ?>>
        <label class="screen-reader-text" for="s">Discounted Price</label>
      </div>
  </div>
      <div class="product bothbutton">
        <input type="submit" id="submit_quick_search" value="Search">
        <input type="hidden" name="post_type" value="product">
        <input type="hidden" id="FLAG" name="FLAG" value="QUICK_SEARCH">
 <a href="<?php echo site_url(); ?>/advanced-search/" class="advanced">Advanced Search</a>

      </div>
      
</form>
	<!--<div id="user-location-current" style="height:247px;"></div>-->
<style>
#user-location-current, #user-location-current img {
     height: auto; 
    max-width: none;
}
</style> 
<?php include_once("googleMaps.php"); ?>
<script>
	<?php 
		$prRange= wc_product_price_range();
	?>
	var map2;
	var m1;
	var radVal;
	$= jQuery.noConflict();
	$(function() {
		$( "#slider-range" ).slider({
			range: true,
			min: <?php echo (isset($prRange['min_price']) && $prRange['min_price']!='')?$prRange['min_price']:0; ?>,
			max: <?php echo (isset($prRange['max_price']) && $prRange['max_price']!='')?$prRange['max_price']:10000; ?>,
			values: [ <?php echo (isset($data['min_price']) && $data['min_price']!='')?$data['min_price']:0; ?>, <?php echo (isset($data['max_price']) && $data['max_price']!='')?$data['max_price']:$prRange['max_price']; ?> ],
			slide: function( event, ui ) {
					$( "#upPriceLabel" ).html( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
					$( "#min_price" ).val(ui.values[ 0 ] );
					$( "#max_price" ).val(ui.values[ 1 ] );	
			}
		});
		$( "#min_price" ).val($( "#slider-range" ).slider( "values", 0 ));
			$( "#max_price" ).val($( "#slider-range" ).slider( "values", 1 ) );
		$( "#upPriceLabel" ).html( "$" + $( "#slider-range" ).slider( "values", 0 ) + " - $" + $( "#slider-range" ).slider( "values", 1 ));
	});
	$('#submit_quick_search').click(function(e) {
		event.preventDefault();
		if($('#zipcode').val()=='' && $('#radius').val()!='')
		{
			alert("Please Enter Zipcode to Search Store within Radius \n otherwsie Do Not Select the Radius");
			return false
		}
		$('#quick_search').submit();
    });
	$(':radio[name="discount"]').change(function() {
		 radVal = $(':radio[name="discount"]').filter(':checked').val();
				 if(radVal)
				 {
					$('.ui-slider').slider('disable');
					$('#min_price').attr('disabled','true');
					$('#max_price').attr('disabled','true');
				 }else{
					 $('.ui-slider').slider('enable');
					 $('#min_price').removeAttr('disabled');
					 $('#max_price').removeAttr('disabled');
				 }
	});
	function findCurLoc()
	{
		navigator.geolocation.getCurrentPosition(successFunction, errorFunction);
	}
	function successFunction(position) { 
		var lat = position.coords.latitude;
		var lng = position.coords.longitude;
		codeLatLng(lat, lng, function(cAdrs){
			//alert(cAdrs);
			if(cAdrs!='') jQuery('#zipcode').val(cAdrs);
	  });
	   jQuery('#genMap #mapBox').empty();
	   //jQuery('#singlebannerwidget-2 h3').empty().html('Your Location');
	   jQuery('#genMap #mapBox').append('<div id="user-location-current" style="height:195px;"></div>');
	   genCurrentLocMap(lat, lng);
	}
	
	function errorFunction(){
		alert("Geocoder failed");
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
			callback('No Result Found');
		}
	});
}

function genCurrentLocMap(lat, lng)
{
	var myCenter=new google.maps.LatLng(lat,lng);
	console.log(mapOptions);
	console.log(lat);
	$map_area = jQuery('#user-location-current');
	map2 = new google.maps.Map( $map_area[0], mapOptions);
	map2.setCenter(myCenter);
	addShortMarker(myCenter, lat, lng);
	map2.setZoom(16);
}
function addShortMarker(curpoint, lat, lng) 
	{
		custIcon= '<?php echo get_stylesheet_directory_uri(); ?>/assets/images/current-loc.png';
		m1 = new google.maps.Marker({
			position: curpoint,
			map: map2,
			icon:custIcon,
			draggable: false,
			animation: google.maps.Animation.DROP,
		});
		addInfoWindow(m1, '<div>Latitude:'+lat+'</div><div>Longitude:'+lng+'</div>');
		markers.push(m1);
	}

function validateNumeric() {
	var maxVal= <?php echo (isset($prRange['max_price']) && $prRange['max_price']!='')?$prRange['max_price']:3000; ?>;
	//console.log(maxVal);
	var val = document.getElementById("max_price").value;
	if (!Number(val)) {
		document.getElementById("max_price").value = '';
	} else if (val > Math.round(maxVal)) {
		document.getElementById("max_price").value = '';
		alert('Invalid value, Please enter max price value from 0 - '+Math.round(maxVal));
	}
}		
function validateMinNumeric() {
	var maxVal= <?php echo (isset($prRange['max_price']) && $prRange['max_price']!='')?$prRange['max_price']:3000; ?>;
	//console.log(maxVal);
	var val = document.getElementById("min_price").value;
   
	if (!Number(val) && val!=0) {
		document.getElementById("min_price").value = '';
	} else if ((val < 0 || val > maxVal)) {
		document.getElementById("min_price").value = '';
		alert('Invalid value, Please enter min price value less than '+Math.round(maxVal));
	}
}		
</script>