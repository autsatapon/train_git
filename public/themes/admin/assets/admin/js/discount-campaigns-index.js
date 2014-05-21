$(function() {
    
    $('.delete').on('click', function() {
        
        if ( ! confirm('Confirm to delete this?'))
        {
            return false;
        }
        
    });
    
});