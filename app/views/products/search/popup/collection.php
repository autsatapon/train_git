<div class="mws-panel grid_8 popup-manage-items-wrap" style="box-shadow:none;" id="popup-manage-items-collection">

    <div class="mws-panel-header">
        <span>Collection</span>
    </div>

    <div class="mws-panel-body no-padding">

        <?php //echo HTML::message() ?>

        <?php //echo Form::open(array('url' => URL::current().'?return-collection='.Input::get('return-collection'), 'class' => 'mws-form', 'files' => true)); ?>
        <div class="mws-form">

            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label"><strong>Collection</strong></label>
                    <div class="mws-form-item">
                        <?php //echo Form::collectionCheckbox($collections, $product->collections->lists('id')) ?>
<?php

function collectionCheckboxWithData($collections, $checkedValue)
{
    $html = "";

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

            $html .= Form::checkbox('collection[]', $collection->id, in_array($collection->id, $checkedValue),
                array(
                    'id' => "ccb{$collection->id}",
                    'data-data' => json_encode(array('id' => $collection->getKey(), 'pkey' => $collection->pkey, 'title' => $collection->name))
                ));

            // $checked = in_array($collection->id, $checkedValue) ? ' checked="checked"' : '';
            // $json = json_encode(array('id' => $collection->getKey(), 'pkey' => $collection->pkey, 'title' => $collection->name));
            // $html .= "<input id=\"ccb{$collection->id}\" name=\"collection[]\" type=\"checkbox\" value=\"{$collection->id}\" {$checked} data-data=\"{$json}\">";

            // if (in_array($collection->id, $checkedValue))
            // {
            //     // $html .= Form::checkbox('collection[]', $collection->id, TRUE);
            //     $html .= "<input id=\"ccb{$collection->id}\" name=\"collection[]\" type=\"checkbox\" value=\"{$collection->id}\" checked=\"checked\">";
            // }
            // else
            // {
            //     // $html .= Form::checkbox('collection[]', $collection->id, FALSE);
            //     $html .= "<input id=\"ccb{$collection->id}\" name=\"collection[]\" type=\"checkbox\" value=\"{$collection->id}\">";
            // }

            $html .= " <label class=\"ccb\" for=\"ccb{$collection->id}\">{$collection->name}</label>";

            if (!empty($collection['children']))
            {
                $html .= collectionCheckboxWithData($collection['children'], $checkedValue);
            }

            $html .= '</li>';
        }

        $html .= '</ul>';
    }

    return $html;
}

echo collectionCheckboxWithData($collections, array());

?>
                    </div>
                </div>
            </div>

            <div class="mws-button-row panel-add">
                <input type="submit" class="btn btn-primary add" value="Add">
            </div>
<!--
        </form>
        -->
        </div>

    </div>

</div>


<?php


// s(Input::get());

?>