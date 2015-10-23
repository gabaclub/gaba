jQuery(document).ready(function(e) {
	var postID = jQuery('#post_ID').val();
	var oldSpecHtml= jQuery('#woocommerce_product_tabs_lite').html();
	var selectedVal = "";
	var selected = jQuery("#product_catchecklist input[type='radio']:checked");
	
	if (selected.length > 0) {
		selectedVal = selected.val();
	}
	
	jQuery('#product_cat-all input').click(function(e) {
			if(confirm('Change in wine type will clear the value selected in Specifications tab, Are you sure ? you want to change the wine type'))
			{
					var selCat= jQuery(this).val();
					jQuery.post(
						ajaxurl, 
						{
							'action': 'add_specification',
							'data':   selCat+'|'+selectedVal+'|'+postID,
						}, 
						function(response)
						{
								if(selectedVal==selCat)
								{
									jQuery('#woocommerce_product_tabs_lite').empty();
									jQuery('#woocommerce_product_tabs_lite').html(oldSpecHtml);
								}else{
									jQuery('#woocommerce_product_tabs_lite').replaceWith(response);
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