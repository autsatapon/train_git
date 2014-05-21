var PCMS = PCMS || {};

PCMS.ManageItemsIframe = (function() {

    var self = self || {};

    self.types = {
        'variant': 'add-variants',
        'exclude-variant': 'add-variants',
        'product': 'add-products',
        'exclude-product': 'add-products'
    };

    self.init = function()
    {
        // init combobox
        self.combobox();

        // select all
        $('#select-all').on('change', function() {

            $('.add-products').prop('checked', $('#select-all').is(':checked'));

        });

        // set brand select combobox
        $('#popup-manage-items-select-brand').combobox();

        // on popup type is variant, select all variants by select product
        $('.products-all').on('change', function() {

            var pkey = $(this).data('pkey');

            if ($(this).is(':checked'))
            {
                $('.products-'+pkey).prop('checked', true);
            }
            else
            {
                $('.products-'+pkey).prop('checked', false);
            }

        });

        // submit the selected items
        $('#popup-manage-items-variant .add, #popup-manage-items-exclude-variant .add, #popup-manage-items-product .add, #popup-manage-items-exclude-product .add').on('click', function()
        {
            var datas = [];
            var selector = self.types[$('#type').val()];

            $('.'+selector+':checked').each(function()
            {
                datas.push($(this).data('data'));
            });

            parent.PCMS.ManageItems.addItems(datas);

            parent.PCMS.ManageItems.closeIframe();
        });

        // submit the brand
        $('#popup-manage-items-brand .add').on('click', function()
        {
            var pkey = $('#popup-manage-items-select-brand').val();

            var data = {
                pkey: pkey,
                title: $('#popup-manage-items-select-brand').find('option[value="'+pkey+'"]').html()
            };

            parent.PCMS.ManageItems.addItems([data]);

            parent.PCMS.ManageItems.closeIframe();
        });

        // submit the selected items
        $('#popup-manage-items-collection .add').on('click', function()
        {
            var datas = [];
            // var selector = self.types[$('#type').val()];

            $('input[name="collection[]"]:checked').each(function()
            {
                datas.push($(this).data('data'));
            });

            parent.PCMS.ManageItems.addItems(datas);

            parent.PCMS.ManageItems.closeIframe();
        });
    };

    self.combobox = function()
    {
        (function( $ ) {
            $.widget( "custom.combobox", {
                // default options
                options: {
                    // set mode of combo box
                    //  - "select" to simulate select box.
                    //  - "select-semi" same as "select" mode but It willn't select first option at first.
                    //  - "suggest" to show combo as suggest but can add another to value.
                    //  - "suggest-semi" same as "suggest" mode but It will select first option at first.
                    mode : 'select'
                },

                _create: function(d) {

                    // get class of select and use it set to span - should inherit class from select
                    this.select_class = this.element.attr('class');

                    // get style of select and use it set to span - should inherit style from select
                    this.select_style = this.element.attr('style');

                    this.wrapper = $( "<span>" )
                            .addClass( "custom-combobox input-append ")
                            .insertAfter( this.element );

                    this.element.hide();
                    this._createAutocomplete();
                    this._createShowAllButton();
                },

                _createAutocomplete: function() {
                    // var selected = this.element.children( ":selected" )

                    // in suggest mode - when option don't select to don't show it.
                    // if (this.options.mode == 'suggest')
                    // {
                    var selected = "";

                    this.element.children("option").each(function(index, option) {
                        var $this = $(this);
                        var html = $this[0].outerHTML;
                        if (html.search("selected") > -1) {
                            selected = $this;
                        }
                    });

                    // }

                    // set value when selected is exists.
                    var value = selected && selected.val() ? selected.text() : "";

                    // if(this.options.mode == 'suggest' || this.options.mode == 'select-semi') {
                    // set select to disabled when value don't exists
                    if (! value)
                    {
                        this.element.prop('disabled', true);
                    }

                    this.input = $( "<input>" ).attr( "type", "text" )
                            .appendTo( this.wrapper );


                    // if(this.options.mode != 'suggest' && this.options.mode != 'select-semi') {
                    // set value only when value is exists
                    if (value)
                    {
                        this.input.val( value );
                    }

                    this.input.attr( "title", "" )
                            .attr( "style", this.select_style)
                            .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left " + this.select_class)
                            .autocomplete({
                        delay: 0,
                        minLength: 0,
                        source: $.proxy( this, "_source" )
                    })
                            .tooltip({ trigger: 'manual' });

                    this._on( this.input, {
                        autocompleteselect: function( event, ui ) {
                            this.element.prop('disabled', false);
                            ui.item.option.selected = true;
                            this._trigger( "select", event, {
                                item: ui.item.option
                            });
                            // add trigger change event
                            this.element.trigger("change");
                        },

                        autocompletechange: "_removeIfInvalid"
                    });
                },

                _createShowAllButton: function() {
                    var input = this.input,
                    wasOpen = false;

                    $( "<span>" )
                            .attr( "tabIndex", -1 )
                            .attr( "title", "Show All Items" )
                            .tooltip()
                            .appendTo( this.wrapper )
                            .html('<i class="icon-chevron-down"></i>')
                            .addClass( "add-on" )
                            .mousedown(function() {
                        wasOpen = input.autocomplete( "widget" ).is( ":visible" );
                    })
                            .click(function() {
                        input.focus();

                        // Close if already visible
                        if ( wasOpen ) {
                            return;
                        }

                        // Pass empty string as value to search for, displaying all results
                        input.autocomplete( "search", "" );
                    });
                },

                _source: function( request, response ) {
                    var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                    response( this.element.children( "option" ).map(function() {
                        var text = $( this ).text();
                        if ( this.value && ( !request.term || matcher.test(text) ) )
                            return {
                                label: text,
                                value: text,
                                option: this
                            };
                    }) );
                },

                _removeIfInvalid: function( event, ui ) {

                    if(this.options.mode == 'suggest') {
                        var value = this.input.val();
                        // if value is blank so exit
                        if( ! value ) {
                            this.element.prop('disabled', true);
                            return;
                        }
                    }

                    // make select active
                    this.element.prop('disabled', false);

                    // Selected an item, nothing to do
                    if ( ui.item ) {
                        return;
                    }

                    // Search for a match (case-insensitive)
                    var value = this.input.val(),
                    valueLowerCase = value.toLowerCase(),
                    valid = false;
                    this.element.children( "option" ).each(function() {
                        if ( $( this ).text().toLowerCase() === valueLowerCase ) {
                            this.selected = valid = true;
                            return false;
                        }
                    });

                    // if(this.options.mode != 'select' && this.options.mode != 'select-semi') {
                    if(this.options.mode == 'suggest') {
                        // not found in select so add it.
                        if ( ! valid ) {
                            option_value = value;
                            option_value = option_value.replace('"', '[doubleq]');
                            option_value = option_value.replace('"', '[doubleq]');
                            option_value = option_value.replace('"', '[doubleq]');
                            option_value = option_value.replace('"', '[doubleq]');
                            option_value = option_value.replace('[doubleq]', '\\"');
                            option_value = option_value.replace('[doubleq]', '\\"');
                            option_value = option_value.replace('[doubleq]', '\\"');
                            option_value = option_value.replace('[doubleq]', '\\"');
                            this.element.append('<option value="' + option_value + '" selected>' + value + '</option>');
                            valid = true;
                        }
                    }

                    // Found a match, nothing to do
                    if ( valid ) {
                        return;
                    }

                    // Remove invalid value
                    this.input
                            .val( "" )
                            .attr( "data-original-title", value + " didn't match any item" )
                            .tooltip( "show" );
                    this.element.val( "" ).prop('disabled', true).change();
                    this._delay(function() {
                        this.input.tooltip( "hide" ).attr( "title", "" );
                    }, 2500 );
                    this.input.data( "ui-autocomplete" ).term = "";
                },

                _destroy: function() {
                    this.wrapper.remove();
                    this.element.show();
                }
            });
        })( jQuery );
    };

    return self;

})($);

$(function() {

    PCMS.ManageItemsIframe.init();

});