<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> List Product</span>
    </div>

    <?php echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>

    <div class="mws-panel-body no-padding dataTables_wrapper">

        <?php echo HTML::message() ?>

        <?php if ( !empty($products) && (is_object($products) && (!$products->isEmpty())) ) { ?>
            <p style="padding:10px; text-align:right;">
                <a class="btn btn-info" href="<?php echo URL::to('products/search/export') . '?' . $_SERVER['QUERY_STRING'] ?>"><i class="icon icon-download-2"></i> Export Search Results</a>
            </p>
            <?php foreach ($products as $key=>$product) { ?>
                <table class="mws-table" style="margin:10px 0; border:1px solid #999;">
                    <thead>
                        <tr>
                            <th style="width:100px;"><?php echo Brand::getLabel('name') ?></th>
                            <th><?php echo Product::getLabel('title') ?></th>
                            <th style="width:100px;"><?php echo Product::getLabel('pkey') ?></th>
                            <th style="width:100px;"><?php echo ProductVariant::getLabel('inventory_id') ?></th>
                            <th style="width:100px;"><?php echo ProductVariant::getLabel('vendor_id') ?></th>
                            <th style="width:120px;"><?php echo ProductVariant::getLabel('vendor') ?></th>
                            <th style="width:80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background:#F9F9E0;">
                            <td class="table-center"><strong><?php echo $product->brand->name ?></strong></td>
                            <td><strong><?php echo $product->title ?></strong></td>
                            <td class="table-center"><strong><?php echo $product->pkey ?></strong></td>
                            <td colspan="3" class="table-center">-</td>
                            <td>&nbsp;</td>
                        </tr>
                        <?php if (!$product->variants->isEmpty()) { ?>

                            <?php foreach ($product->variants as $key2 => $variant) { ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td colspan="2"><?php echo $variant->title ?></td>
                                    <td class="table-center"><?php echo $variant->inventory_id ?></td>
                                    <td class="table-center"><?php echo $variant->vendor_id ?></td>
                                    <td class="table-center"><?php //echo $variant->vendor ?></td>
                                    <td>&nbsp;</td>
                                </tr>
                            <?php } ?>

                        <?php } ?>

                        <tr style="border-top:3px solid #4D4D4D;border-bottom:1px solid #4D4D4D;margin-top:10px;">

                        </tr>
                    </tbody>
                </table>

            <?php } ?>
            <?php
                $query = array(
                    'product' => Input::get('product'),
                    'product_line' => Input::get('product_line'),
                    'tag' => Input::get('tag'),
                    'brand' => Input::get('brand'),
                    'has_product_content' => Input::get('has_product_content'),
                    'has_product_mediacontent' => Input::get('has_product_mediacontent'),
                    'product_allow_installment' => Input::get('product_allow_installment'),
                    'variant_allow_installment' => Input::get('variant_allow_installment'),
                    'product_allow_cod' => Input::get('product_allow_cod'),
                    'has_price' => Input::get('has_price'),
                    );
                echo $products->appends($query)->links();
            ?>


            <?php }else{ ?>

                <table class="mws-table" style="margin:10px 0; border:1px solid #999;">
                    <thead>
                        <tr>
                            <th style="width:100px;"><?php echo Brand::getLabel('name') ?></th>
                            <th><?php echo Product::getLabel('title') ?></th>
                            <th style="width:100px;"><?php echo Product::getLabel('pkey') ?></th>
                            <th style="width:100px;"><?php echo ProductVariant::getLabel('inventory_id') ?></th>
                            <th style="width:100px;"><?php echo ProductVariant::getLabel('vendor_id') ?></th>
                            <th style="width:120px;"><?php echo ProductVariant::getLabel('vendor') ?></th>
                            <th style="width:80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="table-center" colspan="7">ไม่พบข้อมูล</td>
                        </tr>
                    </tbody>
                </table>

            <?php } ?>

    </div>
</div>

