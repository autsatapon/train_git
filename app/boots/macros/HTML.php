<?php

/*
|--------------------------------------------------------------------------
| HTML Label
|--------------------------------------------------------------------------
|
|	Render bootstrap label
|
*/
HTML::macro('label', function($text, $class = '')
{
	if (in_array($class, array('success', 'warning', 'important', 'info', 'inverse')))
	{
		$class = ' label-'.$class;
	}
	else
	{
		$class = '';
	}

	return '<span class="label'.$class.'">'.$text.'</span>';
});

/*
|--------------------------------------------------------------------------
| HTML Pagination Info
|--------------------------------------------------------------------------
|
|	Render pagination information
|	Showing 1 to 10 of 99 entries
|
*/
HTML::macro('pageInfo', function($model)
{
	$start = ($model->getCurrentPage() - 1) * $model->getPerPage() + 1;

	$end = $start + $model->getPerPage() - 1;

	$total = $model->getTotal();

	if ($end >= $total) $end = $total;

	return 'Showing '.$start.' to '.$end.' of '.$total.' entries';
});

/*
|--------------------------------------------------------------------------
| Merge html two attributes into one
|--------------------------------------------------------------------------
*/
HTML::macro('mergeAttributes', function($array1, $array2)
{
    if(!is_array($array1) || !is_array($array2)) return FALSE;

    foreach ($array2 as $attr => $value) {
        if(!empty($array1[$attr])) {
            if(in_array($attr, array('style', 'class'))) {
                $array1[$attr] .= ' '.$value;
                continue;
            }
        }
        $array1[$attr] = $value;
    }

    return $array1;
});

/*
|--------------------------------------------------------------------------
| Button as link
|--------------------------------------------------------------------------
|
|   Render button as link
|
*/
HTML::macro('buttonLink', function($label, $link = "", $type = "", $target="", $extras=array())
{
    if(empty($link)) $link = "#";
    if(empty($target)) $target = "_self";

    $attributes = array(
        'href' => $link,
        'target' => $target
    );

    if($link == '#') unset($attributes['href']);

    switch ($type) {
        case 'edit':
            $attributes['class'] = 'btn btn-warning';
            $text = '<i class="icon-edit"></i> %s';
            break;

        case 'delete':
            $attributes['class'] = 'btn btn-danger action-delete';
            $text = '<i class="icon-remove"></i> %s';
            break;

        case 'view':
            $attributes['class'] = 'btn btn-primary';
            $text = '<i class="icon-eye-open"></i> %s';
            break;

        case 'open':
            $attributes['class'] = 'btn btn-info';
            $text = '<i class="icon-folder-closed"></i> %s';
            break;

        case 'yes':
            $attributes['class'] = 'btn btn-success';
            $text = '<i class="icon-ok-sign"></i> %s';
            break;

        case 'no':
            $attributes['class'] = 'btn btn-inverse';
            $text = '<i class="icon-remove-sign"></i> %s';
            break;

        default:
            $attributes['class'] = 'btn';
            $text = '%s';
            break;
    }

    $attributes = HTML::mergeAttributes($attributes, $extras);

    $html = "<a".HTML::attributes($attributes).">".$text."</a>";

    return sprintf($html, $label);
});

HTML::macro('message', function()
{
    $states = array('success', 'info', 'warning', 'errors', 'danger');

    $html = array();

    foreach ($states as $state)
    {
        if (Session::has($state))
        {
            $type = Str::singular($state);

            $html[] = '<div class="alert alert-'.$type.'">';

            $messages = (Session::get($state) instanceof \Illuminate\Support\MessageBag) ?
                        Session::get($state)->all() :
                        Session::get($state);

            if ( ! is_array($messages))
            {
                $messages = array($messages);
            }

            foreach ($messages as $message)
            {
                $html[] = '<p>'.$message.'</p>';
            }
            $html[] = '</div>';
        }
    }

    return implode("\n", $html);
});
