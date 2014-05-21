<?php

		Theme::asset()->writeStyle('Campaign-Management-index-version-1.0','
		.alignCenter {
		    text-align:center;
		}
		.alignRight {
		    text-align:right;
			padding-right:5px;
		}
		.w30 {
		    width:30px;
		}
		.dataTable {
			width:100%; !Important;
		}
		');

?>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Campaign Management </span>
    </div>

     <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a class="btn" href="<?php echo URL::to('campaigns/create') ?>"><i class="icol-add"></i> Create Campaign</a>
                <a class="btn pull-right" href="<?php echo URL::to('campaigns/rebuild') ?>"><i class="icon-github"></i> Re-build all promotions</a>
            </div>
        </div>
    </div>

    <div class="mws-panel-body no-padding">

        <?php echo HTML::message(); ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
					<th><?php echo Campaign::getLabel('id') ?></th>
					<th><?php echo Campaign::getLabel('name') ?></th>
<?php /*                    <th><?php echo Campaign::getLabel('budget') ?></th>
                    <th><?php echo Campaign::getLabel('used_budget') ?></th>  */ ?>
                    <th><?php echo Campaign::getLabel('used_times') ?></th>
                    <th><?php echo Campaign::getLabel('used_users') ?></th>
                    <th><?php echo Campaign::getLabel('gifted_items') ?></th>
                    <th><?php echo Campaign::getLabel('period') ?></th>
                    <th><?php echo Campaign::getLabel('status') ?></th>
                    <th style="width:15%;" class="no_sort">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($campaignData)) foreach ($campaignData as $key => $cData) : ?>
                    <tr>
					<td><?php echo $cData->id ?></td>
						<td><?php echo $cData->name ?></td>
<?php /*                        <td class="alignRight"><?php echo number_format($cData->budget,2); ?></td>
                        <td class="alignRight"><?php echo number_format($cData->used_budget,2); ?></td>   */ ?>
                        <td class="alignCenter"><?php echo $cData->used_times ?></td>
                        <td class="alignCenter"><?php echo $cData->used_users ?></td>
                        <td class="alignCenter"><?php echo $cData->gifted_items ?></td>
                        <td class="alignRight">
                        	<strong>From:</strong>
                            <div><?php echo $cData->start_date ?></div>
                            <strong>To:</strong>
                            <div><?php echo $cData->end_date ?></div>
                        </td>
                        <td class="alignCenter">
                        	<?php if($cData->status == 'activate'){?>
                        		<img src="imgs/tbd_Check.png" title="activate" class="w30">
                        	<?php }else{ ?>
                        		<img src="imgs/tbd_Delete.png" title="deactivate" class="w30">
                        	<?php } ?>
                        </td>
                        <td class="alignCenter">
                            <div class="btn-group">
                                <a class="btn btn-info" href="<?php echo URL::to("promotions/{$cData->id}") ?>"><i class="icon-tasks"></i> Promotions</a>
                                <a class="btn btn-info" href="<?php echo URL::to("campaigns/rebuild/{$cData->id}") ?>"><i class="icon-github"></i> Re-build</a>
                                <a class="btn btn-default" href="<?php echo URL::to("campaigns/edit/{$cData->id}") ?>"><i class="icon-edit"></i> Edit</a>
                            </div>


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
				  "aaSorting": [[0, 'desc']],
                 "aoColumns": [
					{ "bVisible": false, "bSearchable": false, "bSortable": false , sClass: "" },
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" },
					// { "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "" },
					// { "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" },
                ]
            });
        }

        $("#DataTables_Table_0").removeAttr("style");

    });
}) (jQuery, window, document);
</script>