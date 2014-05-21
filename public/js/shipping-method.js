$(function(){
	$(document).on('change', '.shipping-method-item', function(e){
		var o = $(this),
			relateShippingMethods = (o.data('alway-with')+"").split(","),
			parent = o.parents('.shipping-method-row');
		
		if (o.is(":checked") == false)
			return;

		for (var i in relateShippingMethods)
			if (relateShippingMethods[i])
				parent.find('.shipping-method-item[value='+relateShippingMethods[i]+']').prop('checked', true).change();
	});

	$('.shipping-method-item').each(function(i,item){
		var item = $(item),
		relateShippingMethods = (item.data('alway-with')+"").split(","),
		parent = item.parents('.shipping-method-row');

		for (var i in relateShippingMethods)
			if (relateShippingMethods[i])
				parent.find('.shipping-method-item[value='+relateShippingMethods[i]+']').on('change', function(e){
					var o = $(this);
					if (!o.is(":checked"))
						item.prop('checked', false).change();
				})
	})
});