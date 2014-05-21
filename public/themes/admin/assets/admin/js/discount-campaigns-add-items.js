$(function() {
    
    $('.all-products-checkbox').on('change', function()
    {
        var checked = $(this).is(':checked');
        
        $('.all-products-checkbox').prop('checked', checked);
        $('.products-checkbox').prop('checked', checked);
    });
    
});