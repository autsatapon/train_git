<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Banner Groups Management </span>
    </div>
    

    <form method="get" action="<?php echo url('banners/groups'); ?>" id="search-form" name="search-form" accept-charset="UTF-8">
        <div class="mws-form-inline" style="background-color:#DDD;border:1px solid #8C8C8C;overflow:hidden;padding:10px 0px 10px 0px;">
            <div class="mws-form-row" style="float:left;margin-left:20px;">
                <label for="product">Group Name</label>            
                <div class="mws-form-item">
                    <input name="search_name" type="text" id="search_name" value="<?php echo Input::get('search_name'); ?>">             
                </div>
            </div>
            <div class="mws-form-row" style="float:left;margin-left:20px;">
                <label for="product_line">Position</label> 
                <div class="mws-form-item">                    
                    <select name="banner_position_id" id="banner_position_id">
                        <option value="">All Groups</option>                    
                        <?php foreach (BannerPosition::where('status_flg', 'Y')->get() as $key => $position) : ?>
                        <option value="<?php echo $position['id']; ?>" <?php echo (Input::get('banner_position_id') == $position['id']) ? 'selected="selected"' : ""; ?>><?php echo $position['name']; ?></option>
                        <?php endforeach; ?>                                            
                    </select>                    
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
                <a class="btn" href="<?php echo URL::to('banners/groups/create') ?>"><i class="icol-add"></i> Create Groups</a>
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
                    <th>Position Name</th>
                    <th>Group Name</th>
                    <th style="width:135px;" class="no_sort">Pkey</th>                    
                    <th style="width:135px;" class="no_sort">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($groups as $key => $group) : ?>
                    <tr>
                        <td class="table-center"><?php echo ($key + 1); ?></td>
                        <td class="table-center"><?php echo $group->position->name; ?></td>
                        <td class="table-center"><a href="<?php echo url('banners?banner_group_id='.$group->id); ?>"><?php echo $group->name; ?></a></td>
                        <td class="table-center"><?php echo $group->pkey; ?></td>
                        <td class="table-center">
                            <a href="<?php echo URL::to("banners/groups/update/".$group->id) ?>">Edit</a> &nbsp; | &nbsp;
                            <a href="<?php echo URL::to('banners/groups/delete/'.$group->id); ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
		<?php echo $groups->appends(array('banner_position_id' => $banner_position_id, 'search_name' => $name))->links(); ?>
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
