<div id="popup-manage-items-exclude-product" class="popup-manage-items-wrap">
    
    <input type="hidden" id="type" name="" value="exclude-product" />
    
    <div class="mws-panel-body no-padding">
        
        <?php // echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>
        
        <?php if ( ! empty($products) ): ?>
        
        <div class="panel-add">
            
            <input type="button" class="btn btn-primary add" value="Exclude" />
            
        </div>
        
        <?php foreach ($products as $product): ?>
        
        <table class="mws-table">
            
            <thead>
                
                <tr>
                    <th style="width:12%;"><?php echo Product::getLabel('title') ?></th>
                    <th><?php echo Brand::getLabel('name') ?></th>
                    <th style="width:12%;"><?php echo ProductVariant::getLabel('inventory_id') ?></th>
                    <th style="width:12%;"><?php echo ProductVariant::getLabel('vendor_id') ?></th>
                    <th style="width:12%;"><?php echo ProductVariant::getLabel('vendor') ?></th>
                    <th style="width:5%;"><span class="icon icon-plus"></span> Add</th>
                </tr>
                
            </thead>
             
           <tbody>
               
                <tr class="product-head">
                    <td class="table-center" rowspan="<?php echo count($product->variants)+1 ?>">
                        <strong><?php echo $product->title ?></strong>
                        <div><?php echo (! empty($product->image) ? '<img src="'.$product->image.'">' : null) ?></div>
                    </td>
                    <td><strong><?php echo $product->brand->name ?></strong></td>
                    <td colspan="3" class="table-center">-</td>
                    <td class="table-center">
                        <label>
                            <input name="" type="checkbox" class="add-products" value="<?php echo $product->pkey; ?>" data-data='<?php echo json_encode(array('id' => $product->getKey(), 'pkey' => $product->pkey, 'title' => $product->title)); ?>' />
                        </label>
                    </td>
                </tr>
                
            </tbody>
        </table>
        <?php endforeach; ?>
        
        <div class="panel-add">
            
            <input type="button" class="btn btn-primary add" value="Exclude" />
            
        </div>
        
        <?php endif; ?>

    </div>
    
</div>