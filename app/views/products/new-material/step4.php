<?php echo Form::open(array('class' => 'mws-form wzd-default', 'files' => true)); ?>

<div class="mws-form-row">
    <div class="mws-form-cols" style="margin-bottom: 15px;">
        <?php if ($product->productImage): ?>
        <div class="mws-form-col-1-8">
            <img src='<?php echo $product->productImage; ?>' style='margin-right:20px; width: 80px;'>

        </div>
        <?php endif; ?>

        <div class="mws-form-col-2-8">
            <b>Product:</b>
            <?php echo $product->title; ?>
            <br>
            <b>Product Line:</b>
            <?php echo $product->product_line; ?>
        </div>
        <div class="mws-form-col-2-8">
            <b>Brand:</b>
            <?php echo $product->brand->name; ?>
        </div>
    </div>

</div>
<?php
    $variantOldOption = Input::old('variant_option');
    $types = $product->styleTypes->lists('id');
?>
<?php if( false &&! $variantOldOption): $totalVariant = $product->variants->count(); ?>
    <div class="pull-right show_all" style="margin:0px 5px 5px 0px;">
        <?php echo $totalVariant; ?> existing variant<?php echo $totalVariant > 1 ? 's are' : ' is'; ?> hidden &nbsp;
        <a style="cursor: pointer; color: blue;" onclick="$('tr.old_variant').show(); $('tr.old_variant select').prop('disabled', false); $(this).parent('div').hide();">show</a>
    </div>
<?php endif; ?>
<?php
// dummy combobox - hack for custom select can show as combobox
echo "<div style='display:none;'>".Form::comboBox("hidden", array(0 => 0), null, array("disabled" => "disabled"))."</div>";
?>
<table class="mws-table mws-datatable-fn multiple-selectable" id="datatables_index" ng-app>
    <thead style="border-top: 1px solid rgb(204, 204, 204);">
        <tr>
            <th width="100%">Material Title</th>
            <th>Retail Price</th>
            <?php foreach($product->styleTypes as $index => $styleType): ?>
            <th>
                <?php echo $styleType->name; ?><br>
                <a href="<?php echo URL::action("ProductNewMaterialController@getCreateStyleOption", array($styleType->getKey())); ?>" class="btn btn-mini various-large fancybox.iframe" style="margin-top: 5px; font-size: 12px;"><?php echo __('+ Create new'); ?></a>
            </th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody ng-controller="StyleOptionController" ng-init="styleOptions = []">
        <tr>
            <td colspan="2">&nbsp;</td>
            <?php foreach($product->styleTypes as $index => $styleType): ?>
                <td data-style-type="<?php echo $styleType->id ?>">

                    <div class="style_option_detail" ng-repeat="styleOption in styleOptions.<?php echo $styleType->id ?>" style="display:none;">

                        <div class="style_option_meta_value">
                            <div ng-show="styleOption.meta.type=='color'" style="width: 20px; height: 20px; background-color:{{ styleOption.meta.value }};">
                            </div>
                            <img ng-show="styleOption.meta.type=='image'" src="{{ styleOption.meta.value }}">
                            <span ng-show="styleOption.meta.type=='text'" style="padding-left: 4px; padding-right: 4px;">
                                {{ styleOption.meta.value }}
                            </span>
                        </div>
                        <span class="style_option_text">{{ styleOption.text }}</span>
                        <a href="{{ styleOption.iframe }}" ng-class="{'btn-warning': ! styleOption.meta.type}" class="btn btn-mini various-large fancybox.iframe edit_style_option"><i class="icon-edit"></i></a>
                    </div>
                </td>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($materials as $key => $material): ?>
        <tr style="text-align: center;">
            <td><?php echo $material->name; ?>
                <?php if (! empty($material->color)) { echo " (Color: {$material->color})"; } ?>
                <?php if (! empty($material->size)) { echo " (Size: {$material->size})"; } ?>
                <?php if (! empty($material->surface)) { echo " (Surface: {$material->surface})"; } ?>
            </td>
            <td><?php echo number_format($material->cost_rtp, 0); ?></td>

            <?php foreach($product->styleTypes as $index => $styleType): ?>
                <td>
                    <?php
                        $value = null;
                        if ($styleType->core)
                        {
                            $field = "{$styleType->core}_id";
                            $value = $material->$field;
                        }
                        $selectValue = $value ?: null;
                        $selectName = "material_option[{$material->getKey()}][{$styleType->getKey()}]";
                        $selectAttributes = array(
                            "style"    => "width: 120px;",
                            "class"    => "selectbox select_style_option select_style_type_{$styleType->getKey()}",  // add class for select can be combobox
                            // "ng-model" => "selected.{$styleType->getKey()}.{$material->getKey()}",
                            "data-style-type" => $styleType->getKey(),
                        );

                        echo Form::selectAdvance($selectName, $selectListStyleOptions[$styleType->getKey()], $selectValue, $selectAttributes);

                    ?>
                </td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>


        <?php foreach ($product->variants as $key => $variant): ?>
        <tr class="old_variant" style="
                text-align: center;
                background: #<?php echo $key%2==0 ? 'd2e2f2' : 'daeafa'; ?>;
            " data-select-name="variant_option[<?php echo $variant->getKey(); ?>][]">
            <td><?php echo $variant->title; ?></td>
            <td><?php echo number_format($variant->retail_price, 2); ?></td>
            <?php foreach($product->styleTypes as $index => $styleType): ?>
                <td>
                    <?php
                        // get style option of current style type
                        $variantStyleOption = $variant->variantStyleOption->filter(function($item) use ($styleType) {
                            return $item->style_type_id == $styleType->getKey() ? $item : false;
                        })->first();

                        // get style option from variant
                        $selectValue = $variantStyleOption ? $variantStyleOption->styleOption->getKey() : null;

                        $selectName = "variant_option[{$variant->getKey()}][{$styleType->getKey()}]";
                        $selectAttributes = array(
                            "style"    => "width: 120px;",
                            "class"    => "selectbox select_style_option select_style_type_{$styleType->getKey()}",  // add class for select can be combobox
                            // "ng-model" => "selected.{$styleType->getKey()}.{$variant->getKey()}",
                            "data-style-type" => $styleType->getKey(),
                        );

                        echo Form::selectAdvance($selectName, $selectListStyleOptions[$styleType->getKey()], $selectValue, $selectAttributes);
                    ?>
                </td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>


    </tbody>
</table>

<div class="mws-button-row" style="height: 30px;">
    <div class="label label-warning" id="alert-style-options-meta" style="display:none;">* You have new data that need to fill information</div>
    <input type="submit" class="btn btn-primary pull-right" id="save_submit" name="submit" value="Next" />
    <input type="submit" style="display:none;" class="btn btn-primary pull-right" id="refresh" name="refresh" value="Refresh" />
</div
<?php echo Form::close(); ?>

<?php

Theme::asset()->serve('angular');
Theme::asset()->script('js-step4-angular', '/js/step4-angular.js');
Theme::asset()->serve('fancybox');
Theme::asset()->writeStyle('css_style_option', '
.style_option_meta_value {
    padding: 1px 2px 3px 2px;
    height: 20px;
    width: 20px;
    float: left;
    border: 1px solid #c5c5c5;
    margin: 2px 2px 2px 2px;
}
.style_option_text {
float: left;
margin-top: 5px;
margin-left: 5px;
}
.style_option_detail a{
    margin-top: 3px;
    float: right;
}
.style_option_detail
{
    overflow: hidden;
    clear: both;
}

.new_style_option_dialog .fileinput-holder
{
    width: 180px;
    display: inline-block;
}

.new_style_option_dialog .option_type_radio
{
    width: 70px;
    display: inline-block;
    line-height: 28px;
    margin-bottom: 8px;
}
.mws-form input[disabled].btn-primary
{
    background: rgb(223, 223, 223);
    color: rgb(158, 158, 158);
    border-color: rgb(158, 158, 158);
}
', array('wizard'));

Theme::asset()->writeStyle("main", "
.input-prepend input:focus
{
    z-index: auto;
}
", array("style-pcms"));
