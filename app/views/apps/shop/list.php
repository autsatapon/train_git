<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> <?php echo Theme::place('title') ?></span>
    </div>

    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a class="btn" href="<?php echo URL::to("apps/shop/{$pcmsApp->id}/create") ?>"><i class="icol-add"></i> Create Shop</a>
            </div>
        </div>
    </div>

    <div class="mws-panel-body no-padding">
        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th style="width:30px;">Num</th>
                    <th>Shop Name</th>
                    <th style="width:180px;">Shop ID (Code)</th>
                    <th style="width:135px;" class="no_sort">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appShops as $key => $shop) { ?>
                    <tr>
                        <td><?php echo $key+1 ?></td>
                        <td><?php echo $shop->name ?></td>
                        <td><?php echo $shop->code ?></td>
                        <td>
                            <a href="<?php echo URL::to("apps/shop/{$pcmsApp->id}/edit/{$shop->id}") ?>">Edit</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function( $, window, document, undefined ) {
    $(document).ready(function() {
        // Data Tables
        if( $.fn.dataTable ) {
            $(".mws-datatable-fn").dataTable({
                bSort: true,
                sPaginationType: "full_numbers",
                 "aoColumns": [
                    { "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" },
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
                ]
            });
        }
    });
}) (jQuery, window, document);
</script>