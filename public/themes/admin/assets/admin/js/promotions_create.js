function cloneObj(obj)
{
    return JSON.parse(JSON.stringify(obj));
}

var PCMS = PCMS || {};

PCMS.Promotion = PCMS.Promotion || {};

PCMS.Promotion.Create = (function() {

    var self = self || {};

    self.data = {};

    self.init = function()
    {
        self.createCondition();

        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd'
        });

        $('.timepicker').timepicker();


        var datetimepicker_config = {
            timeFormat: 'HH:mm:ss',
            dateFormat: 'yy-mm-dd',
            stepMinute: 10,
            stepSecond: 10
        };

        var init_end_date = function()
        {
            var $end_date = $("input[name='end_date']");

            if (! $end_date || $end_date.prop('readonly') == true)
            {
                return;
            }

            $end_date.datetimepicker("destroy");

            var new_config = cloneObj(datetimepicker_config);
            new_config.minDateTime = new Date($("input[name='start_date']").val());

            $end_date.datetimepicker(new_config);

        };

        var init_start_date = function()
        {
            var $start_date = $("input[name='start_date']");

            if (! $start_date || $start_date.prop('readonly') == true)
            {
                return;
            }

            $start_date.datetimepicker("destroy");

            var new_config = cloneObj(datetimepicker_config);
            new_config.maxDateTime = new Date($("input[name='end_date']").val());

            $start_date.datetimepicker(new_config);

        };

        init_start_date();
        init_end_date();

        $("input[name='start_date']").change(init_end_date);
        $("input[name='end_date']").change(init_start_date);


        $('.condition-promotion-code .single_code-used_times').on('keyup', function() {

            var $this = $(this);
            var $parent = $this.parents(".condition-promotion-code");
            var $single_code = $parent.find('.single_code');
            var $min_length = $parent.find('.multiple_code-min_length');
            var $end_with = $parent.find('.multiple_code-end_with');
            var min_length = 1;

            if ($this.val())
            {
                $single_code.prop("checked", true);
            }
            else
            {
                $single_code.prop("checked", false);
            }

            $min_length.text(min_length);
            $end_with.val(min_length);
        });

        $('.condition-promotion-code .multiple_code-count').on('keyup', function() {

            var $this = $(this);
            var $parent = $this.parents(".condition-promotion-code");
            var $multiple_code = $parent.find('.multiple_code');
            var $min_length = $parent.find('.multiple_code-min_length');
            var $end_with = $parent.find('.multiple_code-end_with');
            var min_length = $this.val().length;

            if ($this.val())
            {
                $multiple_code.prop("checked", true);
            }
            else
            {
                $multiple_code.prop("checked", false);
                min_length = 1;
            }

            $min_length.text(min_length);
            $end_with.val(min_length);
        });

        function elementDisable(element)
        {
            element = $(element);
            element.hide();
            element.find("input").prop("disabled", true);
            element.find("select").prop("disabled", true);
        }

        function elementEnable(element)
        {
            element = $(element);
            element.show();
            element.find("input").prop("disabled", false);
            element.find("select").prop("disabled", false);
        }

        // event for
        $("input[name=\"effects[discount][on]\"]").on("change", function(e) {
            var $this = $(this);
            if ($this.val() == "following" && $this.prop("checked") == true)
            {
                elementEnable("div.effect-discount-on-following-extra");
            }
            else
            {
                elementDisable("div.effect-discount-on-following-extra");
            }

        });

        $("input[name=\"effects[discount][on]\"]").change();

        // disable free item and Shipping - for next
        // use css to hide instead
//        elementDisable("#auto-effect .effects_free");
//        elementDisable("#auto-effect .effects_shipping");

        // event for promotion category change
        $("select[name=\"promotion_category\"]").change(function() {


            // check promotion_category is custom or not
            if ($(this).val() == "custom") {
                // disable temporary
                // elementEnable("#auto-effect .effects_free");
                // elementEnable("#auto-effect .effects_shipping");
            }
            else
            {
                // when promotion_category is coupond_code, cash_voucher or trueyou
                elementEnable("#auto-effect .effects_discount");
                // auto checked at discount
                // check in html instead
//                $("#auto-effect .effects_discount input[value=\"discount\"]").prop("checked", true);
                elementDisable("#auto-effect .effects_free");
                elementDisable("#auto-effect .effects_shipping");
            }

            if ($(this).val() == "cash_voucher") {
                elementDisable('.effects_discount .discount-percent');
                elementDisable('.effects_discount .grid_1');
                elementDisable('.effects_discount .grid_4');

                $(".effect-discount-price").prop("checked", true);
                $(".effects_discount").find("input[name='effects[type][]']").prop("checked", true);
                $("input[name=\"effects[discount][on]\"][value=\"cart\"]").prop("checked", true);
            }
            else
            {
                elementEnable('.effects_discount .discount-percent');
                elementEnable('.effects_discount .grid_1');
                elementEnable('.effects_discount .grid_4');
            }

            if ($(this).val() == "trueyou") {
                // elementDisable('.effects_discount .discount-price');
                // $(".effect-discount-percent").prop("checked", true);

                elementDisable("#promotion-condition");
                elementEnable("#promotion-condition-trueyou");
                $(".effect-discount-on-cart").prop("disabled", true).parent().hide();
                $(".effect-discount-on-same_product").prop("disabled", "disabled").parent().hide();

                $(".effect-discount-on-following").click();
            } else {
                // elementEnable('.effects_discount .discount-price');

                elementEnable("#promotion-condition");
                elementDisable("#promotion-condition-trueyou");
                $(".effect-discount-on-cart").prop("disabled", false).parent().show();
                $(".effect-discount-on-same_product").prop("disabled", false).parent().show();
            }

        });

        // trigger change event once
        $("select[name=\"promotion_category\"]").change();

        $("select[name=\"effects[discount][which]\"]").change(function() {
            var val = $(this).val();
            if (val == "brand" || val == "collection") {
                elementDisable("#exclude_variant");
                elementEnable("#exclude_product");
            } else if (val == "product") {
                elementEnable("#exclude_variant");
                elementDisable("#exclude_product");
            } else if (val == "variant") {
                elementDisable("#exclude_variant");
                elementDisable("#exclude_product");
            }
        });

        // trigger change event once
        $("select[name=\"effects[discount][which]\"]").change();

        $(".following_exclude input[type=\"text\"]").on("keyup", function() {
            var $this = $(this);
            if ($this.val())
            {
                $this.parent("li").find("label > input").prop("checked", true);
            }
            else
            {
                $this.parent("li").find("label > input").prop("checked", false);
            }
        });
    };

    self.createCondition = function()
    {
        var template = {};
    };

    return self;

})($);

jQuery.fn.outerHTML = function(s) {
    return s
            ? this.before(s).remove()
            : jQuery("<p>").append(this.eq(0).clone()).html();
};

$(function() {

    PCMS.Promotion.Create.init();

});