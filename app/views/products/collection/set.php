<div class="mws-panel grid_8" style="box-shadow:none;">

    <div class="mws-panel-header">
        <span>Set Product Collection</span>
    </div>

    <div class="mws-panel-body no-padding">

		<?php echo HTML::message() ?>

        <?php echo Form::open(array('url' => URL::current().'?return-collection='.Input::get('return-collection'), 'class' => 'mws-form', 'files' => true)); ?>

            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label"><strong><?php echo Product::getLabel('title') ?></strong></label>
                    <div class="mws-form-item">
                        <label class="mws-form-label" style="display:block; width:auto;"><strong><?php echo $product->title ?></strong></label>
                    </div>
                </div>
            </div>

            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label"><strong><?php echo Collection::getLabel('name') ?></strong></label>
                    <div class="mws-form-item">
                        <?php echo Form::collectionCheckbox($rootCollections, $product->collections->lists('id')) ?>
                    </div>
                </div>
            </div>

            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
                <input type="button" class="btn" value="Cancel" onClick="history.back();">
            </div>

        </form>

    </div>

</div>

<?php

Theme::asset()->container('footer')->writeScript('autocheck-ancestor','
$(document).on("change",".collection-checkbox input[type=checkbox]",function(e){
    var o = $(this),
        p = o.parents("li");
    if(o.is(":checked")) {
        p.find("input[type=checkbox]:first").prop("checked","checked");
    }
})
');