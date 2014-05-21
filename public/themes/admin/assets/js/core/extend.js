$.fn.scrollView = function () {
    return this.each(function () {
        $("html, body").animate({
            scrollTop: $(this).offset().top
        }, 1000);
    });
}

// make input reset can reset all to input old and combobox
$("input[type='reset']").on('click', function(e){
    var $this = $(this),
        $parent = $this.parents("form");

    $parent.find("input[type='text']").each(function(index, input){
        $(input).removeAttr("value");
    });
    $parent.find("select.suggestbox").each(function(index, select){
        $(select).prop("disabled", true);
    });
    $parent.find("select.combobox").each(function(index, select){
        $(select).prop("disabled", true);
    });

});

function ajaxLoadingStart(element)
{
    var $loading = $("<div/>").addClass("ajax-loading");
    if(element) {
        //Set div over target element
        var $el = $(element);


        $loading.css({
            opacity: 0.5,
            position: 'absolute',
            top:     $el.offset().top,
            left:    $el.offset().left,
            width:   $el.outerWidth(),
            height:  $el.outerHeight()
        });
    } else {
        //Set div over whole windows and fixed position
        $loading.css({
            opacity:  0.5,
            position: 'fixed',
            top:      0,
            left:     0,
            // width:    $(window).width(),
            // height:   $(window).height()
            width:    "100%",
            height:   "100%"
        });
    }

    //Stop scrolling
    // var scrollPosition = [
    //     self.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft,
    //     self.pageYOffset || document.documentElement.scrollTop  || document.body.scrollTop
    // ];
    // var html = $('html'); // it would make more sense to apply this to body, but IE7 won't have that
    // html.data('scroll-position', scrollPosition);
    // html.data('previous-overflow', html.css('overflow'));
    // html.css('overflow', 'hidden');
    // window.scrollTo(scrollPosition[0], scrollPosition[1]);
    //

    $("body").append($loading);

    $loading.fadeIn(500);

    return $loading;
}

function ajaxLoadingStop($loading)
{
    //Allow scrolling
    // var html = $('html');
    // var scrollPosition = html.data('scroll-position');
    // html.css('overflow', html.data('previous-overflow'));
    // window.scrollTo(scrollPosition[0], scrollPosition[1])

    $loading.fadeOut(500, function(e){
        $(this).remove();
    });
}
