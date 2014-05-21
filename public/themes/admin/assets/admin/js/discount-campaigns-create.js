$(function() {
    
    $('#started_at').datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat: "yy-mm-dd",
        onClose: function( selectedDate ) {
            $( "#ended_at" ).datepicker( "option", "minDate", selectedDate );
        }
    });
    
    $('#ended_at').datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat: "yy-mm-dd",
        onClose: function( selectedDate ) {
            $( "#started_at" ).datepicker( "option", "maxDate", selectedDate );
        }
    });
    
    $('.discount_type').on('change', function() {
        
        var percent = 'ลด';
        var price = 'ลดเหลือ';
        
        var val = $(this).val();
        
        if (val == 'percent')
        {
            $(this).siblings('.pre_discount').text(percent);
        }
        else
        {
            $(this).siblings('.pre_discount').text(price);
        }
        
    });
    $('.discount_type').change();
    
    $('form').on('submit', function() {
        
        var result = true;
        
        if ($('#started_at').val() == '' || $('#ended_at').val() == '')
        {
            alert('กรุณาใส่ระยะเวลาของ Campaign');
            return false;
        }
        
        $('.discount').each(function() {
            
            var val = parseInt($(this).val());
            var type = $(this).siblings('.discount_type').val();
            
            if (type == 'percent')
            {   
                if (val >= 100)
                {
                    alert('ไม่สามารถลดราคามากกว่าหรือเท่ากับ 100% ได้');
                    result = false;
                }
                else if (val <= 0)
                {
                    alert('ไม่สามารถลดราคาให้น้อยกว่าหรือเท่ากับ 0% ได้');
                    result = false;
                }
            }
            else
            {
//                if (val >= parseInt($(this).data('net_price')))
//                {
//                    alert('ไม่สามารถลดราคาให้มากกว่าหรือเท่ากับราคาตั้งต้นได้');
//                    result = false;
//                }
//                else
                if (val <= 0)
                {
                    alert('ไม่สามารถลดราคาให้เหลือน้อยกว่า 0 บาทได้');
                    result = false;
                }
            }
            
        });
        
        return result;
        
    });
    
});