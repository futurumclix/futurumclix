<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Payouts list')?></h2>
	</div>
	<table class="table table-striped table-hover">
			<tbody>
				<tr>
					<th><?=__d('admin', 'Name')?></th>
					<th><?=__d('admin', 'Description')?></th>
					<th><?=__d('admin', 'Version')?></th>
					<th><?=__d('admin', 'Status')?></th>
					<th><?=__d('admin', 'Action')?></th>
				</tr>
				<?php foreach($modules as $module): ?>
					<tr>
						<td><?=$module['name']?></td>
						<td><?=$module['description']?></td>
						<td><?=$module['version']?></td>
						<td><?=$module['status']?></td>
						<td>
							<?php if($module['status'] == 'Active'): ?>
								<?=
									$this->AdminForm->postLink(
										'<i class="fa fa-minus fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Deactivate module').'"></i>',
										array('action' => 'deactivate', $module['name'], true), 
										array('escape' => false), 
										__d('admin', 'Are you sure you want to deactivate "%s"?', $module['name'])
									);
								?>
							<?php else: ?>
								<?=
									$this->AdminForm->postLink(
										'<i class="fa fa-plus fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Activate module').'"></i>',
										array('action' => 'activate', $module['name'], true), 
										array('escape' => false), 
										__d('admin', 'Are you sure you want to activate "%s"?', $module['name'])
									);
								?>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
	</table>
</div>
