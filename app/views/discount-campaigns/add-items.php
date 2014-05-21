<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> List Product </span>
    </div>

    <?php echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>

    <div class="mws-panel-body no-padding">

        <?php echo HTML::message() ?>

        <?php if ( !empty($products) && (is_object($products) && (!$products->isEmpty())) ) { ?>
        
            <?php echo Form::open(array('class' => 'mws-form', 'url' => '/discount-campaigns/list/'.$campaignId, 'method' => 'GET')); ?>
        
            <div class="mws-button-row" style="margin-top: 10px; margin-bottom: -10px;">
                <?php echo Form::submit('Submit', array('class' => 'btn btn-primary')); ?>
                <a href="<?php echo URL::to('discount-campaigns/list/'.$campaignId); ?>" class="btn btn-default">Cancel</a>
                <div class="pull-right">
                    <label>
                        <input type="checkbox" class="all-products-checkbox" <?php if(Input::get('has_collection') == 'no'): ?>disabled<?php endif; ?> />
                        Select all products 
                    </label>
                </div>
            </div>
        
            <?php foreach ($products as $key=>$product) { ?>
                <table class="mws-table" style="margin:10px 0; border:1px solid #999;">
                    <thead>
                        <tr>
                            <th style="width:100px;"><?php echo Product::getLabel('title') ?></th>
                            <th><?php echo Brand::getLabel('name') ?></th>
                            <th style="width:100px;"><?php echo Product::getLabel('pkey') ?></th>
                            <th style="width:100px;"><?php echo ProductVariant::getLabel('inventory_id') ?></th>
                            <th style="width:100px;"><?php echo ProductVariant::getLabel('vendor_id') ?></th>
                            <th style="width:120px;"><?php echo ProductVariant::getLabel('vendor') ?></th>
                            <th style="width:80px;">Add to campaigns</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background:#F9F9E0;">
                            <td class="table-center" rowspan="<?php echo count($product->variants)+1 ?>">
                                <strong><?php echo $product->title ?></strong>
                                <div><?php echo (! empty($product->image) ? '<img src="'.$product->image.'">' : null) ?></div>
                            </td>
                            <td><strong><?php echo $product->brand->name ?></strong></td>
                            <td class="table-center"><strong><?php echo $product->pkey ?></strong></td>
                            <td colspan="3" class="table-center">-</td>
                            <td style="text-align: center;">
                                <label>
                                    <input type="checkbox" name="added-products[]" value="<?php echo $product->getKey(); ?>" class="products-checkbox" <?php if(Input::get('has_collection') == 'no'): ?>disabled<?php endif; ?>/>
                                </label>
                            </td>
                        </tr>
                        <?php if (!$product->variants->isEmpty()) { ?>

                            <?php foreach ($product->variants as $key2 => $variant) { ?>
                                <tr>
                                    <td colspan="2"><?php echo $variant->title ?></td>
                                    <td class="table-center"><?php echo $variant->inventory_id ?></td>
                                    <td class="table-center"><?php echo $variant->vendor_id ?></td>
                                    <td class="table-center"><?php echo @$variant->vendor->name ?></td>
                                    <td>&nbsp;</td>
                                </tr>
                            <?php } ?>

                        <?php } ?>
                        <tr style="border-top:1px solid #999999">

                        </tr>
                    </tbody>
                </table>
        
            <?php } ?>
        
            <div class="mws-button-row">
                <?php echo Form::submit('Submit', array('class' => 'btn btn-primary')); ?>
                <a href="<?php echo URL::to('discount-campaigns/list/'.$campaignId); ?>" class="btn btn-default">Cancel</a>
                <div class="pull-right">
                    <label>
                        <input type="checkbox" class="all-products-checkbox" <?php if(Input::get('has_collection') == 'no'): ?>disabled<?php endif; ?>/>
                        Select all products
                    </label>
                </div>
            </div>
        
        <?php } ?>

        <?php echo Form::close(); ?>

    </div>
</div>