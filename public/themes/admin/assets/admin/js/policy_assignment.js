

function vendorRebuild(shopId, selected, done)
{
    var url = "/policies/assigns/select-option/vendor/"
                + shopId
                + (selected ? '/' + selected : '');
    var $vendor = $("select[name='vendor']");
    resetCombobox($vendor);
    resetCombobox($("select[name='brand']"));
    $vendor.load(url, done);
}

function brandRebuild(vendorId, selected, done)
{
    var url = "/policies/assigns/select-option/brand/"
                + vendorId
                + (selected ? '/' + selected : '');
    var $brand = $("select[name='brand']");
    resetCombobox($brand);
    $brand.load(url, done);
}

function resetCombobox($element)
{
    $element.prop("disabled", "true").html("").next('span').find('input').val("");
}

$("select[name='shop']").on('change', function(e){
    var val = $(this).val();

    if ($(this).prop("disabled") == true)
    {
        $("select[name='vendor']").prop("disabled", true).html("").change();
        return;
    }
    var $loading = ajaxLoadingStart($('.mws-form-inline'));
    vendorRebuild(val, null, function( response, status, xhr ) {
        ajaxLoadingStop($loading);
    });
});

$("select[name='vendor']").on('change', function(e){
    var val = $(this).val();
    if ($(this).prop("disabled") == true)
    {
        $("select[name='brand']").prop("disabled", true).html("").change();
        return;
    }
    var $loading = ajaxLoadingStart($('.mws-form-inline'));
    brandRebuild(val, null, function( response, status, xhr ) {
        ajaxLoadingStop($loading);
    });
});

jQuery.fn.slideLeftHide = function(speed, callback) {
  this.animate({
    width: "hide",
    paddingLeft: "hide",
    paddingRight: "hide",
    marginLeft: "hide",
    marginRight: "hide"
  }, speed, callback);
}

jQuery.fn.slideLeftShow = function(speed, callback) {
  this.animate({
    width: "show",
    paddingLeft: "show",
    paddingRight: "show",
    marginLeft: "show",
    marginRight: "show"
  }, speed, callback);
}


var selectPrevious;
$("tbody").on('focus', 'select.policy_relate_update', function(e){
    selectPrevious = this.value;
});


$("tbody").on('change', 'select.policy_relate_update', function(e){

    var $this = $(this),
        data_id = $this.data('id'),
        data_type = $this.data('type'),
        data_value = $this.val(),
        data_policy_relate_id = $this.data('policy-relate-id'),
        allSelect = $("tbody").find('select.policy_relate_update');

    $( "#dialog-confirm" ).dialog({
        resizable: false,
        height:200,
        modal: true,
        buttons: {
            "OK": function() {
                allSelect.prop('disabled', true);
                var $loading = ajaxLoadingStart($this.parent('td'));
                $.post( "/policies/assigns/ajax-update", { id: data_id, type: data_type, value: data_value, policy_relate_id: data_policy_relate_id }, function( data ) {
                    // $( ".result" ).html( data );
                    console.log(data);
                    if (data.status == "success")
                    {
                        var $icon = $("<i/>").addClass("icon-ok")
                                    .text(" Saved!")
                                    // .css( "overflow", "hidden" )
                                    .css( "color", "green" )
                                    .css( "padding-left", "10px" );
                        $icon.insertAfter($this);
                        setTimeout(function(){
                            // $icon.slideLeftHide(9000, function(e){
                            //     $(this).remove();
                            // });
                            $icon.fadeOut(700, function(e){
                                $(this).remove();
                            });
                        }, 3000);

                        if (data.policy_relate_id)
                        {
                            $this.data('policy-relate-id', data.policy_relate_id);
                            $this.attr('data-policy-relate-id', data.policy_relate_id);
                        }
                    }
                    else
                    {
                        var $icon = $("<i/>").addClass("icon-remove")
                                    .text(" " + data.message)
                                    // .css( "overflow", "hidden" )
                                    .css( "color", "red" )
                                    .css( "padding-left", "10px" );
                        $icon.insertAfter($this);
                    }

                    ajaxLoadingStop($loading);
                    allSelect.prop('disabled', false);
                });
                $( this ).dialog( "close" );
            },
            Cancel: function() {
                $this.find("option[value='"+ selectPrevious +"']").prop("selected", true);
                $( this ).dialog( "close" );
            }
        }
    });


});