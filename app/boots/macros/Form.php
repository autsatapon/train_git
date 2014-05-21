<?php

/*
|--------------------------------------------------------------------------
| Form ckeditor render
|--------------------------------------------------------------------------
|
|   Render ckeditor with include dependency file
|
*/
Form::macro('ckeditor', function($name, $value = '', $extra = array())
{
    Theme::asset()->container('macro')->add('ckeditor', 'vendor/ckeditor/ckeditor.js', 'jquery');

    $input_name = (is_array($name)) ? $name['name'] : $name;

    // Set path to upload file.
    $path = 'uploads/filemanager';
    if ( isset($extra['model']) )
    {
        if ( is_object($extra['model']) && isset($extra['model']->id) )
        {
            $modelFolder = strtolower(get_class($extra['model']));
            $hashIdFolder = (floor($extra['model']->id / 1000));
            $modelId = $extra['model']->id;
            $path = sprintf('uploads/filemanager/%s/%d/%d', $modelFolder, $hashIdFolder, $modelId);
            // $path .= '/' . strtolower(get_class($extra['model'])) . '/' . (floor($extra['model']->id / 1000)) . '/' . $extra['model']->id;
        }

        unset($extra['model']);
    }
    $upload = array();
    $upload['url'] = URL::to($path);
    $upload['dir'] = App::make('path.public').'/'.$path;

    $data = base64_encode('salt'.json_encode($upload));

    @setcookie('fileManagerPathUpload', $data, null, '/');

    // ckeditor height
    $height = isset($extra['height']) ? $extra['height'] : "400px";
    unset($extra['height']);

    Theme::asset()->container('macro')->writeScript('ckeditor-assign-'.$input_name, "
        CKEDITOR.replace('".$input_name."', {
            height: '".$height."',
            filebrowserBrowseUrl: '/vendor/kcfinder/browse.php?type=files',
            filebrowserImageBrowseUrl: '/vendor/kcfinder/browse.php?type=images',
            filebrowserFlashBrowseUrl: '/vendor/kcfinder/browse.php?type=flash',
            filebrowserUploadUrl: '/vendor/kcfinder/upload.php?type=files',
            filebrowserImageUploadUrl: '/vendor/kcfinder/upload.php?type=images',
            filebrowserFlashUploadUrl: '/vendor/kcfinder/upload.php?type=flash'
        });
    ", 'ckeditor');

    return Form::textarea($name, $value, $extra);
});

Form::macro('brandDropdown', function($name, $value=null, $attributes=array())
{
	$list = Brand::orderBy('name')->lists('name', 'id');
	return Form::suggestBox($name, $list, $value, $attributes);
});

Form::macro('vendorDropdown', function($name, $value=null, $attributes=array())
{
	$vendors = VVendor::select('vendor_id', 'master_id', 'shop_id', 'name')->orderBy('name')->orderBy('master_id')->orderBy('shop_id')->get();
	$list = array();

	foreach($vendors as $vendor)
	{
		$list[$vendor['vendor_id']] = $vendor['name'].' | '.($vendor['master_id']!=false ? $vendor['master_id'] : $vendor['shop_id']);
	}

    // return Form::suggestBox('vendor_id', $list, $value, $attributes);
	return Form::suggestBox('vendor_id', $list, $value, $attributes);
});

/*
Form::macro('comboBox', function($name, $option, $value=null, $attributes=array()) {
    Theme::asset()->container('macro')->usePath()->add('jqueryui_autocomplate_combobox', 'admin/js/jqueryui.autocomplete.combobox.js', array('jquery'));
    Theme::asset()->container('macro')->writeScript('jqueryui_combobox_init', '
        $(function() {
            $( ".combobox" ).combobox();
        });
        ', array('jqueryui_autocomplate_combobox'));

    if(empty($attributes['class'])) {
        $attributes['class'] = 'combobox';
    } else {
        $attributes['class'] .= ' combobox';
    }

    Theme::asset()->writeStyle('comboBox', '
    .ui-menu-item:hover {
        background: rgba(0, 0, 0, 0.1);
    }
    .input-append .add-on {
        -webkit-border-radius: 0 4px 4px 0;
        -moz-border-radius: 0 4px 4px 0;
        border-radius: 0 4px 4px 0;
    }
    ', array('bootstrap'));

    return Form::select($name, $option, $value, $attributes);
});
*/

/*
|--------------------------------------------------------------------------
| Form text translatable
|--------------------------------------------------------------------------
|
|   Render input text for translate
|
*/
Form::macro('transText', function($model, $name, $extra = array(), $defaultLocale = array())
{
    $inputPrefix = 'translate';
    $configLocale = Config::get('locale');
    $remainingLocale = $configLocale;
    $localeCollection = array();

    $delete_button = '<button class="btn btn-small delete" type="button" style="margin: 2px 0px 0px 5px;"><i class="icon-trash"></i></button>';

    $uid = $name."_".md5(rand());

    // create form element
    $html = '<div class="form_translation" id="trans_text_'.$uid.'" style="margin-top: 10px;">';

    // loop all locale
    foreach($configLocale as $locale => $lang) {
        // create variable first for protect notice error
        $value = null;

        // if macro has model...
        if($model) {
            // when macro has model - query it
            if(empty($localeCollection[$locale])) {
                $localeCollection[$locale] = $model->translates()->whereLocale($locale)->first();
            }
            // show input only when found strinmg that translated in database
            if( ! empty($localeCollection[$locale]->{$name})) {
                $value = $localeCollection[$locale]->{$name};
            }
        }

        // get old value
        $inputName = $inputPrefix.'.'.$name.'.'.$locale;
        if(Input::old($inputName) !== null)
        {
            $value = Input::old($inputName);
        }

        // if locale is in defaultLocale or has translated string in database - show element
        if (in_array($locale, $defaultLocale) || $value) {
        // if($value) {
            $html .= '<div id="trans_text_'.$name.'_'.$locale.'" locale="'.$locale.'" class="large">';

            // create form input element
            $html .= '<div class="input-prepend large"><span class="add-on">'.substr($locale, 3, 2).'</span>';
            $html .= Form::text($inputPrefix.'['.$name.']['.$locale.']', $value, $extra);

            // create delete button - except en_US
            if($locale != 'en_US') {
                $html .= $delete_button;
            }
            $html .= '</div>';
            $html .= '</div>';

            // unset locale that showed.
            unset($remainingLocale[$locale]);
        }

    }

    // get locale remaining that don't show for set in javascript later
    $remaining = array_keys($remainingLocale);

    // button for add new form element
    $html .= '<div>';
    $html .= '<button class="btn btn-small add" type="button"><i class="icon-plus"> Add translation</i></button> ';
    $html .= Form::select('locale['.$name.']', array(), null, array('class' => 'locale', 'id' => 'select-locale_'.$name, 'style' => 'display:none;'));
    $html .= '</div>';

    // close div box for trans_text
    $html .= '</div>';

    // underscore js
    Theme::asset()->container('macro')->add('underscorejs', 'vendor/underscorejs/underscore-min.js');
    // form_translation
    Theme::asset()->container('macro')->usePath()->add('form_translation', 'admin/js/form_translation.js', array('jquery', 'underscorejs'));

    // js use for each element
    Theme::asset()->container('macro')->writeScript('trans_text_'.$uid, '
        var translation_'.$uid.' = {
            locale: '.json_encode($configLocale).',
            remaining: '.json_encode($remaining).',
            input_prefix: "'.$inputPrefix.'",
            input_name: "'.$name.'",
            element: _.template(
                \'<div id="<%= id %>" locale="<%= locale %>" class="large">\' +
                \'<div class="input-prepend large">\' +
                \'<span class="add-on"><%= code %></span>\' +
                \'<input type="text" name="<%= name %>"'.HTML::attributes($extra).'>\'+
                \''.$delete_button.'\' +
                \'</div> \' +
                \'</div>\'
            )
        };

        form_translation("trans_text_'.$uid.'", translation_'.$uid.');
        ', array('underscorejs'));

    return $html;
});


/*
|--------------------------------------------------------------------------
| Form textarea translatable
|--------------------------------------------------------------------------
|
|   Render textarea for translate
|
*/
Form::macro('transTextarea', function($model, $name, $extra = array(), $defaultLocale = array())
{
    $inputPrefix = 'translate';
    $configLocale = Config::get('locale');
    $remainingLocale = $configLocale;
    $localeCollection = array();

    if(empty($extra['class'])) {
        $extra['class'] = 'textarea-prepend';
    } else {
        $extra['class'] .= ' textarea-prepend';
    }

    $delete_button = '<button class="btn btn-small delete" type="button" style="margin: 2px 0px 0px 5px;"><i class="icon-trash"></i></button>';

    $uid = $name."_".md5(rand());

    // create form element
    $html = '<div class="form_translation" id="trans_textarea_'.$uid.'" style="margin-top: 10px;">';

    // loop all locale
    foreach($configLocale as $locale => $lang) {
        // create variable first for protect notice error
        $value = null;

        // if macro has model...
        if($model) {
            // when macro has model - query it
            if(empty($localeCollection[$locale])) {
                $localeCollection[$locale] = $model->translates()->whereLocale($locale)->first();
            }
            // show input only when found strinmg that translated in database
            if( ! empty($localeCollection[$locale]->{$name})) {
                $value = $localeCollection[$locale]->{$name};
            }
        }

        // get old value
        $inputName = $inputPrefix.'.'.$name.'.'.$locale;
        if(Input::old($inputName) !== null)
        {
            $value = Input::old($inputName);
        }

        // if locale is in defaultLocale or has translated string in database - show element
        if (in_array($locale, $defaultLocale) || $value) {
        // if($value) {
            $html .= '<div id="trans_textarea_'.$name.'_'.$locale.'" locale="'.$locale.'" class="large">';

            // create form input element
            $html .= '<div class="input-prepend large"><span class="add-on">'.substr($locale, 3, 2).'</span>';
            $html .= Form::textarea($inputPrefix.'['.$name.']['.$locale.']', $value, $extra);

            // create delete button - except en_US
            if($locale != 'en_US') {
                $html .= $delete_button;
            }
            $html .= '</div>';
            $html .= '</div>';

            // unset locale that showed.
            unset($remainingLocale[$locale]);
        }

    }

    // get locale remaining that don't show for set in javascript later
    $remaining = array_keys($remainingLocale);

    // button for add new form element
    $html .= '<div>';
    $html .= '<button class="btn btn-small add" type="button"><i class="icon-plus"> Add translation</i></button> ';
    $html .= Form::select('locale['.$name.']', array(), null, array('class' => 'locale', 'id' => 'select-locale_'.$name, 'style' => 'display:none;'));
    $html .= '</div>';

    // close div box for trans_text
    $html .= '</div>';

    // underscore js
    Theme::asset()->container('macro')->add('underscorejs', 'vendor/underscorejs/underscore-min.js');

    // form_translation
    Theme::asset()->container('macro')->usePath()->add('form_translation', 'admin/js/form_translation.js', array('jquery', 'underscorejs'));

    Theme::asset()->writeStyle('textarea-prepend', '
    textarea.textarea-prepend {
        -webkit-border-radius: 0 4px 4px 4px;
        -moz-border-radius: 0 4px 4px 4px;
        border-radius: 0 4px 4px 4px;
        height: 10em;
        font-size: 13px;
    }
        ');


    // js use for each element
    Theme::asset()->container('macro')->writeScript('trans_textarea_'.$uid, '
        var translation_'.$uid.' = {
            locale: '.json_encode($configLocale).',
            remaining: '.json_encode($remaining).',
            input_prefix: "'.$inputPrefix.'",
            input_name: "'.$name.'",
            element: _.template(
                \'<div id="<%= id %>" locale="<%= locale %>" class="large">\' +
                \'<div class="input-prepend large">\' +
                \'<span class="add-on"><%= code %></span>\' +
                \'<textarea name="<%= name %>"'.HTML::attributes($extra).'></textarea>\'+
                \''.$delete_button.'\' +
                \'</div> \' +
                \'</div>\'
            )
        };

        form_translation("trans_textarea_'.$uid.'", translation_'.$uid.');
        ', array('underscorejs'));

    return $html;
});


/*
|--------------------------------------------------------------------------
| Form Ckeditor translatable
|--------------------------------------------------------------------------
|
|   Render Ckeditor for translate
|
*/
Form::macro('transCkeditor', function($model, $name, $extra = array(), $defaultLocale = array())
{
    $inputPrefix = 'translate';
    $configLocale = Config::get('locale');
    $remainingLocale = $configLocale;
    $localeCollection = array();
    $rendered = array();

    // ckeditor height
    $height = isset($extra['height']) ? $extra['height'] : "400px";
    unset($extra['height']);

    if(empty($extra['class'])) {
        $extra['class'] = 'ckeditor-prepend';
    } else {
        $extra['class'] .= ' ckeditor-prepend';
    }

    $delete_button = '<button class="btn btn-small delete" type="button" style="margin: 2px 0px 0px 5px;"><i class="icon-trash"></i></button>';

    $uid = $name."_".md5(rand());

    // create form element
    $html = '<div class="form_translation" id="trans_ckeditor_'.$uid.'" style="margin-top: 10px;">';

    // loop all locale
    foreach($configLocale as $locale => $lang) {
        // create variable first for protect notice error
        $value = null;

        // if macro has model...
        if($model) {
            // when macro has model - query it
            if(empty($localeCollection[$locale])) {
                $localeCollection[$locale] = $model->translates()->whereLocale($locale)->first();
            }
            // show input only when found strinmg that translated in database
            if( ! empty($localeCollection[$locale]->{$name})) {
                $value = $localeCollection[$locale]->{$name};
            }
        }

        // get old value
        $inputName = $inputPrefix.'.'.$name.'.'.$locale;
        if(Input::old($inputName) !== null)
        {
            $value = Input::old($inputName);
        }

        // if locale is in defaultLocale or has translated string in database - show element
        if (in_array($locale, $defaultLocale) || $value) {
        // if($value) {
            $html .= '<div id="trans_ckeditor_'.$name.'_'.$locale.'" locale="'.$locale.'" class="large">';

            // create form input element
            $html .= '<div class="input-prepend large"><span class="add-on">'.substr($locale, 3, 2).'</span>';
            $html .= Form::textarea($inputPrefix.'['.$name.']['.$locale.']', $value, $extra);

            // create delete button - except en_US
            if($locale != 'en_US') {
                $html .= $delete_button;
            }
            $html .= '</div>';
            $html .= '</div>';

            // unset locale that showed.
            unset($remainingLocale[$locale]);

            // push rendered for create ckeditor instance later
            $rendered[] = $inputPrefix.'['.$name.']['.$locale.']';
        }

    }

    // get locale remaining that don't show for set in javascript later
    $remaining = array_keys($remainingLocale);

    // button for add new form element
    $html .= '<div>';
    $html .= '<button class="btn btn-small add" type="button"><i class="icon-plus"> Add translation</i></button> ';
    $html .= Form::select('locale['.$name.']', array(), null, array('class' => 'locale', 'id' => 'select-locale_'.$name, 'style' => 'display:none;'));
    $html .= '</div>';

    // close div box for trans_text
    $html .= '</div>';

    // underscore js
    Theme::asset()->container('macro')->add('underscorejs', 'vendor/underscorejs/underscore-min.js');

    // form_translation
    Theme::asset()->container('macro')->usePath()->add('form_translation', 'admin/js/form_translation.js', array('jquery', 'underscorejs'));

    Theme::asset()->writeStyle('ckeditor-prepend', '
    textarea.ckeditor-prepend {
        -webkit-border-radius: 0 4px 4px 4px;
        -moz-border-radius: 0 4px 4px 4px;
        border-radius: 0 4px 4px 4px;
        height: 10em;
        font-size: 13px;
    }
        ');


    // js use for each element
    Theme::asset()->container('macro')->writeScript('trans_ckeditor_'.$uid, '
        var translation_'.$uid.' = {
            locale: '.json_encode($configLocale).',
            remaining: '.json_encode($remaining).',
            input_prefix: "'.$inputPrefix.'",
            input_name: "'.$name.'",
            element: _.template(
                \'<div id="<%= id %>" locale="<%= locale %>" class="large">\' +
                \'<div class="input-prepend large">\' +
                \'<span class="add-on"><%= code %></span>\' +
                \'<textarea name="<%= name %>"'.HTML::attributes($extra).'></textarea>\'+
                \''.$delete_button.'\' +
                \'</div> \' +
                \'</div>\'
            ),
            onCreated : function(target) {
                CKEDITOR.replace( target , {
                    height: \''.$height.'\',
                    filebrowserBrowseUrl: \'/vendor/kcfinder/browse.php?type=files\',
                    filebrowserImageBrowseUrl: \'/vendor/kcfinder/browse.php?type=images\',
                    filebrowserFlashBrowseUrl: \'/vendor/kcfinder/browse.php?type=flash\',
                    filebrowserUploadUrl: \'/vendor/kcfinder/upload.php?type=files\',
                    filebrowserImageUploadUrl: \'/vendor/kcfinder/upload.php?type=images\',
                    filebrowserFlashUploadUrl: \'/vendor/kcfinder/upload.php?type=flash\'
                });
            },
            onDeleting : function(target) {
                CKEDITOR.instances[target].destroy();
            }
        };

        // render ckeditor when start
        var rendered = '.json_encode($rendered).';
        var length = rendered.length;
        for (var i = 0; i < length; i++) {
            CKEDITOR.replace( rendered[i] , {
                    height: \''.$height.'\',
                    filebrowserBrowseUrl: \'/vendor/kcfinder/browse.php?type=files\',
                    filebrowserImageBrowseUrl: \'/vendor/kcfinder/browse.php?type=images\',
                    filebrowserFlashBrowseUrl: \'/vendor/kcfinder/browse.php?type=flash\',
                    filebrowserUploadUrl: \'/vendor/kcfinder/upload.php?type=files\',
                    filebrowserImageUploadUrl: \'/vendor/kcfinder/upload.php?type=images\',
                    filebrowserFlashUploadUrl: \'/vendor/kcfinder/upload.php?type=flash\'
                });
        }

        form_translation("trans_ckeditor_'.$uid.'", translation_'.$uid.');
        ', array('underscorejs'));

    return $html;
});

/*
|--------------------------------------------------------------------------
| Form Autocompletion ComboBox
|--------------------------------------------------------------------------
|
|   Render autocomplete combobox with jquery
|
*/
Form::macro('comboBox', function($name, $option, $value=null, $attributes=array(), $semi = false) {

    // $mode = $semi ? 'select-semi' : 'select';

    $mode = "select";
    $jsmode = str_replace('-', '_', $mode);

    Theme::asset()->container('macro')->usePath()->add('jqueryui_autocomplate_combobox', 'admin/js/jqueryui.autocomplete.combobox.js', array('jquery'));
    Theme::asset()->container('macro')->writeScript('jqueryui_'.$mode.'box_init', "
        var {$jsmode}box = $( '.{$mode}box' ).combobox({mode: '$mode'});
        ", array('jqueryui_autocomplate_combobox'));

    if(empty($attributes['class'])) {
        $attributes['class'] = $mode.'box';
    } else {
        $attributes['class'] .= ' '.$mode.'box';
    }

    Theme::asset()->writeStyle('comboBox', '
    .ui-menu-item:hover {
        background: rgba(0, 0, 0, 0.1);
    }
    .input-append .add-on {
        -webkit-border-radius: 0 4px 4px 0;
        -moz-border-radius: 0 4px 4px 0;
        border-radius: 0 4px 4px 0;
    }
    .custom-combobox {
        margin: 0px;
    }
    ', array('bootstrap'));

    return Form::select($name, $option, $value, $attributes);
});

/*
|--------------------------------------------------------------------------
| Form Autocompletion SuggestBox
|--------------------------------------------------------------------------
|
|   Render autocomplete suggestbox with jquery
|
*/
Form::macro('suggestBox', function($name, $option, $value=null, $attributes=array(), $semi = false) {

    // $mode = $semi ? 'suggest-semi' : 'suggest';
    $mode = "suggest";

    $jsmode = str_replace('-', '_', $mode);

    Theme::asset()->container('macro')->usePath()->add('jqueryui_autocomplate_combobox', 'admin/js/jqueryui.autocomplete.combobox.js', array('jquery'));
    Theme::asset()->container('macro')->writeScript('jqueryui_'.$mode.'box_init', "
        var {$jsmode}box = $( '.{$mode}box' ).combobox({mode: '$mode'});
        ", array('jqueryui_autocomplate_combobox'));

    if(empty($attributes['class'])) {
        $attributes['class'] = $mode.'box';
    } else {
        $attributes['class'] .= ' '.$mode.'box';
    }

    Theme::asset()->writeStyle('combobox', '
    .ui-menu-item:hover {
        background: rgba(0, 0, 0, 0.1);
    }
    .input-append .add-on {
        -webkit-border-radius: 0 4px 4px 0;
        -moz-border-radius: 0 4px 4px 0;
        border-radius: 0 4px 4px 0;
    }
    .custom-combobox {
        margin: 0px;
    }
    ', array('bootstrap'));

    return Form::select($name, $option, $value, $attributes);
});

/*
|--------------------------------------------------------------------------
| Form Tag with Autocompletion SuggestBox
|--------------------------------------------------------------------------
|
|   Render Tag with autocomplete suggestbox with jquery
|
*/

Form::macro('tagBox', function($name, $value=null, $attributes=array() , $sampleTags= null) {
$themeAsset = Theme::asset();
$themeAsset->add('tagit_css', 'css/jquery.tagit.css');
$themeAsset->add('tagit_ui', 'css/tagit.ui-zendesk.css');
$themeAsset->container('macro')->add('tag-it', 'js/tag-it.js');
$html = '' ;
$html .= '<div>';
$html .= Form::text($name, $value , $attributes = array('id'=>$name));
$html .= '</div>';
Theme::asset()->container('macro')->writeScript(
    'tagit-js',
    '   $(document).ready(function() {
            var sampleTags = '.$sampleTags.';
            $(\'#'.$name.'\').tagit({
                availableTags: sampleTags ,
                caseSensitive: true,
                allowSpaces: true
            });
    }); '
    );
 return $html;
});

/*
|--------------------------------------------------------------------------
| Collection Hierarchy Checkbox
|--------------------------------------------------------------------------
|
|   Render Collection Hierarchy with Checkbox
|
*/
Form::macro('collectionCheckbox', function($collections = array(), $checkedValue = array())
{
    $html = '';

    if (!empty($collections))
    {
        $html .= '<ul class="collection-checkbox">';

        foreach ($collections as $key=>$collection)
        {
            if ($collection->collection_type == 'best_seller')
            {
                continue;
            }

            $html .= "<li>";

            if (in_array($collection->id, $checkedValue))
            {
                // $html .= Form::checkbox('collection[]', $collection->id, TRUE);
                $html .= "<input id=\"ccb{$collection->id}\" name=\"collection[]\" type=\"checkbox\" value=\"{$collection->id}\" checked=\"checked\">";
            }
            else
            {
                // $html .= Form::checkbox('collection[]', $collection->id, FALSE);
                $html .= "<input id=\"ccb{$collection->id}\" name=\"collection[]\" type=\"checkbox\" value=\"{$collection->id}\">";
            }

            $html .= " <label class=\"ccb\" for=\"ccb{$collection->id}\">{$collection->name}</label>";

            if (!empty($collection['children']))
            {
                $html .= Form::collectionCheckbox($collection['children'], $checkedValue);
            }

            $html .= '</li>';
        }

        $html .= '</ul>';
    }

    return $html;
});


/*
|--------------------------------------------------------------------------
| Select advance version
|--------------------------------------------------------------------------
|
|   Select that can attach data-html to option for js use later
|
*/
Form::macro('selectAdvance', function($name, $list = array(), $selected = null, $options = array())
{
    // When building a select box the "value" attribute is really the selected one
    // so we will use that when checking the model or session for a value which
    // should provide a convenient method of re-populating the forms on post.
    $selected = Form::getValueAttribute($name, $selected);

    $options['id'] = Form::getIdAttribute($name, $options);

    $options['name'] = $name;

    // We will simply loop through the options and build an HTML value for each of
    // them until we have an array of HTML declarations. Then we will join them
    // all together into one single HTML element that can be put on the form.
    $html = array();

    foreach ($list as $value => $optionAttribute)
    {
        // check option is selected or not
        if (is_array($selected))
        {
            $optionSelected = in_array($value, $selected) ? 'selected' : null;
        }
        else
        {
            $optionSelected = ((string) $value == (string) $selected) ? 'selected' : null;
        }

        $attributes = array('value' => e($value), 'selected' => $optionSelected);
        $text = $optionAttribute['text'];
        unset($optionAttribute['text']);
        $attributes += $optionAttribute;

        $attributes = HTML::attributes($attributes);

        $html[] = "<option{$attributes}>{$text}</option>";
        //$this->getSelectOption($display, $value, $selected);
    }

    // Once we have all of this HTML, we can join this into a single element after
    // formatting the attributes into an HTML "attributes" string, then we will
    // build out a final select statement, which will contain all the values.
    $options = HTML::attributes($options);

    $list = implode('', $html);

    return "<select{$options}>{$list}</select>";
});


/*
|--------------------------------------------------------------------------
| Form Autocompletion ComboBox + Select advance
|--------------------------------------------------------------------------
|
|   Render autocomplete combobox with jquery with select advance
|
*/
Form::macro('comboBoxAdvance', function($name, $option, $value=null, $attributes=array(), $semi = false) {

    $mode = "select";
    $jsmode = str_replace('-', '_', $mode);

    Theme::asset()->container('macro')->usePath()->add('jqueryui_autocomplate_combobox', 'admin/js/jqueryui.autocomplete.combobox.js', array('jquery'));
    Theme::asset()->container('macro')->writeScript('jqueryui_'.$mode.'box_init', "
        var {$jsmode}box = $( '.{$mode}box' ).combobox({mode: '$mode'});
        ", array('jqueryui_autocomplate_combobox'));

    if(empty($attributes['class'])) {
        $attributes['class'] = $mode.'box';
    } else {
        $attributes['class'] .= ' '.$mode.'box';
    }

    Theme::asset()->writeStyle('comboBox', '
    .ui-menu-item:hover {
        background: rgba(0, 0, 0, 0.1);
    }
    .input-append .add-on {
        -webkit-border-radius: 0 4px 4px 0;
        -moz-border-radius: 0 4px 4px 0;
        border-radius: 0 4px 4px 0;
    }
    .custom-combobox {
        margin: 0px;
    }
    ', array('bootstrap'));

    return Form::selectAdvance($name, $option, $value, $attributes);
});