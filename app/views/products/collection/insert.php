<div class="mws-panel grid_8" style="box-shadow:none;">

    <div class="mws-panel-header">
        <span>Insert Product to Collection</span>
    </div>

    <div class="mws-panel-body no-padding">

		<?php echo HTML::message() ?>

        <form method="post" action="" class="mws-form" enctype="multipart/form-data">
            
            <div class="clear" style="height:20px;">&nbsp;</div>

            <div class="mws-panel grid_3">
                <div class="mws-panel-header">
                    <span>Product</span>
                </div>
                <div class="mws-panel-body no-padding">
                    <div class="mws-form">
                        <div class="mws-form-row">
                            <label class="mws-form-label"><strong><?php echo Product::getLabel('title') ?></strong></label>
                            <div class="mws-form-item">
                                
                                <input type="hidden" id="product-collections-insert" name="">
                                <input type="button" id="effects-discount-following_items-popup" class="popup-manage-items btn btn-default" value="Browse"
                                       data-popup-id="product-collections-insert"
                                       data-popup-type="product"
                                       data-popup-target-id="product-collections-insert"
                                       data-popup-pkeys='[]'
                                       data-popup-datas='[]'
                                       data-popup-li-template='<li data-pkey="{pkey}"><input type="checkbox" name="product[]" value="{id}" id="pcb{id}" checked> {title}</li>'
                                       />
                                
                                <?php foreach ($products as $key=>$product) { ?>
                                    <p>
                                        <input type="checkbox" name="product[]" value="<?php echo $product->id ?>" id="pcb<?php echo $product->id ?>" <?php if ($checkedAll) { echo 'checked="checked"'; } ?>>
                                        <label class="ccb" for="pcb<?php echo $product->id ?>">
                                            <?php echo $product->title ?>
                                        </label>
                                    </p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mws-panel grid_5">
                <div class="mws-panel-header">
                    <span>Collection</span>
                </div>
                <div class="mws-panel-body no-padding">
                    <div class="mws-form">
                        <div class="mws-form-row">
                            <label class="mws-form-label"><strong><?php echo Collection::getLabel('name') ?></strong></label>
                            <div class="mws-form-item">
                                <?php echo Form::collectionCheckbox($rootCollections); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clear" style="height:20px;">&nbsp;</div>

            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
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
});
$("#ccb'.Input::get('collection_id').'").prop("checked", true).change();
');