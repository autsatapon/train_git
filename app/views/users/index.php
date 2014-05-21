<div class="container">
    <div class="mws-panel grid_8">

    <?php echo Theme::widget('CMessage', array('messages' => $success, 'type' => 'success'))->render(); ?>

    <?php echo Theme::widget('CMessage', array('messages' => $error, 'type' => 'error'))->render(); ?>

        <div class="mws-panel-header">
            <span><i class="icon-table"></i> User list</span>
        </div>
        <div class="mws-panel-toolbar">
            <div class="btn-toolbar">
                <div class="btn-group">
                    <a href="<?php echo URL::action('UsersController@getCreate'); ?>" class="btn"><i class="icol-add"></i> Create user</a>
                </div>
            </div>
        </div>

        <div class="mws-panel-body no-padding">
            <table class="mws-datatable-fn mws-table" id="datatables_admin_default">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Permission</th>
                        <th>Last login</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user->display_name; ?></td>
                            <td><?php echo $user->email; ?></td>
                            <td><?php echo $user->group_name; ?></td>
                            <td><?php echo $user->app_name; ?></td>
                            <td><?php echo $user->last_login; ?></td>
                            <td>
                                <?php echo HTML::buttonLink('Edit', URL::action( 'UsersController@getEdit')."/".$user->id, 'edit', '', array('class' => 'btn-small')); ?>
                                <?php echo HTML::buttonLink('Edit role', URL::action( 'UsersController@getPerms')."/".$user->id, 'open', '', array('class' => 'btn-small')); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>