(function($) {
	$.fn.emptyTest = function(){
		return this.each(function(){
			this.options.length = 0; 
		}); 
	}
	$.fn.resetSelect = function(text) {
		return this.emptySelect().each(function(){
			if (this.tagName == "SELECT")
			{
				var selectElement = this; 
				var option = new Option(text, ''); 

				if ($.browser.msie)
				{
					selectElement.add(option);
				}
				else 
				{
					selectElement.add(option, null); 
				}
			}
		}); 
	}
	$.fn.showLoading = function(text) {
		return this.emptySelect().each(function(){
			if (this.tagName == "SELECT")
			{
				var selectElement = this; 
				var option = new Option(text, ''); 

				if ($.browser.msie)
				{
					selectElement.add(option);
				}
				else 
				{
					selectElement.add(option, null); 
				}
				
			}
		}); 
	}
	$.fn.disabled = function() {
		this.attr('disabled', true); 
		this.addClass('disabled_elem'); 
	}
	$.fn.enabled = function() {
		this.attr('disabled', false); 
		this.removeClass('disabled_elem'); 
	}

	$.fn.emptySelect = function() {
		return this.each(function(){
			if (this.tagName=='SELECT') this.options.length = 0;
		});
	}
	$.fn.loadSelect = function(optionsDataArray, lblFirst, currentValue) {
		return this.emptySelect().each(function(){
			if (this.tagName=='SELECT') {
				var selectElement = this;
				if (lblFirst != "")
				{
					//selectElement.add(new Option(lblFirst, ""));
					var option = new Option(lblFirst, ""); 
					if ($.browser.msie)
						selectElement.add(option); 
					else 
						selectElement.add(option, null); 
				}		
				if (optionsDataArray != null)
				{
					$.each(optionsDataArray,function(index,optionsData){		
						if (optionsData.opt_value == currentValue)
						{
							var option = new Option(optionsData.opt_text, optionsData.opt_value);
							option.selected = true; 
						}
						else
						{
							var option = new Option(optionsData.opt_text, optionsData.opt_value);
						}
						if ($.browser.msie) {
							selectElement.add(option);
						}
						else {
							selectElement.add(option,null);
						}
					});					
				}			
			}
		});
	}
	
})(jQuery);