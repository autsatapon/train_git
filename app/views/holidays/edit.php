<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>Create Holiday</span>
    </div>
    <div class="mws-panel-body no-padding">
        
        <?php echo HTML::message(); ?>
        
        <form method="post" action="" class="mws-form" enctype="multipart/form-data">
            <div class="mws-form-inline">
                
                <div class="mws-form-row">
                    <label class="mws-form-label" for="title">Title</label>
                    <div class="mws-form-item">
                        <?php echo Form::text('title', $holiday['title'], array('id' => 'title', 'class' => 'small')); ?>
                    </div>
                </div>
                
                <div class="mws-form-row">
                    <label class="mws-form-label" for="description">Description</label>
                    <div class="mws-form-item">
                        <?php echo Form::textarea('description', $holiday['description'], array('id' => 'description', 'class' => 'small')); ?>
                    </div>
                </div>
                
                <div class="mws-form-row">
                    <label class="mws-form-label" for="started_at">Period</label>
                    <div class="mws-form-item">
                        <?php echo Form::text('started_at', $holiday['started_at'], array('id' => 'started_at', 'class' => 'ssmall datepicker')); ?>
                        To
                        <?php echo Form::text('ended_at', $holiday['ended_at'], array('id' => 'ended_at', 'class' => 'ssmall datepicker')); ?>
                    </div>
                </div>
                
            </div>
            
            <div class="mws-button-row">
                <input type="submit" class="btn btn-primary" value="Save">
                <a href="/holidays" class="btn">Cancel</a>
            </div>
            
        </form>        
        
    </div>
</div>
