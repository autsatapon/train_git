<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span>Holidays</span>
    </div>
    
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a class="btn" href="/holidays/create"><i class="icol-add"></i> Create new Holiday</a>
            </div>
        </div>
    </div>
    
    <div class="mws-panel-body no-padding">
        
        <?php echo HTML::message(); ?>
        
        <form method="get" action="" class="mws-form" enctype="multipart/form-data">
            <div class="mws-button-row" style="text-align: center;">
                <?php echo Form::select('year', array_combine(range($now-5, $now+5), range($now-5, $now+5)),Input::get('year', $year)); ?>
                <?php echo Form::submit('Search', array('class' => 'btn btn-primary')); ?>
            </div>
        </form>
        
        <table class="mws-datatable-fn mws-table">
            
            <thead>
                <tr>
                    <th width=50%><?php echo __('Title'); ?></th>
                    <th width=30%><?php echo __('Period'); ?></th>
                    <th width=20%><?php echo __('Action'); ?></th>
                </tr>
            </thead>
            
            <tbody>
                <?php foreach ($holidays as $holiday): ?>
                <tr>
                    <td>
                        <h4><?php echo $holiday->title; ?></h4>
                        <p><?php echo $holiday->description; ?></p>
                    </td>
                    <td>
                        <strong><?php echo date('j F Y', strtotime($holiday->started_at)); ?></strong>
                        <em>to</em>
                        <strong><?php echo date('j F Y', strtotime($holiday->ended_at)); ?></strong>
                    </td>
                    <td>
                        <a href="/holidays/edit/<?php echo $holiday->getKey(); ?>" class="btn btn-info">Edit</a>
                        <a href="/holidays/delete/<?php echo $holiday->getKey(); ?>" class="btn btn-delete" onclick="return confirmDelete();">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            
        </table>
        
    </div>
</div>

<script>
function confirmDelete()
{
    return confirm('Please confirm this action as it could not be undone');
}
</script>