// write by EThaiZone
// Dependencies are jquery and underscore.

var form_translation = function (mainNode, variable) {
    // event for add button
    $(".add" , "#" + mainNode).click(function(e) {
        var select = $("#" + mainNode + " > div > select");

        // set dropdown value
        select.html('<option value="">Select language</option>');

        // set parent hide if remaining is blank.
        if(variable.remaining.length < 1) {
            $(this).parent("div").hide();
            return true;
        }

        // set each locale
        $.each(variable.remaining, function(key, value) {
            select.append('<option value="'+value+'">' + variable.locale[value] + '</option>');
        });

        // show it
        select.show();
    });

    // event for select box
    $(".locale", "#" + mainNode).change(function(e) {
        var select = $(this);
        var locale = this.value;

        var name = variable.input_prefix+"["+variable.input_name+"]["+locale+"]";

        if(variable.onCreating) {
            variable.onCreating(name);
        }

        // insert new element
        var html = variable.element({
            id: mainNode + "_" + locale,
            locale: locale, code: locale.substring(3),
            name: name
        });
        $("#" + mainNode + " > div:last-child").before(html);

        // unset locale that selected
        for (var key in variable.remaining) {
            if (variable.remaining[key] == locale) {
                variable.remaining.splice(key, 1);
            }
        }

        // if en_US is select
        if (locale == 'en_US')
        {
            // hide delete button
            $("#" + mainNode).find("div[locale='en_US']").find(".delete").hide();
        }

        // trigger event click at add button - for refresh select box
        $(".add" , "#" + mainNode).trigger('click');

        if(variable.onCreated) {
            variable.onCreated(name);
        }
    });

    // event for delete button
    $("#" + mainNode).on("click", ".delete", function(e) {
        var parent = $(this).parent("div").parent("div");
        var locale = parent.attr("locale");

        // get name of
        var name = parent.find('div > span').next().attr('name');

        if(variable.onDeleting) {
            variable.onDeleting(name);
        }

        // remove input of that locale
        parent.remove();

        // set locale back to remaining
        variable.remaining.push(locale);

        // show div of add button
        $("#" + mainNode + " > div:last-child").show();

        // trigger event click at add button - for refresh select box
        $(".add" , "#" + mainNode).trigger('click');

        if(variable.onDeleted) {
            variable.onDeleted(name);
        }
    });
};