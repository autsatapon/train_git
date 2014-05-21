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
		');

?>
<div class="mws-panel grid_8">

    <div class="mws-panel-header">
        <span><i class="icon-table"></i> <?php echo $campaign ? "Campaign : ".$campaign->name : "Promotion Management"; ?></span>
    </div>

     <div class="mws-panel-toolbar">
        <?php if($campaign): ?>
        <div class="btn-toolbar">
            <div class="btn-group">
                <a class="btn" href="<?php echo URL::to('promotions/create/'.$campaign->id) ?>"><i class="icol-add"></i> Create Promotion</a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="mws-panel-body no-padding">

        <?php echo HTML::message(); ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
					<th><?php echo Promotion::getLabel('name') ?></th>
<?php /*                    <th><?php echo Promotion::getLabel('budget') ?></th>
                    <th><?php echo Promotion::getLabel('used_budget') ?></th>  */ ?>
                    <th><?php echo Promotion::getLabel('used_times') ?></th>
                    <th><?php echo Promotion::getLabel('used_users') ?></th>
                    <th><?php echo Promotion::getLabel('gifted_items') ?></th>
                    <th><?php echo Promotion::getLabel('period') ?></th>
                    <th><?php echo Promotion::getLabel('status') ?></th>
                    <th style="width:15%;" class="no_sort">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($promotions)) foreach ($promotions as $key => $promotion) : ?>
                    <tr>
						<td><?php echo $promotion->name ?></td>
<?php /*                         <td class="alignRight"><?php echo number_format($promotion->budget,2); ?></td>
                        <td class="alignRight"><?php echo number_format($promotion->used_budget,2); ?></td>  */ ?>
                        <td class="alignCenter"><?php echo $promotion->used_times ?></td>
                        <td class="alignCenter"><?php echo $promotion->used_users ?></td>
                        <td class="alignCenter"><?php echo $promotion->gifted_items ?></td>
                        <td class="alignRight">
                        	<strong>From:</strong>
                            <div><?php echo $promotion->start_date ?></div>
                            <strong>To:</strong>
                            <div><?php echo $promotion->end_date ?></div>
                        </td>
                        <td class="alignCenter">
                        	<?php if($promotion->status == 'activate'){?>
                        		<!-- <img src="/imgs/tbd_Check.png" title="activate" class="w30"> -->
                                <strong style="color:#0FC406">Activated</strong>
                        	<?php }else{ ?>
                        		<!-- <img src="/imgs/tbd_Delete.png" title="deactivate" class="w30"> -->
                                <strong style="color:#FF0000">Deactivate</strong>
                        	<?php } ?>
                        </td>
                        <td class="alignCenter">
                            <div class="btn-group">
                                <a class="btn btn-info" href="<?php echo URL::to("promotions/view/{$promotion->id}").($campaign ? "?campaign=true" : "") ?>"><i class="icon-list-alt"></i> View</a>
                                <a class="btn btn-default" href="<?php echo URL::to("promotions/edit/{$promotion->id}").($campaign ? "?campaign=true" : "") ?>"><i class="icon-edit"></i> Edit</a>
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
                 "aoColumns": [
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": true , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" },
					{ "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" }
                ]
            });
        }
    });
}) (jQuery, window, document);
</script>