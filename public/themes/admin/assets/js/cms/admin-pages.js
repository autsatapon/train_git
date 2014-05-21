$(function(){
    // Delete page
    $('a.delete-page').click(function(e){
        e.preventDefault();
        if ( confirm('คุณต้องการลบเนื้อหาทั้งหมดในหน้านี้จริงๆ หรือ ?') ) {
            var pageId = $(this).attr('pageid');
            var targetUrl = $(this).attr('href');
            var parentRow = $(this).parents('tr');

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: targetUrl,
                data: {'page_id':pageId},
                success: function(response) {
                    if (response.hasOwnProperty('status') && response.status == 'success') {
                        // Remove Row
                        parentRow.remove();
                    }
                }
            });
        }
    });

    // DataTable
    $(".mws-datatable-fn").dataTable({
        bSort: false,
        sPaginationType: "full_numbers",
         "aoColumns": [
            { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" },
            { "bVisible": true, "bSearchable": true, "bSortable": false , sClass: "" },
            { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" },
            { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" },
            { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" }
        ]
    });
    /*
    $(".mws-datatable-fn").dataTable({
        sPaginationType: "full_numbers",
         "aoColumns": [
            { "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "alignCenter" },
            { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" },
            { "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "" },
            { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" },
            { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" }
        ]
    });
    */
});