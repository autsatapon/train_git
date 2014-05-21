<?php

Theme::asset()->add('bulk-select', URL::to('js/bulkselect-table.js'));
Theme::asset()->container('footer')->writeScript('bulk-select-script', '
    $(".multiple-selectable").bulkSelectTable();
');

?>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Manage Product's Collection</span>
    </div>

	<?php echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>
    
    <form method="get" action="<?php echo URL::to('products/collection/insert') ?>">

    <div class="mws-panel-body no-padding dataTables_wrapper">

    	<?php echo HTML::message() ?>

            <table class="mws-datatable-fn mws-table multiple-selectable">
                <thead>
                    <tr>
                        <th style="width:25px;">&nbsp;</th>
    					<th><?php echo Product::getLabel('title') ?></th>
    					<th><?php echo Collection::getLabel('name') ?></th>
    					<th style="width:120px;">Action</th>
                    </tr>
                </thead>
                <tbody>
    				<?php foreach ($products as $product) { ?>
                        <?php
                            $collectionList = '';
                            if ( !$product->collections->isEmpty() )
                            {
                                $collectionList = implode(', ', $product->collections()->rootCollection()->lists('name'));
                            }

                            $productImage = '';
                            $mediaImage = $product->mediaContents->first();
                            if ( !empty($mediaImage) )
                            {
                                $productImage = (string) UP::lookup($mediaImage->attachment_id)->scale('s');
                            }
                        ?>
    					<tr style="border-bottom:3px solid #000;">
                            <td class="table-center"><input type="checkbox" value="<?php echo $product->id ?>" class="selectable" name="product[]"></td>
                            <td>
                                <?php if (!empty($productImage)) { ?>
                                    <img src="<?php echo $productImage ?>" style="margin-right:20px;">
                                <?php } ?>
                                <?php echo $product->title ?>
                            </td>
                            <td><?php echo $collectionList ?></td>
                            <td class="table-center"><a href="<?php echo URL::to("products/collection/set/{$product->id}") ?>" class="btn btn-info btn-small">Edit Collections</a></td>
                        </tr>
                  	<?php } ?>
                </tbody>
            </table>

            <?php
                $query = array(
                    'product' => Input::get('product'),
                    'product_line' => Input::get('product_line'),
                    'brand' => Input::get('brand'),
                    'tag' => Input::get('tag'),
                    'has_product_content' => Input::get('has_product_content'),
                    'has_product_mediacontent' => Input::get('has_product_mediacontent'),
                    'product_allow_installment' => Input::get('product_allow_installment'),
                    'variant_allow_installment' => Input::get('variant_allow_installment'),
                    'product_allow_cod' => Input::get('product_allow_cod'),
                    'has_price' => Input::get('has_price'),
                    );
                echo $products->appends($query)->links();
            ?>


            <div class="mws-button-row" style="margin:10px;">
                <input type="submit" value="Insert Product to Collection" class="btn btn-primary">
            </div>

    </div>
</form>

</div>

