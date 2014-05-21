<?php

Theme::asset()->add('bulk-select', URL::to('js/bulkselect-table.js'));
Theme::asset()->container('footer')->writeScript('bulk-select-script', '
    $(".multiple-selectable").bulkSelectTable();
');

?>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Product in Collection - <?php echo $collection->name ?></span>
    </div>

	<?php // echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>

    <div class="mws-panel-body no-padding dataTables_wrapper">

    	<?php echo HTML::message() ?>

        <form method="get" class="mws-form" action="<?php echo URL::to('products/collection/insert') ?>">
            <table class="mws-datatable-fn mws-table multiple-selectable">
                <thead>
                    <tr>
                        <!--<th style="width:25px;">&nbsp;</th>-->
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
    					<tr>
                            <!--<td class="table-center"><input type="checkbox" value="<?php // echo $product->id ?>" class="selectable" name="product[]"></td>-->
                            <td>
                            	<?php if (!empty($productImage)) { ?>
                            		<img src="<?php echo $productImage ?>" style="margin-right:20px;">
                            	<?php } ?>
                            	<?php echo $product->title ?>
                            </td>
                            <td><?php echo $collectionList ?></td>
                            <td class="table-center"><a href="<?php echo URL::to("products/collection/set/{$product->id}?return-collection={$collection->id}") ?>" class="btn btn-info btn-small">Edit Collections</a></td>
                        </tr>
                  	<?php } ?>
                </tbody>
            </table>



            <div class="mws-button-row">
                <input type="hidden" name="collection_id" value="<?php echo $collection->getKey(); ?>" class="btn btn-primary">
                <input type="submit" value="Insert Product to Collection" class="btn btn-primary">
                |
                <?php if (!empty($collection->bestSeller)) { ?>
                    <a href="<?php echo URL::to("collections/best-seller/{$collection->bestSeller->id}/{$collection->id}") ?>">
                    Manage Best Seller
                    </a>
                <?php } else { ?>
                    <a href="<?php echo URL::to("collections/best-seller/0/{$collection->id}") ?>">
                    Manage Best Seller
                    </a>
                <?php } ?>
            </div>
            <?php
                echo $products->links();
            ?>
        </form>
    </div>
</div>

