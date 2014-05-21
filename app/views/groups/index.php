<div class="mws-panel grid_8">

<?php echo Theme::widget('CMessage', array('messages' => $success, 'type' => 'success'))->render(); ?>

<?php echo Theme::widget('CMessage', array('messages' => $error, 'type' => 'error'))->render(); ?>

	<div class="mws-panel-header">
		<span><i class="icon-table"></i> Role list</span>
	</div>
	<div class="mws-panel-toolbar">
		<div class="btn-toolbar">
			<div class="btn-group">
				<a href="<?php echo URL::action('GroupsController@getCreate'); ?>" class="btn"><i class="icol-add"></i> Create group</a>
			</div>
		</div>
	</div>

	<div class="mws-panel-body no-padding">
		<table class="mws-datatable-fn mws-table" id="datatables_admin_default">
			<thead>
				<tr>
					<th>Name</th>
					<th>Users</th>
					<th>Role Permission</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($groups as $group): ?>
					<tr>
						<td><?php echo $group->name; ?></td>
						<td><?php echo $group->users_count; ?></td>
						<td><?php
								$permissions = $group->permissions;
								ksort($permissions);
								foreach($permissions as $perm => $v)
								{
									list($controller, $action) = explode(".", $perm);
									$pretty_action = str_replace("-"," ",$action);
									echo "User can \"{$pretty_action}\" {$controller}.<br>";
								}
							?>
						</td>
						<td>
							<?php echo HTML::buttonLink('Edit', URL::action( 'GroupsController@getEdit').'/'.$group->id, 'edit', '', array('class' => 'btn-small')); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>