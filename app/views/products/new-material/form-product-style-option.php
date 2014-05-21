<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><?php echo $styleOption ? __("Edit style option of product.") : __("Create style option"); ?></span>
    </div>

    <?php echo Form::open(array('class' => 'mws-form wzd-default', 'files' => true)); ?>
        <div class="mws-panel-body no-padding">
            <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
            <?php echo HTML::message(); ?>
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label">Product</label>
                    <div class="mws-form-item" style="padding-top: 5px;">
                        <?php echo $product->title; ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label">Style type</label>
                    <div class="mws-form-item" style="padding-top: 5px;">
                        <?php echo $styleType->name; ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label">Text</label>
                    <div class="mws-form-item">
                        <?php // echo Form::hidden("new_style_option[style_type_id]", null, array("class" => "style_type_id")); ?>
                        <?php $styleOptionID = (get_class($styleOption) == 'ProductStyleOption') ? $styleOption->style_option_id : $styleOption->getKey(); ?>
                        <?php echo Form::text("text", @$styleOption->text); ?> <a href="<?php echo URL::action("ProductNewMaterialController@getEditStyleOption", array($styleType->getKey(), $styleOptionID)); ?>">Edit master</a>
                        <?php echo Form::transText($styleOption, 'text'); ?>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label">Type</label>
                    <div class="mws-form-item">
                        <?php
                            echo "<span class='option_type_radio'>", Form::radio("meta_type", "color", @$styleOption->meta["type"] == "color");
                            echo " ", __("Color"), "</span> ";
                            echo Form::text("meta_color", @$styleOption->meta["type"] == "color" ? @$styleOption->meta['value'] : null);
                        ?><br>
                        <?php
                            echo "<span class='option_type_radio'>", Form::radio("meta_type", "image", @$styleOption->meta["type"] == "image");
                            echo " ", __("Image"), "</span> ";
                            if (@$styleOption->meta["type"] == "image")
                            {
                                echo "<img src='{$styleOption->image}' class='option_img_preview'> ";
                            }
                            echo Form::file("meta_image");
                        ?><br>
                        <?php
                            echo "<span class='option_type_radio'>", Form::radio("meta_type", "text", @$styleOption->meta["type"] == "text");
                            echo " ", __("Text"), "</span> ";
                            echo Form::text("meta_text", @$styleOption->meta["type"] == "text" ? @$styleOption->meta['value'] : null);
                        ?>
                    </div>
                </div>
            </div>
        </div>


        <div class="mws-button-row">
            <input type="submit" class="btn btn-primary" value="Save">
        </div>
    <?php echo Form::close(); ?>

</div>

<?php
Theme::asset()->writeStyle('css_style_option', '
.fileinput-holder
{
    width: 180px;
    display: inline-block;
}

.option_type_radio
{
    width: 70px;
    display: inline-block;
    line-height: 28px;
    margin-bottom: 8px;
}
.option_img_preview
{
    padding: 2px 2px 2px 2px;
    border: 1px solid #c5c5c5;
    margin: 2px 2px 8px 2px;
    height: 50px;
    width: 50px;
}
', array('wizard'));