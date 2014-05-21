<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Brands Management </span>
    </div>

     <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a class="btn" href="<?php echo URL::to('brands/create') ?>"><i class="icol-add"></i> Create Brand</a>
            </div>
        </div>
    </div>

    <div class="mws-panel-body no-padding">
        <?php if (Session::has('success')): ?>
            <div class="alert alert-success">
                <p><?php echo Session::get('success') ?></p>
            </div>
        <?php endif ?>
        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th style="width:30px;">No</th>
                    <th><?php echo Brand::getLabel('name') ?></th>
                    <th style="width:135px;" class="no_sort"><?php echo Brand::getLabel('pkey') ?></th>
                    <th style="width:135px;" class="no_sort">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($brandData as $key => $bData) { ?>
                    <tr>
                        <td><?php echo $key+1 ?></td>
                        <td><?php echo $bData->name ?></td>
                        <td><?php echo $bData->pkey ?></td>
                        <td>
                            <a href="<?php echo URL::to("brands/edit/{$bData->id}") ?>">Edit</a>
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
                    { "bVisible": true, "bSearchable": true, "bSortable": false , sClass: "" },
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" },
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
                ]
            });
        }
    });
}) (jQuery, window, document);
</script>