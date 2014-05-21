<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Banner Sections Management </span>
    </div>

     <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a class="btn" href="<?php echo URL::to('banners/create') ?>"><i class="icol-add"></i> Create Section</a>
            </div>
        </div>
    </div>

    <div class="mws-panel-body no-padding">
        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th style="width:30px;">No</th>
                    <th><?php echo BannerSection::getLabel('name') ?></th>
                    <th style="width:135px;" class="no_sort"><?php echo BannerSection::getLabel('pkey') ?></th>
                    <th style="width:135px;" class="no_sort"><?php echo BannerSection::getLabel('app') ?></th>
                    <th style="width:135px;" class="no_sort">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sections as $key => $section) { ?>
                    <tr>
                        <td class="table-center"><?php echo $key+1 ?></td>
                        <td class="table-center"><?php echo $section->name ?></td>
                        <td class="table-center"><?php echo $section->pkey ?></td>
                        <td class="table-center"><?php echo $section->app->name ?></td>
                        <td class="table-center">
                            <a href="<?php echo URL::to("banners/edit/{$section->id}") ?>">Edit</a>
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
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" },
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
                ]
            });
        }
    });
}) (jQuery, window, document);
</script>
