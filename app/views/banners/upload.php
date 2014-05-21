<script type="text/javascript">
	$(document).ready(function(){
		$('#upload_form').submit(function(){

			if ($.trim($("#file_src").val()) == "")
			{
				$('#error_message').css('display', 'block');
				return false; 
			}


		});
		
		$('.fake_section').click(function(e){
			e.preventDefault();

			$('#file_src').trigger('click');    
		});

		$('#file_src').change(function(e){
			var filename = $(this).val();
			var ext = filename.split('.').pop().toLowerCase();

			if( $.inArray( ext, ['gif','jpg','jpeg','png'] ) == -1 ){
				alert('not valid!');
			}
			else{
				$('input[name="fake_section"]').val(filename);
			}
		});
	});
</script>
<form class="mws-form" accept="image/*" enctype="multipart/form-data" method="post" action="<?php echo URL::to("banners/iframe"); ?>" id="upload_form" name="upload_form">
	<input type="text" name="fake_section" class="small" style="height: 24px; min-height:0; width:270px" readonly="readonly" placeholder="No file selected...">
    <input type="button" class="fake_section" value="Browse"/>	
	<input class="small" style="display:none;" type="file" name="file_src" size="13" id="file_src" />	
	<input id="upload_iframe" class="small" type="submit" name="btn_add" value="Upload" />
	<span style="color:#FF0000; display:none;" id="error_message">Please select image for upload</span>
</form>