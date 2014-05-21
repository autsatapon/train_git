<?php
header("Content-Type: application/vnd.ms-excel");
header('Content-Disposition: attachment; filename="search_results.xls"');
header("Pragma: no-cache");
header("Expires: 0");
?>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-type" content="text/html;charset=utf-8" />
</head>
<body>
<?php if (!empty($products)) { ?>
    <?php foreach ($products as $key=>$product) { ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr style="background:#F9F9E0;">
                    <th>Brand</th>
                    <th>Product Title</th>
                    <th>Product pkey</th>
                    <th>Inventory ID</th>
                    <th>Vendor ID</th>
                    <th>Vendor</th>
                    <th>Net Price</th>
                    <th>Special Price</th>
                    <th>Style & Options</th>
                    <th>Dimension (width, length, height, max) (cm)</th>
                    <th>Weight (g)</th>
                    <th>Fragility</th>
                    <th>Picture</th>
                    <th>360 Picture</th>
                    <th>Youtube</th>
                    <th>Product Tags</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center;"><strong><?php echo $product->brand->name ?></strong></td>
                    <td><strong><?php echo$product->title ?></strong></td>
                    <td style="text-align:center;"><strong><?php echo $product->pkey ?></strong>&nbsp;</td>
                    <td style="text-align:center;" colspan="9">-</td>
                    <td>
                        <?php
                            if (!empty($productLink[$product->id]['image']))
                            {
                                echo implode('<br>', $productLink[$product->id]['image']);
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            if (!empty($productLink[$product->id]['360']))
                            {
                                echo implode('<br>', $productLink[$product->id]['360']);
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            if (!empty($productLink[$product->id]['youtube']))
                            {
                                echo implode('<br>', $productLink[$product->id]['youtube']);
                            }
                        ?>
                    </td>
                    <td><?php echo $product->tag ?></td>
                </tr>
                <?php if (!$product->variants->isEmpty()) { ?>

                    <?php foreach ($product->variants as $key2 => $variant) { ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="2"><?php echo $variant->title ?></td>
                            <td style="text-align:center;"><?php echo $variant->inventory_id ?>&nbsp;</td>
                            <td style="text-align:center;"><?php echo $variant->vendor_id ?>&nbsp;</td>
                            <td style="text-align:center;"><?php echo $variant->vendor ?></td>
                            <td style="text-align:center;"><?php echo $variant->net_price ?></td>
                            <td style="text-align:center;"><?php echo $variant->special_price ?></td>
                            <td style="text-align:center;">
                                <?php
                                    if (!empty($variantStyleOption[$product->id][$variant->id]))
                                    {
                                        foreach ($variantStyleOption[$product->id][$variant->id] as $style => $option)
                                        {
                                            echo "{$style} - {$option}<br>";
                                        }
                                    }
                                ?>
                            </td>
                            <td style="text-align:center;">
                                <?php
                                    $tmp = array();
                                    if (!empty($variant->dimension_width))
                                        $tmp[] = $variant->dimension_width;
                                    if (!empty($variant->dimension_length))
                                        $tmp[] = $variant->dimension_length;
                                    if (!empty($variant->dimension_height))
                                        $tmp[] = $variant->dimension_height;
                                ?>
                                <?php
                                    echo implode('x', $tmp);
                                    if (!empty($variant->dimension_max))
                                        echo ' , ' . $variant->dimension_max;
                                ?>
                            </td>
                            <td style="text-align:center;"><?php echo $variant->weight ?></td>
                            <td style="text-align:center;"><?php echo $variant->fragility ?></td>
                            <td>
                                <?php
                                    if (!empty($variantLink[$product->id][$variant->id]['image']))
                                    {
                                        echo implode('<br>', $variantLink[$product->id][$variant->id]['image']);
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    if (!empty($variantLink[$product->id][$variant->id]['360']))
                                    {
                                        echo implode('<br>', $variantLink[$product->id][$variant->id]['360']);
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    if (!empty($variantLink[$product->id][$variant->id]['youtube']))
                                    {
                                        echo implode('<br>', $variantLink[$product->id][$variant->id]['youtube']);
                                    }
                                ?>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                    <?php } ?>

                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
<?php } ?>
</body>
</html>