<div class="col-md-12">
	<div class="title">
	<h2><?=__d('admin', 'Memberships')?></h2>
	</div>
	<?=
		$this->AdminForm->create(false, array(
			'url' => array('action' => 'massaction'),
		))
	?>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
					<th><?=$this->Paginator->sort('name', __d('admin', 'Membership name'))?></th>
					<th><?=$this->Paginator->sort('direct_referrals_limit')?></th>
					<th><?=$this->Paginator->sort('rented_referrals_limit')?></th>
					<th><?=$this->Paginator->sort('minimum_cashout')?></th>
					<th><?=$this->Paginator->sort('status')?></th>
					<th><?=__d('admin', 'Actions')?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($memberships as $membership): ?>
				<tr>
					<td>
						<?php if($membership['Membership']['status'] != 'Default'): ?>
						<?=
							$this->AdminForm->checkbox('Memberships.'.$membership['Membership']['id'], array(
								'value' => $membership['Membership']['id'],
								'class' => 'ActionCheckbox'
							))
						?>
						<?php endif; ?>
					</td>
					<td><?=h($membership['Membership']['name'])?></td>
					<td><?=$membership['Membership']['direct_referrals_limit'] == -1 ? __d('admin', 'Unlimited') : h($membership['Membership']['direct_referrals_limit'])?></td>
					<td><?=$membership['Membership']['rented_referrals_limit'] == -1 ? __d('admin', 'Unlimited') : h($membership['Membership']['rented_referrals_limit'])?></td>
					<td><?=h($membership['Membership']['minimum_cashout'])?></td>
					<td><?=$membership['Membership']['status']?></td>
					<td class="actions">
						<?=
							$this->Html->link('<i class="fa fa-pencil fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Edit membership').'"></i>',
								array('action' => 'edit', $membership['Membership']['id']),
								array('escape' => false)
							)
						?>
						<?php if($membership['Membership']['status'] != 'Default'): ?>
						<?=
							$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete membership').'"></i>',
								array('action' => 'delete', $membership['Membership']['id']),
								array('escape' => false),
								__d('admin', 'Are you sure you want to delete # %s?', $membership['Membership']['id'])
							)
						?>
						<?=
							$this->AdminForm->postLink('<i class="fa fa-check-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Make active').'"></i>',
								array('action' => 'activate', $membership['Membership']['id']),
								array('escape' => false),
								__d('admin', 'Are you sure you want to activate # %s?', $membership['Membership']['id'])
							)
						?>
						<?=
							$this->AdminForm->postLink('<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Make inactive').'"></i>',
								array('action' => 'disable', $membership['Membership']['id']),
								array('escape' => false),
								__d('admin', 'Are you sure you want to make inactive # %s?', $membership['Membership']['id'])
							)
						?>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="col-sm-5 text-left">
		<div class="input-group">
			<label for="selectMassAction" class="input-group-addon"><?=__d('admin', 'Mass Action')?></label>
			<?=
				$this->AdminForm->input('Action', array(
					'empty' => __d('admin', '--Choose--'),
					'required' => true,
					'id'=> 'selectMassAction',
					'options' => array(
						'activate' => __d('admin', 'Activate memberships'),
						'disable' => __d('admin', 'Disable memberships'),
						'delete' => __d('admin', 'Delete memberships'),
					)
				))
			?>
			<div class="input-group-btn">
				<button class="btn btn-danger"><?=__d('admin', 'Perform Action')?></button>
			</div>
		</div>
	</div>
	<div class="col-sm-7 text-right">
		<?=$this->Paginator->counter(['format' => __d('admin', 'Page {:page} of {:pages}')])?>
	</div>
	<div class="col-sm-12 text-center paddingten">
		<nav>
			<ul class="pagination pagination-sm">
				<?php
					echo $this->Paginator->first('&laquo;', array('tag' => 'li', 'escape' => false));
					echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'a'));
					echo $this->Paginator->last('&raquo;', array('tag' => 'li', 'escape' => false));
				?>
			</ul>
		</nav>
	</div>
	<div class="col-sm-12 text-center paddingten">
		<?=
			$this->Html->link(__d('admin', 'Add Membership'),
				array('action' => 'add'),
				array('class' => 'btn btn-primary')
			)
		?>
	</div>
	<?=$this->AdminForm->end()?>
</div>
