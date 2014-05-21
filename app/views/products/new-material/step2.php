<?php echo Form::open(array('class' => 'mws-form wzd-default', 'files' => true)); ?>
<div class="mws-button-row" style="height: 30px;">
    <input type="submit" class="btn btn-primary pull-right" name="submit" value="Next" />
</div>
<table class="mws-table mws-datatable-fn multiple-selectable" id="datatables_index">
    <thead>
        <tr>
            <th class="checkbox-column"><input type="checkbox"></th>
            <!--
            <th>Vendor</th>
            <th>Product Line</th>
            <th>Material Title</th>
            <th>Inventory ID / Material Code</th>
            -->
            <th>Material Title</th>
            <th>Details</th>

            <th>Pick</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // if($materialsId)
            // {
            //     $first_set = null;
            // } else {
            //     $first_set = $data->first();
            //     // get first linesheet to save
            //     $first_set = md5($first_set['linesheet'].$first_set['vendor_detail']);
            //     $materialsId = array();
            // }
        ?>
        <?php foreach ($data as $key => $row): ?>
        <tr>
            <td class="checkbox-column">
                <input type="checkbox"
                	class="selectable"
                    id="cb_<?php echo $row['id']; ?>" name="id[]"
                    value="<?php echo $row['id']; ?>"
                    <?php
                        if (
                            ($materialsId && in_array($row['id'], $materialsId))
                            || (! $materialsId && $row['linesheet'] == $lineSheet)
                        )
                        {
                            echo "checked";
                        }
                    ?>
                >
            </td>
            <!--
            <td><?php echo $row['vendor_detail']; ?></td>
            <td><?php echo $row['linesheet']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td class="table-center"><?php echo $row['inventory_id']; ?> / <?php echo $row['material_code']; ?></td>
            -->
            <td width="350px">
                <?php echo $row->name; ?><br>
                <img src="<?php echo $row->image_preview_1_url; ?>" class="image_preview"><br>
                <?php echo $row->linesheet; ?>
            </td>
            <td>
                Inventory ID: <?php echo $row->inventory_id; ?><br>
                Material code: <?php echo $row->material_code; ?><br>
                Color: <?php echo $row->color ?: '-'; ?><br>
                Size: <?php echo $row->size ?: '-'; ?><br>
                Surface: <?php echo $row->surface ?: '-'; ?><br>
                Vendor: <?php echo $row->vendor_detail; ?>
            </td>

            <td style="width: 30px;"><button type="submit" class="btn" name="pick" value="<?php echo $row['id']; ?>">&gt;</button></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="mws-button-row" style="height: 30px;">
    <input type="submit" class="btn btn-primary pull-right" name="submit" value="Next" />
</div
<?php echo Form::close(); ?>

<?php
Theme::asset()->writeStyle('css_style_option', '
.image_preview {
    max-width: 90px;
    max-height: 90px;
}

', array('wizard'));
Theme::asset()->add('bulk-select', URL::to('js/bulkselect-table.js'));
Theme::asset()->container('footer')->writeScript('bulk-select-script', '
    $(".multiple-selectable").bulkSelectTable();
');

?>