<?php echo HTML::message(); ?>

<div class="mws-panel grid_8">

    <div class="mws-panel-header">
        <span>
            <i class="icon-table"></i> <?php echo $discountCampaign->name; ?> (<?php echo $discountCampaign->code; ?>)
        </span>
    </div>
    
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group btn-collection-nav">
                <a class="btn" href="<?php echo URL::to('discount-campaigns/add-items/'.$discountCampaign->getKey()); ?>"><i class="icol-add"></i> Add items</a>
            </div>
        </div>
    </div>

    <div class="mws-panel-body no-padding">
        
        <?php echo Form::open(array('class' => 'mws-form')); ?>
        
        <div class="mws-button-row">
            <?php echo Form::submit('Save', array('class' => 'btn btn-primary')); ?>
            <a href="<?php echo URL::to('discount-campaigns'); ?>" class="btn btn-default">Cancel</a>
        </div>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th width="300px">Name</th>
                    <th><?php echo ProductVariant::getLabel('net_price'); ?></th>
                    <th><?php echo ProductVariant::getLabel('special_price'); ?></th>
                    <th>Discount</th>
                    <th>Price</th>
                    <!--<th>Start</th>-->
                    <!--<th>End</th>-->
                    <th width="40px"></th>
                </tr>
            </thead>
            <tbody>
                
                
                <?php if (count($newProducts)) foreach ($newProducts as $product): ?>
                <?php if ($product->variants->count() > 0): ?>
                <tr style="border-top:3px solid #4D4D4D;border-bottom:1px solid #4D4D4D;margin-top:10px;" class="new-added-product-id-<?php echo $product->getKey(); ?>">
                    <td colspan="7">
                        <h4><span class="label label-warning">New</span> <?php echo $product['title']; ?></h4>
                        <?php echo (! empty($product['image']) ? '<img src="'.$product['image'].'">' : null) ?>
                    </td>
                </tr>
                
                <?php foreach ($product->variants as $variant): ?>
                <tr class="new-added-variant new-added-variant-product-id-<?php echo $product->getKey(); ?>">
                    <td>
                        <?php echo $variant->title; ?>
                    </td>
                    <td class="table-center">
                        <?php echo $variant->net_price; ?>
                    </td>
                    <td class="table-center">
                        <?php echo ($variant->special_price=='0.00')?'-':$variant->special_price; ?>
                    </td>
                    <td>
                        <span class="pre_discount"></span>
                        <?php echo Form::text('added-variants['.$variant->getKey().'][discount]', $discountCampaign->discount, array('class' => 'discount', 'data-net_price' => $variant->net_price)); ?>
                        <?php echo Form::select('added-variants['.$variant->getKey().'][discount_type]', $discountOptions, $discountCampaign->discount_type, array('class' => 'discount_type')); ?>
                    </td>
                    <td>
                        
                    </td>
                    
                    <!--<td>-->
                        <?php echo Form::hidden('added-variants['.$variant->getKey().'][started_at]', $discountCampaign->started_at->format('Y-m-d'), array('class' => 'started_at')); ?>
                    <!--</td>-->
                    <!--<td>-->
                        <?php echo Form::hidden('added-variants['.$variant->getKey().'][ended_at]', $discountCampaign->ended_at->format('Y-m-d'), array('class' => 'ended_at')); ?>
                    <!--</td>-->
                    <td>
                        
                        <?php echo Form::hidden('added-variants['.$variant->getKey().'][net_price]', $variant->net_price); ?>
                        <?php echo Form::hidden('added-variants['.$variant->getKey().'][inventory_id]', $variant->inventory_id); ?>
                        
                        <a href="javascript: void();" class="btn btn-default remove" data-new-added-product-id="<?php echo $product->getKey(); ?>"><span class="icon icon-remove"></span></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php endif; ?>
                <?php endforeach; ?>
                
                
                <?php if (count($products)) foreach ($products as $product): ?>
                <tr style="border-top:3px solid #4D4D4D;border-bottom:1px solid #4D4D4D;margin-top:10px;">
                    <td colspan="7">
                        <h4><?php echo $product['title']; ?></h4>
                        <?php echo (! empty($product['image']) ? '<img src="'.$product['image'].'">' : null) ?>
                    </td>
                </tr>
                
                <?php foreach ($product['variants'] as $variant): ?>
                <tr>
                    <td>
                        <?php echo $variant['product_variant']['title']; ?>
                    </td>
                    <td class="table-center">
                        <?php echo $variant['product_variant']['net_price']; ?>
                    </td>
                    <td class="table-center">
                        <?php echo ($variant['product_variant']['special_price']=='0.00')?'-':$variant['product_variant']['special_price']; ?>
                    </td>
                    <td>
                        <span class="pre_discount"></span>
                        <?php echo Form::text('variants['.$variant['id'].'][discount]', $variant['discount'], array('class' => 'discount', 'data-net_price' => $variant['product_variant']['net_price'])); ?>
                        <?php echo Form::select('variants['.$variant['id'].'][discount_type]', $discountOptions, $variant['discount_type'], array('class' => 'discount_type')); ?>
                    </td>
                    <td>
                        <?php echo min($variant['discount_price'], $variant['product_variant']['price']); ?>
                    </td>
                    <!--<td>-->
                        <?php echo Form::hidden('variants['.$variant['id'].'][started_at]', date('Y-m-d', strtotime($variant['started_at'])), array('class' => 'started_at')); ?>
                    <!--</td>-->
                    <!--<td>-->
                        <?php echo Form::hidden('variants['.$variant['id'].'][ended_at]', date('Y-m-d', strtotime($variant['ended_at'])), array('class' => 'ended_at')); ?>
                    <!--</td>-->
                    <td>
                        
                        <?php echo Form::hidden('variants['.$variant['id'].'][net_price]', $variant['product_variant']['net_price']); ?>
                        
                        <label>
                            <?php echo Form::checkbox('variants['.$variant['id'].'][delete]'); ?>
                            <span class="icon icon-trash"></span>
                        </label>
                    </td>
                </tr>
                <?php endforeach; ?>

                <?php endforeach; ?>
                
            </tbody>
        </table>
        
        <div class="mws-button-row">
            <?php echo Form::submit('Save', array('class' => 'btn btn-primary')); ?>
            <a href="<?php echo URL::to('discount-campaigns'); ?>" class="btn btn-default">Cancel</a>
        </div>
        
        <?php echo Form::close(); ?>
    </div>

</div>

<style>
.discount { width: 80px; }    
.started_at, .ended_at { width: 120px; }
.pre_discount { display: inline; width: 50px; }
</style>