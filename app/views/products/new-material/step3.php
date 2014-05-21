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

    <div class="mws-panel grid_8">
        <div class="mws-panel-header">
            <span><i class="icon-table"></i> Style Types</span>
        </div>
        <div class="mws-panel-toolbar">
            <div class="btn-toolbar">
                <div class="btn-group">
                    <a class="btn add-type"><i class="icol-add"></i> Add style type</a>
                </div>
            </div>
        </div>
        <div class="mws-panel-body no-padding">
            <table class="mws-table">
                <thead>
                    <tr>
                        <th>Style</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="style_type_list">
                    <?php if ($product->styleTypes->count() > 0): ?>
                        <?php foreach($product->styleTypes as $styleType): ?>
                        <tr data-id="<?php echo $styleType->getKey(); ?>">
                            <td>
                                <?php echo $styleType->name; ?>
                            </td>
                            <td>
                                <?php
                                    $delete = array(
                                        'product' => $product->getKey(),
                                        'style_type' => $styleType->getKey(),
                                    );
                                    $attributes = array(
                                        'class' => 'btn edit_style',
                                        'type'  => 'button',
                                        'data-target' => "edit_style_type_{$styleType->getKey()}_dialog"
                                    );
                                    echo Form::button("Edit", $attributes);
                                ?>

                                <div id="edit_style_type_<?php echo $styleType->getKey(); ?>_dialog" class="edit_style_type" style="display:none;">
                                    <div id="edit_style_type_<?php echo $styleType->getKey(); ?>_form" class="mws-form">
                                        <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
                                        <div class="mws-form-inline">
                                            <div class="mws-form-row">
                                                <label class="mws-form-label">Name</label>
                                                <div class="mws-form-item">
                                                    <?php echo Form::hidden("style_type_id", $styleType->getKey()); ?>
                                                    <?php echo Form::text("name", $styleType->name); ?>
                                                    <?php echo Form::transText($styleType, "name"); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                    $attributes = array(
                                        'class' => 'btn',
                                        'type'  => 'submit',
                                        'name'  => 'detach_style_type',
                                        'value' => $styleType->getKey()
                                    );
                                    echo Form::button("Unlink", $attributes);
                                ?>


                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">
                                <?php echo __("No style type for this product. Please create new."); ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <tr data-type="select-prototype" class="hide">
                        <td>
                            <?php echo Form::select('selectType[]', $selectStyleType, null, array("class" => "select-type")); ?>
                        </td>
                        <td>
                            <?php
                                $attributes = array(
                                    'class' => 'btn remove_tr',
                                    'type'  => 'button'
                                );
                                echo Form::button("Remove", $attributes);
                            ?>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

</div>


<div id="new_style_type_dialog" style="display:none;">
    <div id="new_style_type_form" class="mws-form">
        <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
        <div class="mws-form-inline">
            <div class="mws-form-row">
                <label class="mws-form-label">Name</label>
                <div class="mws-form-item">
                    <?php echo Form::text("new_style_type", null); ?>
                    <?php echo Form::transText(null, 'name'); ?>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="mws-button-row" style="height: 30px;">
    <?php echo Form::submit('Next', array('class' => 'btn btn-primary pull-right')); ?>
</div>

<?php echo Form::close(); ?>


<?php

$keys = array_keys(Input::old('selectType', array('0' => '')));
$baseCounter = max($keys);
Theme::asset()->container('footer')->writeScript('wizard-form-style-type', '

    // create form and submit
    function callPostForm(element, submitName, submitValue)
    {
        var submitInput = "<input type=\'hidden\' name=\'" + submitName + "\' value=\'" + submitValue + "\'>";
        element.wrap("<form id=\'edit_style_js_form\' method=\'post\'></form>");
        element.append(submitInput);
        $("#edit_style_js_form").submit();
    }

    ;(function( $, window, document, undefined ) {
        $(document).ready(function() {

            // start edit modal
            $(".edit_style_type").dialog({
                autoOpen: false,
                title: "Edit style type",
                modal: true,
                width: "520",
                height: "360",
                buttons: [{
                    text: "Save",
                    name: "edit_style_type",
                    value: "true",
                    class: "call_post_form",
                    click: function () {}
                }]
            });

            // bind edit button to open edit modal
            $("button.edit_style").click(function(e){
                var target = $(this).attr("data-target");
                $("#" + target).dialog("option", {
                    modal: true,
                }).dialog("open");
            });

            // start create modal
            $("#new_style_type_dialog").dialog({
                autoOpen: false,
                title: "Create style type",
                modal: true,
                width: "520",
                height: "360",
                buttons: [{
                    text: "Create",
                    name: "create_new_style_type",
                    value: "true",
                    class: "call_post_form",
                    click: function () {}
                }]
            });

            $("button.call_post_form").click(function(e){
                var parent = $(this).closest("div.ui-dialog");
                var name = $(this).attr("name");
                var value = $(this).attr("value");
                callPostForm(parent, name, value);
            });

            var counter = ('.$baseCounter.')+1;

            var event_remove_tr = function(event){
                $(this).closest("tr").remove();
            };

            var event_select_change = function(e){
                var select = $(this);
                if (select.val() == "add")
                {
                    $("#new_style_type_dialog").dialog("option", {
                        modal: true,
                        close: function( event, ui ) {
                            select.val("0");
                        }
                    }).dialog("open");
                    event.preventDefault();
                }
            };

            $("a.add-type").click(function(event){

                var prototype = $("tr[data-type=\'select-prototype\']");

                prototype.before("<tr>" + prototype.html() + "</tr>");

                $("button.remove_tr").click(event_remove_tr);
                $("select.select-type").change(event_select_change);

                counter++;
            });

            $("select.select-type").change(event_select_change);
            $("button.remove_tr").click(event_remove_tr);

            // protect enter keyboard for submit
            // $(window).keydown(function(event){
            //     if(event.keyCode == 13) {
            //         event.preventDefault();
            //         return false;
            //     }
            // });
        });
    }) (jQuery, window, document);
    ', array('jquery-form'));

Theme::asset()->writeStyle("main", "
.input-prepend input:focus
{
    z-index: auto;
}
", array("style-pcms"));

?>