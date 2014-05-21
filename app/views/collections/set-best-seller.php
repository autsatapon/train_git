<?php

Theme::asset()->add('bulk-select', URL::to('js/bulkselect-table.js'));
Theme::asset()->container('footer')->writeScript('bulk-select-script', '
    $(".multiple-selectable").bulkSelectTable();
');

?>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Best Seller Products in Collection - <?php echo $collection->name ?></span>
    </div>
    

	<?php // echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>
     <?php $firstsegment = Request::segment(3); ?> <!-- Parent_id -->
    <?php $secsegment = Request::segment(4); ?>
    <div class="mws-panel-body no-padding">

    	<?php echo HTML::message() ?>

        <form method="post" class="mws-form" action="<?php echo URL::to("collections/insert-best-seller/{$firstsegment}/{$secsegment}") ?>">
            <table class="mws-datatable-fn mws-table multiple-selectable">
                <thead>
                    <tr>
                        <th style="width:25px;">&nbsp;</th>
    					<th><?php echo Product::getLabel('title') ?></th>
    					<th><?php echo Collection::getLabel('name') ?></th>
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
                                <td class="table-center"><input type="checkbox" value="<?php echo $product->id ?>" class="selectable" name="product[]"></td>
                                <td>
                                	<?php if (!empty($productImage)) { ?>
                                		<img src="<?php echo $productImage ?>" style="margin-right:20px;">
                                	<?php } ?>
                                	<?php echo $product->title ?>
                                </td>
                                <td><?php echo $collectionList ?></td>
                            </tr>
                      	<?php } ?>
                    <?php } ?>
                </tbody>
            </table>

            <div class="mws-button-row">
                <input type="submit" value="Set Products as Best Seller" class="btn btn-primary">
            </div>

        </form>
    </div>
</div>

