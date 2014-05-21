$(document).ready(function(){
	var banner_type = $('.banner_type:checked').val();
	showFields(banner_type);
	//alert("clicked1");
	setTimeout(function () {
		//alert('test');
		gui_loadImage(window.frames['uploader'].document.getElementById('src').getAttribute('rel'));
		$('#hid_img_path').val(window.frames['uploader'].document.getElementById('src').getAttribute('data-path'));
		
		gui_htmlFocus();
		
 		var text = $('#mapareacode').text();
		$("#html_container").val(text);
		gui_htmlBlur();
		//gui_addArea(3);
	}, 3000);
	
	$('.banner_type').click(function(event){
		var banner_type = $(this).val();
		showFields(banner_type);
	});
	
	var period_time = $('#period_time').attr('checked');
	showPeriod(period_time);
	
	$('#period_time').click(function(event){
		var period_time = $(this).attr('checked');
		showPeriod(period_time);
	});
	
	$('#banner_create').submit(function( event ){
		var name = $('#name').val();
		var banner_type = $('.banner_type:checked').val();
		var banner_image = $('#banner_image').val();
		var link = $('#link').val();
		var youtube_embed = $('#youtube_embed').val();
		
		var errors = 0;
		
		var valid_by_type = false;
		var error_type = 0;
		
		/* validate banner type */
		if(banner_type == 1)
		{
			if(empty(banner_image))
			{
				error_type = 1;
			}
			else if(empty(link))
			{
				error_type = 2;
			}
		}
		else if(banner_type == 3)
		{
			if(empty(youtube_embed))
			{
				error_type = 3;
			}
		}
		
		if(empty(error_type))
		{
			valid_by_type = true;
		}
		
		
		var valid_date = false;
		var period_time = $('#period_time').attr('checked');
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		
		if(!empty(period_time))
		{
			if(!empty(start_date) && !empty(end_date))
			{
				if(start_date <= end_date)
				{
					valid_date = true;
				}
			}
		}
		else
		{
			valid_date = true;
		}
		 
		if(empty(name))
		{
			alert('กรุณากรอกชื่อ');
			errors++;
		}
		else if(empty(valid_by_type))
		{
			if(error_type == 1)
			{
				alert('กรุณาอัพโหลดภาพ');
			}
			else if(error_type == 2)
			{
				alert('กรุณากรอก link');
			}
			else if(error_type == 3)
			{
				alert('กรุณากรอก Youtube embed');
			}
			errors++;
		}
		else if(empty(valid_date))
		{
			alert('วันที่สิ้นสุดต้องมากกว่าวันเริ่มต้น');
			errors++;
		}
		
		if(errors > 0)
		{
			event.preventDefault();
		}
		
	});
});

function showFields(banner_type)
{
	if(banner_type == 1)
	{
		$('#row_banner_image').show();
		$('#row_show_image').show();
		$('#row_banner_link').show();
		$('#row_map').hide();
		$('#row_youtube_embed').hide();
	}
	else if(banner_type == 2)
	{
		$('#row_banner_image').hide();
		$('#row_show_image').hide();
		$('#row_banner_link').hide();
		$('#row_map').show();
		$('#row_youtube_embed').hide();
	}
	else
	{
		$('#row_banner_image').hide();
		$('#row_show_image').hide();
		$('#row_banner_link').hide();
		$('#row_map').hide();
		$('#row_youtube_embed').show();
	}
}

function showPeriod(period_time)
{
	if(period_time == 'checked')
	{
		$('#row_period_time').show();
	}
	else
	{
		$('#row_period_time').hide();
	}
}

function empty (v) {
	var key;
	if (v === "" || v === 0 || v === "0" || v === null || v === false || typeof v === 'undefined') {
		return true;
	}
	if (v.length === 0) {
		return true;
	}
	if (typeof v === 'object') {
		for (key in v) {
			return false;
		}
		return true;
	}
	return false;
}
	
function showCoords(c)
{
	$('#x').val(c.x);
	$('#y').val(c.y);
	$('#x2').val(c.x2);
	$('#y2').val(c.y2);
	$('#w').val(c.w);
	$('#h').val(c.h);
};