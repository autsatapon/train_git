<?php
    $user = Sentry::getUser();
	$userGroup = $user->getGroups()->first();

Theme::asset()->writeStyle('boyd-inline-in-dashboard-version-2.0','
.row:before, .row:after {
    content: "";
    display: table;
    line-height: 0;
}
.box .box-header {
    background-color: #F1F1F1;
    background-image: linear-gradient(to bottom, #C9C9C9, #ECECEC);
    background-repeat: repeat-x;
    border-bottom: 1px solid #D5D5D5;
    color: #333333;
    height: 20px;
    line-height: 20px;
    margin-bottom: auto;
    margin-top: auto;
    padding: 10px;
    -webkit-border-top-left-radius: 15px;
	-webkit-border-top-right-radius: 15px;
	-moz-border-radius-topleft: 15px;
	-moz-border-radius-topright: 15px;
	border-top-left-radius: 15px;
	border-top-right-radius: 15px;
	box-shadow:2px 0px 2px #ccc;
}
.box .box-content {
    padding: 10px;
    background-color:#F5F2F3;
   	-webkit-border-bottom-right-radius: 15px;
	-webkit-border-bottom-left-radius: 15px;
	-moz-border-radius-bottomright: 15px;
	-moz-border-radius-bottomleft: 15px;
	border-bottom-right-radius: 15px;
	border-bottom-left-radius: 15px;
	overflow:hidden;
	box-shadow:2px 2px 2px #ccc;
}

.box .box-content2 {
    padding: 15px;
    background-color:#F5F2F3;
   	-webkit-border-bottom-right-radius: 15px;
	-webkit-border-bottom-left-radius: 15px;
	-moz-border-radius-bottomright: 15px;
	-moz-border-radius-bottomleft: 15px;
	border-bottom-right-radius: 15px;
	border-bottom-left-radius: 15px;
	height:30px;
	overflow:hidden;
	box-shadow:2px 2px 2px #ccc;
}

.box {

    margin-bottom: 20px;
   	margin-top:20px;
}
.span8 {
    width: 718px;
}
/*.span6 {
    width: 433px;
}*/
[class*="span"] {
    float: left;
    margin-left: 30px;
    min-height: 1px;
}

.span12 {
	width:95%;
	overflow:hidden;
}

.span6 {
	width:46%;
	overflow:hidden;
}

.table {
    width: 100%;
    overflow:hidden;
    text-align:left;
}
table {
    background-color: rgba(0, 0, 0, 0);
    border-collapse: collapse;
    border-spacing: 0;
    max-width: 100%;
}

.left_box {
	width:60%;
	float:left;
	text-align:left;
}
.left_box strong {
	color:#FF0000;
}
.right_box {
	width:40%;
	float:left;
	text-align:right;
}

.box .box-header h5 {
    color: #555555;
    display: inline-block;
    font-size: 15px;
    font-weight: 800;
    left: 10px;
    line-height: 14px;
    position: relative;
    text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.5);
    top: -3px;
}

.table-striped tbody > tr:nth-child(2n+1) > td, .table-striped tbody > tr:nth-child(2n+1) > th {
    background-color: #FFFFFF;
}
.table-condensed th, .table-condensed td {
    padding: 4px 5px;
}

.pagination {
	text-align:center;
	width:100%
	border-top:1px solid #D5D5D5;
}

.pagination ul{
	list-style-type: none;
	margin-left:180px;
	overflow:hidden;
}

.pagination li{
	float:left;
	margin-left:5px;
}


');
?>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-home"></i> Dashboard </span>
    </div>


    <div class="mws-panel-body no-padding">

    	<?php echo HTML::message(); ?>

		<div class="row clearfix">

			<?php if(!$user->hasAccess('dashboard.view-new-material')){ ?>
				<?php if(!$user->hasAccess('dashboard.view-wait-approve-product')){ ?>
					<?php if(!$user->hasAccess('dashboard.view-rejected-product')){ ?>
						<div class="span6">
			                <div class="box">
			                	 <div class="box-header">
			                        <h5><i class="icon-user"></i> Profile</h5>
			                    </div>
			                    <div class="box-content">

			                      		<div class="span4 well">
											<div class="row">
												<div class="span3">
													<p><?php //echo $userGroup->name; ?></p>
										          	<p><strong><?php echo $user->display_name; ?></strong></p>
													<a href="<?php echo URL::action('AuthController@getLogout'); ?>"><span class=" badge badge-warning">Log Out</span></a>
												</div>
											</div>
										</div>

			                    </div>
							</div>
			        	</div>
			        <?php } ?>
        		<?php } ?>
        	<?php } ?>

			<?php if($user->hasAccess('dashboard.view-new-material')): ?>
			<div class="span6">
                <div class="box">
                    <div class="box-header">
                        <h5><i class="icon-flag"></i> New Material</h5>
                    </div>
                    <div class="box-content2">
                        <div class="left_box" >&nbsp;<i class="icon-chevron-left"></i> &nbsp;<strong><?php echo $number_imported_mat ?></strong>&nbsp;
                        	<i class="icon-chevron-right"></i>&nbsp; new <?php echo str_plural('material', $number_imported_mat) ?> to be set as product
                        </div>
                        <div class="right_box">
                        	<a href="<?php echo URL::to('supplychain/daily-sync'); ?>"><input type="button" class="btn btn-warning" value="Sync" ></a>
                        	<a href="<?php echo URL::to('products/new-material/index'); ?>"><input type="button" class="btn btn-info" value="Manage" ></a>
                        </div>
                    </div>
                </div>
            </div>
        	<?php endif ?>


        </div>

        <div class="clear"></div>

        <div class="row">
        	<?php if($user->hasAccess('dashboard.view-wait-approve-product')): ?>
        	 <div class="span6">
                <div class="box">
                    <div class="box-header">
                        <h5><i class="icon-pencil"></i> Waiting for approval products </h5>
                    </div>
                    <div class="box-content">

						    <div class="100percent">
							    <table class="table table-striped table-condensed">
								    <thead>
								    <tr>
									    <th style="width:80%"><?php echo Product::getLabel('title') ?></th>
									    <th>Last Updated</th>
								    </tr>
								    </thead>
								    <tbody>
								    	 <?php foreach ($pending_data_collection as $key => $Data) { ?>
										    <tr>
											    <td><?php echo $Data->title ?></td>
											    <?php if($Data->warning == 0){ ?>
											   		<td><a href="#" title="<?php echo $Data->updated_at ?>"><span class="label label-warning"><?php echo $Data->since; ?></span></a></td>
											    <?php } else { ?>
											    	<td><a href="#" title="<?php echo $Data->updated_at ?>"><span class="label label-important"><?php echo $Data->since; ?></span></a></td>
											    <?php } ?>
										    </tr>
									   	<?php } ?>
								    </tbody>
							    </table>
							    <div class="clear"></div>
							    <?php echo $pending_data->links(); ?>
							</div>


                    </div>
                </div>
            </div>
        	<?php endif ?>

        	<?php if($user->hasAccess('dashboard.view-rejected-product')): ?>
            <div class="span6">
                <div class="box">
                    <div class="box-header">

                        <h5><i class="icon-remove"></i> Rejected Products </h5>
                    </div>
                    <div class="box-content">

                       		 <div class="100percent">
							    <table class="table table-striped table-condensed">
								    <thead>
								    <tr>
									    <th style="width:45%"><?php echo Product::getLabel('title') ?></th>
									    <th  style="width:40%"><?php echo Product::getLabel('note') ?></th>
									    <th  style="width:15%">Last Updated</th>
								    </tr>
								    </thead>
								    <tbody>
								    	<?php foreach ($rejected_data_collection as $key => $data) { ?>
										    <tr>
												<td><?php echo $data->title ?></td>
												<td><?php echo nl2br($data->note) ?></td>
												<td><a href="#" title="<?php echo $data->updated_at ?>"><span class="label label-important"><?php echo $data->since; ?></span></a></td>
											</tr>
										<?php } ?>
								    </tbody>
							    </table>
							    <div class="clear"></div>
							    <?php echo $rejected_data->links(); ?>
							</div>

                    </div>
                </div>
            </div>
        	<?php endif ?>

        </div>

        <div class="clear"></div>

    </div>
</div>

