<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'View User Items - user "%s"', $user['User']['username'])?></h2>
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
						<th><?=__d('admin', 'Item name')?></th>
						<th><?=$this->Paginator->sort('created', __d('admin', 'Purchase date'))?></th>
						<th><?=__d('admin', 'Action')?></th>
					</tr>
				</thead>
				<?php foreach($items as $item): ?>
					<tbody>
						<tr>
							<td>
								<?=
									$this->AdminForm->checkbox('BoughtItems.'.$item['BoughtItem']['id'], array(
										'value' => $item['BoughtItem']['id'],
										'class' => 'ActionCheckbox'
									))
								?>
							</td>
							<td><?=h($item['BoughtItem']['title'])?></td>
							<td><?=$this->Time->nice($item['BoughtItem']['created'])?></td>
							<td>
								<?=
									$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete').'"></i>',
										array('action' => 'delete', $item['BoughtItem']['id']),
										array('escape' => false),
										__d('admin', 'Are you sure you want to delete # %s?', $item['BoughtItem']['id'])
									)
								?>
							</td>
						</tr>
					</tbody>
				<?php endforeach; ?>
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
								'delete' => __d('admin', 'Delete'),
							)
						))
					?>
				<div class="input-group-btn">
					<button class="btn btn-danger"><?=__d('admin', 'Perform Action')?></button>
				</div>
			</div>
		</div>
	<?=$this->AdminForm->end()?>
	<div class="col-sm-7 text-right">
		<?=
			$this->Paginator->counter(array(
				'format' => __d('admin', 'Page {:page} of {:pages}')
			))
		?>
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
	<div class="text-center col-sm-12 paddingten">
		<?=$this->Html->link(__d('admin', 'Add new item'), array('action' => 'add', $user['User']['username']), array('class' => 'btn btn-primary'))?>
	</div>
</div>
