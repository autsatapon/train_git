<?php

use Teepluss\Theme\Theme;
use Teepluss\Theme\Widget;

class WidgetMeta extends Widget {

    /**
     * Widget template.
     *
     * @var string
     */
    public $template = 'meta';

    /**
     * Watching widget tpl on everywhere.
     *
     * @var boolean
     */
    public $watch = false;

    /**
     * Arrtibutes pass from a widget.
     *
     * @var array
     */
    public $attributes = array(
        'app'     => null,
        'content' => null,
    );

    /**
     * Code to start this widget.
     *
     * @return void
     */
    public function init(Theme $theme)
    {
        // Using fancy box to create dialog.
        $theme->asset()->serve('fancybox');
        $theme->asset()->container('embed')->writeScript('callback', '

            function metaInsert(result) {

                var html = \'\
                    <div class="mws-form-cols node">\
                        <div class="mws-form-col-2-8">\
                            <strong>\' + result.key + \'</strong>\
                        </div>\
                        <div class="mws-form-col-2-8"><span id="meta-id-\' + result.id + \'" style="display:inline-block; width:600px; word-wrap:break-word;">\' + result.value + \'</span></div>\
                        <span class="pull-right"><a href="/metas/edit/\' + result.id + \'" class="various fancybox.iframe">Edit</a> |\
                        <a href="/metas/ajax-delete/\' + result.id + \'" class="ajax-delete">Delete</a></span>\
                    </div>\
                \';

                $("#meta-app-" + result.app_id).append(html);

                setTimeout(function() { $("#meta-app-" + result.app_id).scrollView() }, 300);
            }

            function metaUpdate(result) {

                $("#meta-id-" + result.id).html(result.value);

                setTimeout(function() { $("#meta-app-" + result.app_id).scrollView() }, 300);
            }

            $("body").on("click", ".ajax-delete", function(e) {

                if (confirm("Delete?")) {
                    var $that  = $(this);
                    $that.parents(".node:first").fadeOut("fast", function() {
                        var href = $that.attr("href");
                        $.get(href, function(res) {
                            $(this).remove();
                        });
                    });
                }

                e.preventDefault();
            });

        ');

        $content = $this->getAttribute('content');

        $this->setAttribute('model', get_class($content));



        // Initialize widget.

        //$theme->asset()->usePath()->add('widget-name', 'js/widget-execute.js', array('jquery', 'jqueryui'));
        //$this->setAttribute('user', User::find($this->getAttribute('userId')));

        //s( $this->getAttributes() );

    }

    /**
     * Logic given to a widget and pass to widget's view.
     *
     * @return array
     */
    public function run()
    {
        // $label = $this->getAttribute('label');

        // //$this->setAttribute('label', 'changed');

        //$attrs = $this->getAttributes();

        //return $attrs;
    }

}