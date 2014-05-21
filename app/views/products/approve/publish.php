<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Approve Product</span>
    </div>
    <div class="mws-panel-body no-padding dataTables_wrapper">

        <?php if ( !empty($product) && (is_object($product)) ) { ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th style="width:100px;">Field</th>
                    <th>Published Version</th>
                    <th>Modified Version</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Editor</strong></td>
                    <td colspan="2"><?php echo User::find($revision->editor_id)->display_name ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo Product::getLabel('title') ?></strong></td>
                    <td><?php echo $product->title ?></td>
                    <td><?php if (isset($modifiedData['title'])) { echo '<span class="text-success">' . $modifiedData['title'] . '</span>'; } else { echo $product->title; } ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo Brand::getLabel('name') ?></strong></td>
                    <td><?php echo $product->brand->name ?></td>
                    <td><?php if (isset($modifiedData['brand'])) { echo '<span class="text-success">' . $modifiedData['brand']->name . '</span>'; } else { echo $product->brand->name; } ?></td>
                </tr>

                <?php if (isset($modifiedData['description'])) { ?>
                <tr>
                    <td><strong><?php echo Product::getLabel('description') ?></strong></td>
                    <td><?php echo $product->description ?></td>
                    <td><?php if (isset($modifiedData['description'])) { echo '<span class="text-success">' . $modifiedData['description'] . '</span>'; } else { echo $product->description; } ?></td>
                </tr>
                <?php } else { ?>
                <tr>
                    <td><strong><?php echo Product::getLabel('description') ?></strong></td>
                    <td class="table-center" colspan="2">Not Modified</td>
                </tr>
                <?php } ?>

                <?php if (isset($modifiedData['key_feature'])) { ?>
                <tr>
                    <td><strong><?php echo Product::getLabel('key_feature') ?></strong></td>
                    <td><?php echo $product->key_feature ?></td>
                    <td><?php if (isset($modifiedData['key_feature'])) { echo '<span class="text-success">' . $modifiedData['key_feature'] . '</span>'; } else { echo $product->key_feature; } ?></td>
                </tr>
                <?php } else { ?>
                <tr>
                    <td><strong><?php echo Product::getLabel('key_feature') ?></strong></td>
                    <td class="table-center" colspan="2">Not Modified</td>
                </tr>
                <?php } ?>

                <?php if (isset($modifiedData['price'])) { ?>
                <tr>
                    <td><strong>Price</strong></td>
                    <td>
                        <?php foreach ($product->variants as $key=>$variant) { ?>
                        <p style="margin-bottom:15px;">
                            <strong style="color:#960;"><?php echo $variant->title ?></strong><br>
                            Net Price - <?php echo $variant->net_price ?><br>
                            Special Price - <?php echo $variant->special_price ?><br>
                            <?php /*
                            Normal Price - <?php echo $variant->normal_price ?><br>
                            Price - <?php echo $variant->price ?><br>
                            */ ?>
                            Free Item - <?php echo $variant->free_item ?>
                        </p>
                        <?php } ?>
                    </td>
                    <td>
                        <?php foreach ($product->variants as $key=>$variant) { ?>
                        <p style="margin-bottom:15px;">
                            <strong style="color:#960;"><?php echo $variant->title ?></strong><br>
                            Net Price - <?php echo isset($modifiedData['price']["{$variant->id}"]['net_price']) ? $modifiedData['price']["{$variant->id}"]['net_price'] : $variant->net_price ?><br>
                            Special Price - <?php echo isset($modifiedData['price']["{$variant->id}"]['special_price']) ? $modifiedData['price']["{$variant->id}"]['special_price'] : $variant->special_price ?><br>
                            <?php /*
                            Normal Price - <?php echo isset($modifiedData['price']["{$variant->id}"]['normal_price']) ? $modifiedData['price']["{$variant->id}"]['normal_price'] : $variant->normal_price ?><br>
                            Price - <?php echo isset($modifiedData['price']["{$variant->id}"]['price']) ? $modifiedData['price']["{$variant->id}"]['price'] : $variant->price ?><br>
                            */ ?>
                            Free Item - <?php echo isset($modifiedData['price']["{$variant->id}"]['free_item']) ? $modifiedData['price']["{$variant->id}"]['free_item'] : $variant->free_item ?>
                        </p>
                        <?php } ?>
                    </td>
                </tr>
                <?php } else { ?>
                <tr>
                    <td><strong>Price</strong></td>
                    <td class="table-center" colspan="2">Not Modified</td>
                </tr>
                <?php } ?>

            </tbody>
        </table>


        <?php } ?>
 
    </div>

    <div class="mws-panel-body no-padding">
        <form class="mws-form" method="post" action="">
            <?php /*
            <div class="mws-form-row">
                <label class="mws-form-label"><strong>Revision Status</strong></label>
                <div class="mws-form-item">
                    <ul class="mws-form-list">
                        <li>
                            <label class="radio">
                                <input name="status" type="radio" value="approved" <?php if ($revision->status == 'approved') { echo ' checked="checked"'; } ?>> Approve
                            </label>
                        </li>
                        <li>
                            <label class="radio">
                                <input name="status" type="radio" value="rejected" <?php if ($revision->status == 'rejected') { echo ' checked="checked"'; } ?>> Reject
                            </label>
                        </li>
                        <li>
                            <label class="radio">
                                <input name="status" type="radio" value="draft" <?php if ($revision->status == 'draft') { echo ' checked="checked"'; } ?>> Draft
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mws-form-row">
                <label class="mws-form-label"><strong>Note</strong></label>
                <div class="mws-form-item">
                    <textarea name="note" class="small"><?php echo $revision->note ?></textarea>
                </div>
            </div>
            */ ?>

            <div class="mws-button-row">
                <div class="mws-form-item">

                    <input value="Publish Content" class="btn btn-primary" type="submit">

                    <a href="<?php echo URL::to('products/approve/wait-for-publish') ?>" class="btn">Back</a>
                </div>
            </div>
        </form>
    </div>

</div>
