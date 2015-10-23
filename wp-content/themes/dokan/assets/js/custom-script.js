jQuery(document).ready(function(e) {
   	jQuery('#acf-wine_type input').each(function(index, element) {
        var selected= jQuery(this).val();
		alert('Hii'+selected);
    });
});
