<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Shop Management </span>
    </div>

    <div class="mws-panel-body no-padding">

        <?php echo HTML::message(); ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
					<th style="width:15%;" class="no_sort">Shop ID</th>
					<th>Shop Name</th>
					<th style="width:15%;" class="no_sort">Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($shopData)) foreach ($shopData as $key => $sData) : ?>
                    <tr>
						 <td><?php echo $sData->shop_id ?></td>
                        <td><?php echo $sData->name ?></td>
                        <td>
                            <a href="<?php echo URL::to("shops/edit/{$sData->shop_id}") ?>">Edit</a>
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
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "alignCenter" },
                    { "bVisible": true, "bSearchable": true, "true": false , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "alignCenter" }
                ]
            });
        }
    });
}) (jQuery, window, document);
</script>