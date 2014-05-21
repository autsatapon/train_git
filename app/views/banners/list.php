<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Banner Management </span>
    </div>
    

    <form method="get" action="<?php echo url('banners?banner_group_id='.$banner_group_id); ?>" id="search-form" name="search-form" accept-charset="UTF-8">
        <div class="mws-form-inline" style="background-color:#DDD;border:1px solid #8C8C8C;overflow:hidden;padding:10px 0px 10px 0px;">
            <div class="mws-form-row" style="float:left;margin-left:20px;">
                <label for="product_line">Position</label> 
                <div class="mws-form-item">                    

                    <select name="banner_position_id" id="banner_position_id">
                        <option value="">All Positions</option>                    
                        <?php foreach (BannerPosition::where('status_flg', 'Y')->get() as $key => $position) : ?>
                        <option value="<?php echo $position['id']; ?>" <?php echo ($banner_position_id == $position['id']) ? 'selected="selected"' : ""; ?>><?php echo $position['name']; ?></option>
                        <?php endforeach; ?>                                            
                        

                    </select>                    
                </div>
            </div>    
            <div class="mws-form-row" style="float:left;margin-left:20px;">
                <label for="product">Groups</label>            
                <div class="mws-form-item">
                    
                    <select name="banner_group_id" id="banner_group_id">
                        <option value="">All Groups</option>                        
                        <?php if ( ! empty($banner_groups)) : ?>
                        <?php foreach ($banner_groups as $key => $group) : ?>
                        <option value="<?php echo $group['id']; ?>" <?php echo ($banner_group_id == $group['id']) ? 'selected="selected"' : ""; ?>><?php echo $group['name']; ?></option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="mws-form-row" style="float:left; margin-left:20px;">
                <label for="keyword">Keyword</label>
                <div class="mws-form-item">
                    <?php echo Form::text('keyword', Input::get('keyword'), array('class' => 'small', 'id' => 'keyword')); ?>
                </div>
            </div>     
            <div class="clear">&nbsp;</div>              
            <div class="mws-button-row" style="float:left;margin-left:20px;padding-top:22px;">
                <input class="btn btn-primary" type="submit" id="search-button" name="search-button" value="Search">            
                <input class="btn btn-primary" type="button" id="reset-button" name="reset-button" value="Reset">            
            </div>
        </div>
    </form>
	
     <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a class="btn" href="<?php echo URL::to('banners/create/'.$banner_group_id); ?>"><i class="icol-add"></i> Create Banner</a>
            </div>
        </div>
    </div>

    <div class="mws-panel-body no-padding">
		<?php if (Session::has('success')) : ?>
			<div class="alert alert-success">
				<span style="display:block;"><?php echo Session::get('success') ?></span>
			</div>
		<?php endif; ?>
		<?php if ($errors->count() > 0) : ?>
			<div class="alert alert-error">
				<?php foreach ($errors->all() as $error) : ?>
					<span style="display:block;"><?php echo $error; ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		
	</div>
	<div class="mws-panel-body no-padding dataTables_wrapper">
        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th style="width:30px;">No</th>
                    <th>Position</th>
                    <th>Group</th>
                    <th>Banner Title</th>
                    <th>Banner Type</th>
                    <th style="width:135px;" class="no_sort">Pkey</th>                    
                    <th style="width:135px;" class="no_sort">Expiry</th>
                    <th style="width:80px;" class="no_sort">Publish Status</th>
                    <th style="width:135px;" class="no_sort">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($banners as $key => $banner) : ?>
                    <tr>
                        <td class="table-center"><?php echo ($key + 1); ?></td>
                        <td class="table-center"><?php echo $banner->groups->position->name; ?></td>
                        <td class="table-center"><?php echo $banner->groups->name; ?></td>
                        <td class="table-center"><?php echo $banner->name; ?></td>
                        <td class="table-center"><?php echo $banner_type[$banner->type]; ?></td>                            
                        <td class="table-center"><?php echo $banner->pkey; ?></td>
                        <td class="table-center"><?php echo $banner->expired_at; ?></td>
                        <td class="table-center"><?php echo $banner->status_flg; ?></td>
                        <td class="table-center">
                            <a href="<?php echo URL::to("banners/update/".$banner->id) ?>">Edit</a> &nbsp; | &nbsp; 
                            <a href="<?php echo URL::to('banners/delete/'.$banner->id); ?>" class="delete-button">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
		<?php echo $banners->appends(array('banner_group_id' => $banner_group_id))->links(); ?>	
    </div>
	
    
</div>


