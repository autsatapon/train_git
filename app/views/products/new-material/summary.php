<?php // echo Form::open(array('class' => 'mws-form wzd-default', 'files' => true)); ?>
<div class="mws-form wzd-default">
    <div class="mws-form-row">
        <div class="mws-form-cols" style="margin-bottom: 15px;">
            <?php if ($product->productImage): ?>
            <div class="mws-form-col-1-8">
                <img src='<?php echo $product->productImage; ?>' style='margin-right:20px; width: 80px;'>

            </div>
            <?php endif; ?>

            <div class="mws-form-col-2-8">
                <b>Product:</b>
                <?php echo $product->title; ?>
                <br>
                <b>Product Line:</b>
                <?php echo $product->product_line; ?>
            </div>
            <div class="mws-form-col-2-8">
                <b>Brand:</b>
                <?php echo $product->brand->name; ?>
            </div>
        </div>
    </div>

    <table class="mws-table mws-datatable-fn multiple-selectable" id="datatables_index">
        <thead style="border-top: 1px solid rgb(204, 204, 204);">
            <tr>
                <th>Variant Title</th>
                <th>Retail Price</th>
                <?php foreach($product->styleTypes as $index => $styleType): ?>
                    <th data-set="style_<?php echo $index; ?>">
                        <?php echo $styleType->name; ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($newVariantsId as $key => $newVariantId):
                    // get product variant
                    $variant = $product->variants->find($newVariantId);
                    if (! $variant)
                    {
                        continue;
                    }
            ?>
                <tr style="text-align: center;">
                    <td><?php echo $variant->title; ?></td>
                    <td><?php echo number_format($variant->retail_price, 0); ?></td>
                    <?php foreach($product->styleTypes as $index => $styleType): ?>
                        <td>
                            <?php
                                $filter = function($model) use ($styleType)
                                {
                                    if ($model->style_type_id != $styleType->getKey())
                                    {
                                        return false;
                                    }

                                    return $model;
                                };
                                $variantStyleOptions = $variant->variantStyleOptions->filter($filter)->first();
                                $styleOption = null;
                                if ($variantStyleOptions)
                                {
                                    $styleOption = $variantStyleOptions->styleOption;
                                }
                            ?>
                            <?php echo $styleOption ? $styleOption->text : ""; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>


            <?php
                foreach ($product->variants as $key => $variant):
                    // skip new variant
                    if (in_array($variant->getKey(), $newVariantsId))
                    {

                        continue;
                    }
            ?>
                <tr class="old_variant" style="text-align: center; background: #<?php echo $key%2==0 ? 'd2e2f2' : 'daeafa'; ?>;" data-select-name="option[<?php echo $variant->id; ?>][]">
                    <td><?php echo $variant->title; ?></td>
                    <td><?php echo number_format($variant->retail_price, 0); ?></td>
                    <?php foreach($product->styleTypes as $index => $styleType): ?>
                        <td data-set="style_<?php echo $index; ?>">
                            <?php
                                $filter = function($model) use ($styleType)
                                {
                                    if ($model->style_type_id != $styleType->getKey())
                                    {
                                        return false;
                                    }

                                    return $model;
                                };
                                $variantStyleOptions = $variant->variantStyleOptions->filter($filter)->first();
                                $styleOption = null;
                                if ($variantStyleOptions)
                                {
                                    $styleOption = $variantStyleOptions->styleOption;
                                }
                            ?>
                            <?php echo $styleOption ? $styleOption->text : ""; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>


        </tbody>
    </table>

    <div class="mws-button-row" style="height: 30px;">

    </div>
</div>
<?php // echo Form::close(); ?>
