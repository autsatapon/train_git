$(function(){
	var notNumeric = /[^\d\.\-]/;

	$(document).on('keyup','.pcms-numeric',function(e){
		var o = $(this);
    	o.val( o.val().replace(notNumeric, '') );
	});
})