// this file don't write for single use
// please l;ook at ProductNewMaterialController@getStep3

// random seed base16 char 4 lengths
function s4() {
  return Math.floor((1 + Math.random()) * 0x10000)
             .toString(16)
             .substring(1);
};

var PCMS = PCMS || {};
PCMS.events = PCMS.events || {};
PCMS.funcs = PCMS.funcs || {};

PCMS.events.select_style_option = function(event)
{
    var $select = $(this);
    var $option = $select.find(":selected");
    var meta_type = $option.data("meta-type"),
        meta_value = $option.data("meta-value"),
        iframe_url = $option.data("iframe");

    var style_option_detail = $select.nextAll(".style_option_detail");


    if (meta_type)
    {
      var option_meta_value = style_option_detail.children(".style_option_meta_value");


      if (meta_type == "color")
      {
        option_meta_value.html($("<div/>")
          .css("width", "").css("padding-left", "").css("padding-right", "")
          .css("background-color", meta_value).css("width", "20px").css("height", "20px"));
      }

      if (meta_type == "image")
      {
        // console.log();
        option_meta_value.html($("<img/>").attr("src", meta_value).html())
          .css("width", "").css("padding-left", "").css("padding-right", "");
      }

      if (meta_type == "text")
      {
        // console.log();
        option_meta_value.text(meta_value).css("width", "auto").css("padding-left", "4px").css("padding-right", "4px");
      }



    }

    style_option_detail.children("a.various-large").attr("href", iframe_url);

    // set text to option text
    style_option_detail.children(".style_option_text").text($option.text());

    style_option_detail.removeClass("hide");

};

$(".select_style_option").on("change", PCMS.events.select_style_option);

PCMS.events.open_new_style_option = function(event)
{
  var $this = $(this),
      target = $this.data("target"),
      style_type_id = $this.data("style-type-id"),
      style_type_name = $this.data("style-type-name"),
      iframe_url = $this.data("iframe"),
      // $dialog = $("#" + target);
      $dialog = $(".style_option_iframe"),
      $iframe = $dialog.children("iframe");


    // $dialog.find(".style_type_id").val(style_type_id);
    // $dialog.find(".style_type_name").text(style_type_name);
    $iframe.attr("src", iframe_url);
    $dialog.dialog("open");

};

$(".style_option_iframe").dialog({
                show: false,
                autoOpen: false,
                title: "Create style option",
                modal: true,
                width: "520",
                height: "400",
                buttons: [{
                    text: "Create",
                    name: "create_new_style_option",
                    value: "true",
                    class: "call_post_form",
                    click: function () {}
                }]
            });
$(".open_new_style_option").on("click", PCMS.events.open_new_style_option);



$("select.select_style_option").on("change", function(e){
    updateStyleOptionList(this);
});

// first init
//
window.onload = function(){
    var inited = {};
    $("select.select_style_option").each(function(index, select){
        var $this = $(this),
            style_type_id = $this.data("style-type");

        if (! inited[style_type_id])
        {
            // trigger update
            updateStyleOptionList(this);

            inited[style_type_id] = true;
        }
    });

    $(".select_style_option").on("change", function(e){
      $("#save_submit").prop("disabled", false);
      $("#alert-style-options-meta").hide();
      $(".edit_style_option").each(function(index, select){
        var $this = $(this);
        if ($this.hasClass('btn-warning'))
        {
          $("#save_submit").prop("disabled", true);
          $("#alert-style-options-meta").show();
        }
      });
    });

    $(".select_style_option").change();
};