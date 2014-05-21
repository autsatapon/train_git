var PCMS = PCMS || {};

PCMS.Holiday = PCMS.Holiday || {};

PCMS.Holiday.Create = (function() {

    var self = self || {};

    self.init = function()
    {
        $('#started_at').datepicker({
            dateFormat: 'yy-mm-dd',
            onClose: function(selectedDate)
            {
                $("#ended_at").datepicker('option', 'minDate', selectedDate);
            }
        });
        
        $('#ended_at').datepicker({
            dateFormat: 'yy-mm-dd',
            onClose: function(selectedDate)
            {
                $("#started_at").datepicker('option', 'maxDate', selectedDate);
            }
        });
    };

    return self;

})($);

$(function() {

    PCMS.Holiday.Create.init();

});