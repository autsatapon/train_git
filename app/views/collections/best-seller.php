<?php

Theme::asset()->add('bulk-select', URL::to('js/bulkselect-table.js'));
Theme::asset()->container('footer')->writeScript('bulk-select-script', '
    $(".multiple-selectable").bulkSelectTable();
');

?>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span <i class="icon-table"></i> Best seller Products in Collection - <?php echo $collection->name ?></span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <?php $firstsegment = Request::segment(3); ?>
                <?php $secsegment = Request::segment(4); ?>
                <a href="<?php echo URL::to("collections/set-best-seller/{$secsegment}/{$firstsegment}") ?>" class="btn"><i class="icol-add"></i> Add Best Seller Products</a>
            </div>
        </div>
    </div>
    

	<?php // echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>

    <div class="mws-panel-body no-padding">

    	<?php echo HTML::message() ?>

            <table class="mws-datatable-fn mws-table multiple-selectable">
                <thead>
                    <tr>
    					<th><?php echo Product::getLabel('title') ?></th>
    					<th><?php echo Collection::getLabel('name') ?></th>
    				    <th style="width:120px;">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($products)){ ?>
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
                                <td>
                                	<?php if (!empty($productImage)) { ?>
                                		<img src="<?php echo $productImage ?>" style="margin-right:20px;">
                                	<?php } ?>
                                	<?php echo $product->title ?>
                                </td>
                                <td><?php echo $collectionList ?></td>
                                <?php $secsegment = Request::segment(4); ?>
                                <td class="table-center"><a href="<?php echo URL::to("collections/delete-best-seller/{$product->id}/{$firstsegment}/{$secsegment}") ?>">
                                    <input type="button" class="btn btn-danger" value="Delete" ></a>
                                </td>
                            </tr>
                      	<?php } ?>
                    <?php } ?>
                </tbody>
            </table>

    </div>
</div>

