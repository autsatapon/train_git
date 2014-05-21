function StyleOptionController($scope) {

	// $scope.styleOptions = [
	// 	{
	// 		id: 1,
	// 		style_type_id: 1,
	// 		style_option_id: 1,
	// 		text: 'ดำ',
	// 		meta: {
	// 			type: 'color',
	// 			value: '#000'
	// 		}
	// 	},
	// 	{
	// 		id: 2,
	// 		style_type_id: 1,
	// 		style_option_id: 2,
	// 		text: 'แดง',
	// 		meta: {
	// 			type: 'color',
	// 			value: '#f00'
	// 		}
	// 	},
	// 	{
	// 		id: 3,
	// 		style_type_id: 2,
	// 		style_option_id: 3,
	// 		text: 'ใหญ่',
	// 		meta: {
	// 			type: 'text',
	// 			value: 'L'
	// 		}
	// 	},
	// 	{
	// 		id: 4,
	// 		style_type_id: 2,
	// 		style_option_id: 4,
	// 		text: 'เล็ก',
	// 		meta: {
	// 			type: 'text',
	// 			value: 'S'
	// 		}
	// 	}
	// ];

	$scope.selectStyleOption = function(obj) {
		var data = $(obj).children(':selected').data('json'),
			exists = false;
		for(var i in $scope.styleOptions) {
			if($scope.styleOptions[i].id == data.id) {
				exists = true;
				break;
			}
		}
		if(! exists)
			$scope.$apply(function(){
				$scope.styleOptions.push(data);
			})
	}

	$scope.setStyleOptions = function(style_type_id, obj)
	{
		$scope.$apply(function(){
			$scope.styleOptions[style_type_id] = obj;
		})
	}

}

function updateStyleOptionList(obj) {
	var $obj = $(obj),
		style_type_id = $obj.data("style-type")
		obj_json = {};


	$("select.select_style_type_"+style_type_id).each(function(index, select){
		var $select = $(select);
		// if select is disabled.. continue to next loop
		if ($select.prop("disabled") == true)
		{
			return true;
		}

		var json = $select.children(':selected').data('json');
		if (json)
		{
			json['iframe'] = $select.children(':selected').data('iframe');
			obj_json[$select.val()] = json;
		}
	});
	angular.element('[ng-controller=StyleOptionController]').scope().setStyleOptions(style_type_id, obj_json);

	$(".ng-scope").show();
}

