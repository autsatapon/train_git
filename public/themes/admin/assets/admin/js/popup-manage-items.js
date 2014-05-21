var PCMS = PCMS || {};

PCMS.ManageItems = (function() {

    var self = self || {};

    self.width = '90%';
    self.height = 700;

    self.class = 'popup-manage-items';
    self.btn = null;
    self.target = null;
    self.ul = null;
    self.popup_id = 'popup-manage-dialog-';
    self.popups = [];
    self.uls = [];
    self.selects = [];

    self.label = {
        'variant': 'warning',
        'exclude-variant': 'danger',
        'product': 'info',
        'brand': 'success'
    };

    self.popupTemplate = '<div id="popup-manage-dialog-{popupid}" style="display: none;"></div>';
    self.iframeTemplate = '<iframe id="iframe-manage-items" src="/products/search/popup/{type}?{params}" style="border: none; width:100%; height: 90%;" onload="javascript: PCMS.ManageItems.resizeIframe(this);"></iframe>';
    self.ulTemplate = '<ul id="popup-manage-ul-{popupid}" style="margin: 0px;"></ul>';
    self.liTemplate = '<li data-pkey="{pkey}" style="list-style-type:none;">{label} {title} (<a href="#" class="remove-item" data-pkey="{pkey}" data-popup-id="{popupid}">remove</a>)</li>';
    self.selectBrandTemplate = '<select id="popup-manage-select-{popupid}" class="popup-manage-select" data-popup-id="{popupid}">{options}</select>';
    self.optionBrandTemplate = '<option value="{pkey}">{name}</option>';

    self.init = function()
    {
        // draw ul and popup template on each button
        // draw selected pkeys
        // draw multiple select if type is brand
        $('.'+self.class).each(function ()
        {
            var id = $(this).data('popup-id');

            self.uls[id] = $(self.ulTemplate.replace(/{popupid}/g, id));
            self.uls[id].insertAfter($(this));

            self.popups[id] = $(self.popupTemplate.replace(/{popupid}/g, id));
            self.popups[id].insertAfter($(this));

            var datas = $(this).data('popup-datas');

            if (typeof datas != 'undefined')
            {
                if (datas == 'null')
                {
                    datas = [];
                }

                self.defineAction($(this));
                self.addItems(datas);
                self.defineAction(null);
            }
        });

        // on click, define btn and target to action
        $('.'+self.class).on('click', function()
        {
            self.defineAction($(this));

            self.openPopup($(this));
        });

        $('body').on('click', '.remove-item', function(e)
        {
            e.preventDefault();

            var pkey = $(this).data('pkey');

            self.defineAction($('.'+self.class+'[data-popup-id="'+$(this).data('popup-id')+'"]'));

            self.removeItem(pkey);

            self.defineAction(null);
        });
    };

    self.defineAction = function(btn)
    {
        if (btn == null)
        {
            self.btn = null;
            self.target = null;
            self.ul = null;
        }
        else
        {
            self.btn = btn;
            self.target = $('#'+self.btn.data('popup-target-id'));
            self.ul = $('#popup-manage-ul-'+self.btn.data('popup-id'));
        }
    };

    self.openPopup = function()
    {
        var type = self.getType();

        var pkeys = [];

        var params = '';

        if (type == 'exclude-variant' || type == 'exclude-product')
        {
            pkeys = eval(self.btn.data('popup-product-pkeys'));
        }

        params += 'pkeys='+pkeys.join();

        var app_id = $('input[name="app_id"]').val();

        if (type == 'collection' && app_id)
        {
            params += '&app_id=' + app_id;
        }

        var discountWhich = $('#effect-discount-which').val();
        if ((type == 'exclude-variant' || type == 'exclude-product') && discountWhich)
        {
            params += '&parent=' + discountWhich;
        }

        // var pkeys = (btn.data('popup-pkeys'))?btn.data('popup-pkeys'):[];
        var iframe = self.iframeTemplate.replace(/{type}/g, type).replace(/{params}/g, params);

        var width = (type=='brand')?400:self.width;
        var height = (type=='brand')?400:self.height;

        // open popup
        $('#'+self.popup_id+self.btn.data('popup-id'))
        .dialog(
        {
            title: self.btn.data('popup-title') || 'Manage items',
            width: width,
            height: height,
            modal: true,
            draggable: false,
            resizable: false,
            open: function (e, u)
            {
                $(this).html(iframe);
            },
            close: function(e, u)
            {
                $('#iframe-manage-items').remove();
            }
        });
    };

    self.addItems = function(datas)
    {
        var type = self.getType();
        var pkeys = [];
        var val = {};
        var li = '';

        // get already added items and remove empty
        $.each(self.target.val().split(','), function(k, v)
        {
            if (v != '')
            {
                val[v] = '';
            }
        });

        // assign added items from popup to already added items
        $.each(datas, function(k, v)
        {
            li = self.btn.data('popup-li-template') || self.liTemplate;

            val[v['pkey']] = '';

            // remove dupplicate li
            li = li.replace(/{label}/g, '<span class="label label-'+self.label[type]+'">'+type+'</span>')
                    .replace(/{id}/g, v['id'])
                    .replace(/{title}/g, v['title'])
                    .replace(/{pkey}/g, v['pkey'])
                    .replace(/{popupid}/g, self.btn.data('popup-id'));

            // draw li to ul
            if (self.ul.find('[data-pkey="'+v['pkey']+'"]').length == 0)
            {
                self.ul.append(li);
            }
        });

        // flip key to value
        $.each(val, function(k, v)
        {
            pkeys.push(k);
        });

        // join with ',' and put to target
        self.target.val(pkeys.join());
    };

    self.removeItem = function(pkey)
    {
        var pkeys = [];
        var val = {};

        // get already added items and remove empty
        $.each(self.target.val().split(','), function(k, v)
        {
            if (v != '' && v != pkey)
            {
                val[v] = '';
            }
        });

        // flip key to value
        $.each(val, function(k, v)
        {
            pkeys.push(k);
        });

        // join with ',' and put to target
        self.target.val(pkeys.join());

        // remove li
        self.ul.find('[data-pkey="'+pkey+'"]').remove();
    };

    self.removeAllItems = function(btn)
    {
        btn = btn || self.btn;

        self.defineAction(btn)

        $.each(self.target.val().split(','), function(k, v)
        {
            self.removeItem(v);
        });

        self.defineAction(null);
    };

    self.closeIframe = function()
    {
        $('#'+self.popup_id+self.btn.data('popup-id')).dialog('destroy');
    };

    self.resizeIframe = function(iframe)
    {
        iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
    };

    self.getType = function(btn)
    {
        btn = btn || self.btn;

        try
        {
            type = eval(btn.data('popup-type'));
        }
        catch (e)
        {
            type = eval('"'+btn.data('popup-type')+'"');
        }

        return type;
    };

    return self;

})($);

$(function() {

    PCMS.ManageItems.init();

});