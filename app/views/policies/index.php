<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Policy Management </span>
    </div>

     <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a class="btn" href="<?php echo URL::to('policies/create') ?>"><i class="icol-add"></i> Create Policy</a>
            </div>
        </div>
    </div>

    <div class="mws-panel-body no-padding">

        <?php echo HTML::message(); ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
					<th style="width:15%;" class="no_sort"><?php echo Policy::getLabel('logo') ?></th>
					<th><?php echo Policy::getLabel('title') ?></th>
                    <th style="width:15%;"><?php echo Policy::getLabel('created_at') ?></th>
					<th style="width:15%;" class="no_sort"><?php echo Policy::getLabel('action') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($policyData)) foreach ($policyData as $key => $bData) : ?>
                    <tr>
						 <td><img src="<?php echo $bData->logo?>" alt="<?php echo $bData->title ?>" width="60"></td>
                        <td><?php echo $bData->title ?></td>
                        <td><?php echo $bData->created_at->format('d-m-Y H:i:s'); ?></td>
                        <td>
                            <a href="<?php echo URL::to("policies/edit/{$bData->id}") ?>">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
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
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": true, "true": false , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
                ]
            });
        }
    });
}) (jQuery, window, document);
</script>