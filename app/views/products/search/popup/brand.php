<div id="popup-manage-items-brand" class="popup-manage-items-wrap">
    
    <input type="hidden" id="type" name="" value="brand" />
    
    <div class="mws-panel-body no-padding">
        
        <?php echo Form::select('', $brands, null, array('id' => 'popup-manage-items-select-brand')); ?>
        
        <input type="button" class="btn btn-primary add" value="Add" />
        
    </div>
    
</div>