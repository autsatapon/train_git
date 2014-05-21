$(function() {

    $('.discount_type').on('change', function() {

        var percent = 'ลด';
        var price = 'ลด';

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

    $('.remove').on('click', function(e) {

        var id = $(this).data('new-added-product-id');

        $(this).parents('tr:first').remove();

        if ($('.new-added-variant-product-id-'+id).length == 0)
        {
            $('.new-added-product-id-'+id).remove();
        }

        e.stopPropagation();

    });

    $('.started_at').datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat: "yy-mm-dd"
    });

    $('.ended_at').datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 1,
        dateFormat: "yy-mm-dd"
    });

    $('input.discount').on('keyup', function(){

        var $this = $(this),
            $discounted = $this.parent('td').next('td'),
            netprice = $this.data('net_price'),
            discount = $this.val(),
            type = $this.siblings('.discount_type').val(),
            discounted_price = 0;

        if (type == 'percent')
        {
            // 100 > discount > 0
            discount = Math.max(Math.min(discount, 100), 0);
            discounted_price = netprice - ((discount / 100) * netprice);
        }
        else
        {
            // net price > discount > 0
            discount = Math.max(Math.min(discount, netprice), 0);
            discounted_price = netprice - discount;
        }

        // net price > discount_price > 0
        discounted_price = Math.max(Math.min(discounted_price, netprice), 0);

        $discounted.text(discounted_price);
    });

    $('form').on('submit', function() {

        var result = true;

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
                if (val >= parseInt($(this).data('net_price')))
                {
                    alert('ไม่สามารถลดราคาให้มากกว่าหรือเท่ากับราคาตั้งต้นได้');
                    result = false;
                }
                else if (val <= 0)
                {
                    alert('ไม่สามารถลดราคาให้เหลือน้อยกว่า 0 บาทได้');
                    result = false;
                }
            }

        });

        return result;

    });

});