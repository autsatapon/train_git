<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Publish Product</span>
    </div>

    <?php echo Theme::widget('WidgetProductSearchForm', array())->render(); ?>

    <div class="mws-panel-body no-padding dataTables_wrapper">

        <?php if ( !empty($products) && (is_object($products) && (!$products->isEmpty())) ) { ?>
        <?php foreach ($products as $product) { ?>
            <?php if ( !$product->revisions->isEmpty() ) { ?>
            <table class="mws-datatable-fn mws-table" style="margin:5px auto; border-bottom:1px solid #CCC; border-top:1px solid #CCC;">
                <thead>
                    <tr>
                        <th colspan="7" style="text-align:left;">
                            <h4>
                                Product - <?php echo $product->title ?>
                                &nbsp;&nbsp;&nbsp;<?php echo ($product->status == 'publish') ? '<i class="icol-accept"></i> Publish' : '<i class="icol-error"></i> Require Some Data before Publish' ; ?>
                            </h4>
                        </th>
                    </tr>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Product Title</th>
                        <th>Brand</th>
                        <th>Description</th>
                        <th>Key Feature</th>
                        <th>Price</th>
                        <?php /*
                        <th>Media</th>
                        <th>Tag</th>
                        <th>Net Price</th>
                        <th>Shipping Data</th>
                        */ ?>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background-color:#F9F9E0;">
                        <td>Product</td>
                        <td class="table-center"><?php echo $product->title ?></td>
                        <td class="table-center"><?php echo $product->brand->name ?></td>
                        <td class="table-center"><?php echo (!empty($product->description)) ? '<i class="icol-accept"></i> Has Description' : '<i class="icol-exclamation-octagon-fram"></i> Require' ; ?></td>
                        <td class="table-center"><?php echo (!empty($product->key_feature)) ? '<i class="icol-accept"></i> Has Key Feature' : '<i class="icol-exclamation-octagon-fram"></i> Require' ; ?></td>
                        <td class="table-center"><?php echo ($product->isProductHasPrice()) ? '<i class="icol-accept"></i> Has Price' : '<i class="icol-exclamation-octagon-fram"></i> Require' ; ?></td>
                        <td class="table-center">&nbsp;</td>
                    </tr>
                    <?php foreach ($product->revisions as $key=>$revision) { ?>
                    <?php $modifiedData = $revision->modifiedData; ?>
                    <tr>
                        <td style="text-align:right;">Revision <?php echo $key+1 ?></td>
                        <td class="table-center">
                            <?php echo (isset($modifiedData['title'])) ? $modifiedData['title'] : '-' ; ?>
                        </td>
                        <td class="table-center">
                            <?php echo (isset($modifiedData['brand'])) ? $modifiedData['brand']->name : '-' ; ?>
                        </td>
                        <td class="table-center">
                            <?php echo (isset($modifiedData['description'])) ? '<i class="icol-pencil"></i> has modified' : '-' ; ?>
                        </td>
                        <td class="table-center">
                            <?php echo (isset($modifiedData['key_feature'])) ? '<i class="icol-pencil"></i> has modified' : '-' ; ?>
                        </td>
                        <td class="table-center">
                            <?php echo (isset($modifiedData['price'])) ? '<i class="icol-pencil"></i> has modified' : '-' ; ?>
                        </td>
                        <td class="table-center">
                            <a href="<?php echo URL::to("products/approve/publish/{$product->id}/{$revision->id}") ?>" class="btn btn-primary">Detail</a>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr style="border-top:3px solid #4D4D4D;">
                        
                    </tr>
                </tbody>
            </table>
            
            <?php } ?>
        <?php } ?>


        <?php }else{ ?>

                <table class="mws-datatable-fn mws-table">
                    <table class="mws-datatable-fn mws-table" style="margin:15px auto; border-bottom:1px solid #CCC; border-top:1px solid #CCC;">
                        <thead>
                            <tr>
                                <th colspan="7" style="text-align:left;">
                                    <h4>
                                        Product Name
                                        
                                    </h4>
                                </th>
                            </tr>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Product Title</th>
                                <th>Brand</th>
                                <th>Description</th>
                                <th>Key Feature</th>
                                <th>Price</th>
                                <?php /*
                                <th>Media</th>
                                <th>Tag</th>
                                <th>Net Price</th>
                                <th>Shipping Data</th>
                                */ ?>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                    <tbody>
                        <td class="table-center" colspan="7">ไม่พบข้อมูล</td>
                    </tbody>
                </table>

        <?php } ?>


    </div>
</div>

