jQuery(window).load(function(e) {
   	jQuery('#acf-wine_type input').each(function(index, element) {
        if(jQuery(this).attr('checked')=='"checked"');
		{
			var selected= jQuery(this).val();
			alert('Hii'+selected);
		}
    });
});
