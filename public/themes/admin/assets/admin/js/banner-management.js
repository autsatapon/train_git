$(function(){
	$('#banner_position_id').change(function(){
		$.post(
			'/banners/positions/groups',
			{
				isAjax: true,
				banner_position_id: $('#banner_position_id').val()
			},
			function (data){
				if (data != undefined && data != null)
				{
					jsonDecode = $.parseJSON(data);
					$('#banner_group_id').loadSelect(jsonDecode, 'All Groups');
				}
			},
			'html'
		);
	});

	$('#reset-button').click(function(){
		$('#keyword').val('');
	});


	$('#search-form').submit(function(event){
		event.preventDefault();
		var action = $(this).attr('action');
		myAction = action.replace(/\?banner_group_id=(.*)/, '');
		queryString = new Array; 
		if ($('#banner_group_id').val() != "")
		{
			queryString[queryString.length] = 'banner_group_id=' + $('#banner_group_id').val();
		}
		else
		{
			alert("Please select banner group");
			return false;
		}

		if ($.trim($('#keyword').val()) != "")
		{
			queryString[queryString.length] = 'keyword=' + $("#keyword").val();
		}

		if (queryString.length > 0)
		{
			window.location.href = '/banners?' + queryString.join('&');
		}
		else
		{
			window.location.href = '/banners';
		}

	});

	$('.delete-button').click(function(event){
		event.preventDefault();

		var anchor = $(this).attr('href');

		if (confirm("Are you sure to delete this banner ?"))
		{
			window.location.href = anchor;
		}
	});
});