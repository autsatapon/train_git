$(function(){
    // Delete categoies
    $('a.delete-category').click(function(e){
        e.preventDefault();
        if ( confirm('คุณต้องการลบ หมวดหมู่เนื้อหานี้จริงๆ หรือ ?') ) {
            var categoryId = $(this).attr('categoryid');
            var targetUrl = $(this).attr('href');
            var parentRow = $(this).parents('tr');

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: targetUrl,
                data: {'category_id':categoryId},
                success: function(response) {
                    if (response.hasOwnProperty('status') && response.status == 'success') {
                        // Remove Row
                        parentRow.remove();
                    }
                }
            });
        }
    });
});