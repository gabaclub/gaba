jQuery(document).ready(function(e) {
	var selectedVal='';
		selectedVal = jQuery("#product_cat").val();
	var postID = jQuery('[name="dokan_product_id"]').val();
	var oldSpecHtml= jQuery('#product-specifications').html();
	
	//console.log(oldSpecHtml);
	
	jQuery('#product_cat').change(function(e) {
     	var selCat= jQuery(this).val();
		//alert(selCat+'****'+postID+'****'+selectedVal);
		if(confirm('Change in wine type will clear the value selected in Specifications tab, Are you sure ? you want to change the wine type'))
			{
					var selCat= jQuery(this).val();
					jQuery.post(
						ajaxurl, 
						{
							'action': 'dokan_custom_specification',
							'data':   selCat+'|'+selectedVal+'|'+postID,
						}, 
						function(response)
						{
								//alert(response);
								if(selectedVal==selCat)
								{
									jQuery('#product-specifications').empty();
									jQuery('#product-specifications').html(oldSpecHtml);
								}else{
									jQuery('#product-specifications').empty();
									jQuery('#product-specifications').html(response);
								}
						}
					);
			}
			else{
				e.preventDefault();
			}
	});
});
/*************************************************************************************************/