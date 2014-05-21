<div class="mws-panel grid_8">

    <div class="mws-panel-header">
        <span>
            <i class="icon-table"></i> Discount campaigns
        </span>
    </div>

    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group btn-collection-nav">
                <a class="btn" href="<?php echo URL::to('discount-campaigns/create'); ?>"><i class="icol-add"></i> Create campaign</a>
            </div>
        </div>
    </div>

    <div class="mws-panel-body no-padding">

        <?php echo HTML::message(); ?>

        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th width="10%">App</th>
                    <th width="30%">Name</th>
                    <th width="10%">Type</th>
                    <th width="15%">Start</th>
                    <th width="15%">End</th>
                    <th width="15%">Created at</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($discountCampaigns as $campaign): ?>
                <tr>
                    <td>
                        <?php echo $campaign->pApp['name']; ?>
                    </td>
                    <td>
                        <h4><?php echo $campaign->name; ?> (<?php echo $campaign->code; ?>)</h4>
                        <p><?php echo $campaign->description; ?></p>
                    </td>
                    <td>
                        <?php echo $typeOptions[$campaign->type]; ?>
                    </td>
                    <td>
                        <?php echo $campaign->started_at->format('d F Y'); ?>
                    </td>
                    <td>
                        <?php echo $campaign->ended_at->format('d F Y'); ?>
                    </td>
                    <td>
                        <?php echo $campaign->created_at->format('d F Y'); ?>
                    </td>
                    <td>
                        <a href="<?php echo URL::to('discount-campaigns/list').'/'.$campaign->getKey(); ?>" class="btn btn-warning" style="margin-bottom: 5px;">
                            <span class="icon icon-th-list"></span> Discount products
                        </a><br/>
                        <a href="<?php echo URL::to('discount-campaigns/edit').'/'.$campaign->getKey(); ?>" class="btn btn-info">
                            <span class="icon icon-edit"></span> Edit
                        </a>
                        <a href="<?php echo URL::to('discount-campaigns/delete').'/'.$campaign->getKey(); ?>" class="btn btn-danger delete">
                            <span class="icon icon-trash"></span> Delete
                        </a>
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
        if ($.fn.dataTable)
		{
            var table = $(".mws-datatable-fn").dataTable({
                bSort: true,
				aLengthMenu: [
					[30, 100, -1],
					[30, 100, "All"]
				],
				iDisplayLength: 30,
                sPaginationType: "full_numbers",
                 "aoColumns": [
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" }, // app
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" }, // name
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" }, // type
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" }, // start
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" }, // end
                    { "bVisible": true, "bSearchable": true, "bSortable": true , sClass: "" }, // created_at
                    { "bVisible": true, "bSearchable": false, "bSortable": false , sClass: "" } // actions
                ]
            });

			// order by sla_time_at
			table.fnSort([[5,'desc']]);

//			$(".filter.task").click(function() {
//				table.fnFilter('1', 7);
//			});

        }
    });
}) (jQuery, window, document);
</script>