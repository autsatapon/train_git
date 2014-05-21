<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Apps Management</span>
    </div>

    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a class="btn" href="<?php echo URL::to('apps/create') ?>"><i class="icol-add"></i> Create App</a>
            </div>
        </div>
    </div>

    <div class="mws-panel-body no-padding">
        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th style="width:30px;">No</th>
                    <th><?php echo PApp::getLabel('name') ?></th>
                    <th style="width:180px;"><?php echo PApp::getLabel('url') ?></th>
                    <th style="width:100px;"><?php echo PApp::getLabel('stock_code') ?></th>
                    <th style="width:100px;"><?php echo PApp::getLabel('nonstock_code') ?></th>
                    <th style="width:135px;" class="no_sort"><?php echo PApp::getLabel('pkey') ?></th>
                    <th style="width:80px;" class="no_sort"><?php echo PApp::getLabel('Free Shipping') ?></th>
                    <th style="width:55px;" class="no_sort">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pcmsApps as $key => $pApp) { ?>
                    <tr>
                        <td><?php echo $key+1 ?></td>
                        <td><?php echo $pApp->name ?></td>
                        <td><?php echo $pApp->url ?></td>
                        <td><?php echo $pApp->stock_code ?></td>
                        <td><?php echo $pApp->nonstock_code ?></td>
                        <td><?php echo $pApp->pkey ?></td>
                        <td><?php echo $pApp->free_shipping ?></td>
                        <td>
                            <?php /* <a href="<?php echo URL::to("apps/shop/{$pApp->id}") ?>">Shops</a> | */ ?>
                            <a href="<?php echo URL::to("apps/edit/{$pApp->id}") ?>">Edit</a>
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
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": true, "bSortable": false , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": true, "bSortable": false , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
                ]
            });
        }
    });
}) (jQuery, window, document);
</script>