<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Commissions list')?></h2>
	</div>
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'));?>
	<div class="form-group searchform col-sm-3">
		<label><?=__d('admin', 'Username')?></label>
		<?=
			$this->AdminForm->input('Filter.Upline.username', array(
				'data-toggle' => 'tooltip',
				'data-placement' => 'top',
				'title' => __d('admin', 'Enter username to search'),
				'default' => isset($this->request->params['named']['Upline.username']) ? $this->request->params['named']['Upline.username'] : '',
			))
		?>
	</div>
	<div class="form-group searchform col-sm-4">
		<label><?=__d('admin', 'Referral')?></label>
		<?=
			$this->AdminForm->input('Filter.Referral.username', array(
				'data-toggle' => 'tooltip',
				'data-placement' => 'top',
				'title' => __d('admin', 'Enter referal name to search'),
				'default' => isset($this->request->params['named']['Referral.username']) ? $this->request->params['named']['Referral.username'] : '',
			))
		?>
	</div>
	<div class="form-group searchform col-sm-4">
		<label><?=__d('admin', 'Item')?></label>
		<?=
			$this->AdminForm->input('Filter.Deposit.name', array(
				'type' => 'select',
				'options' => $availableItems,
				'empty' => __d('admin', 'Please select'),
			))
		?>
	</div>
	<div class="form-group searchform col-sm-1 text-right">
		<a href="#collapse1" data-toggle="collapse" style="position: relative; top: 30px;">
			<i id="collapse1Button" title="<?=__d('admin', 'Click to show more search options')?>" data-toggle="tooltip" data-placement="top" class="fa fa-plus-circle fa-lg"></i>
		</a>
	</div>
	<div id="collapse1" class="panel-collapse collapse <?=$searchCollapse?>">
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'From')?></label>
			<?=
				$this->AdminForm->input('Filter.Deposit.date >=', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Select from date'),
					'type' => 'datetime',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'To')?></label>
			<?=
				$this->AdminForm->input('Filter.Deposit.date <=', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Select to date'),
					'type' => 'datetime',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'Status')?></label>
			<?=
				$this->AdminForm->input('Filter.Commission.status', array(
					'options' => $statuses,
					'empty' => __d('admin', 'Please select'),
				))
			?>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="text-center col-md-12 paddingten">
		<button class="btn btn-primary btn-sm"><?=_('Search')?></button>
	</div>
	<?=$this->AdminForm->end()?>
	<?=
		$this->AdminForm->create(false, array(
			'url' => array('action' => 'massaction'),
		))
	?>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<tbody>
				<tr>
					<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
					<th><?=$this->Paginator->sort('Deposit.date', __d('admin', 'Purchase Date'))?></th>
					<th><?=$this->Paginator->sort('credit_date')?></th>
					<th><?=$this->Paginator->sort('Upline.username', __d('admin', 'Username'))?></th>
					<th><?=$this->Paginator->sort('Referral.username', __d('admin', 'Referral'))?></th>
					<th><?=$this->Paginator->sort('Deposit.title', __d('admin', 'Item'))?></th>
					<th><?=$this->Paginator->sort('amount')?></th>
					<th><?=$this->Paginator->sort('status')?></th>
					<th><?=__d('admin', 'Actions')?></th>
				</tr>
				<?php foreach($commissions as $commission): ?>
				<tr>
					<td>
						<?=
							$this->AdminForm->checkbox('Commission.'.$commission['Commission']['id'], array(
								'value' => $commission['Commission']['id'],
								'class' => 'ActionCheckbox',
							))
						?>
					</td>
					<td><?=h($commission['Deposit']['date'])?></td>
					<td><?=h($commission['Commission']['credit_date'])?></td>
					<td>
						<?php if($commission['Upline']['username']): ?>
							<?=$this->Html->link($commission['Upline']['username'], array('controller' => 'users', 'action' => 'edit', $commission['Commission']['upline_id']))?>
						<?php else: ?>
							<?=__d('admin', 'User deleted')?>
						<?php endif; ?>
					</td>
					<td>
						<?php if($commission['Referral']['username']): ?>
							<?=$this->Html->link($commission['Referral']['username'], array('controller' => 'users', 'action' => 'edit', $commission['Commission']['referral_id']))?>
						<?php else: ?>
							<?=__d('admin', 'User deleted')?>
						<?php endif; ?>
					</td>
					<td><?=h($commission['Deposit']['title'])?></td>
					<td><?=h($this->Currency->format($commission['Commission']['amount']))?></td>
					<td><?=h($commission['Commission']['status'])?></td>
					<td class="actions">
						<?=
							$this->AdminForm->postLink(
								'<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete commission').'"></i>',
								array('action' => 'delete', $commission['Commission']['id']), 
								array('escape' => false), 
								__d('admin', 'Are you sure you want to delete # %s?', $commission['Commission']['id'])
							);
						?>
						<?php if($commission['Commission']['status'] == 'Credited'): ?>
							<?=
								$this->AdminForm->postLink(
									'<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete commission and remove cash from account').'"></i>',
									array('action' => 'delete', $commission['Commission']['id'], true), 
									array('escape' => false), 
									__d('admin', 'Are you sure you want to delete # %s?', $commission['Commission']['id'])
								);
							?>
						<?php else: ?>
						<?=
							$this->AdminForm->postLink(
								'<i class="fa fa-money fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Pay').'"></i>',
								array('action' => 'credit', $commission['Commission']['id']),
								array('escape' => false),
								__d('admin', 'Are you sure you want to pay # %s', $commission['Commission']['id'])
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
						'credit' => __d('admin', 'Set as credited'),
						'delete' => __d('admin', 'Delete'),
						'deleteAndCancel' => __d('admin', 'Delete and remove cash'),
					)
				))
			?>
			<div class="input-group-btn">
				<button class="btn btn-danger"><?=__d('admin', 'Perform Action')?></button>
			</div>
		</div>
	</div>
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
	<?=$this->AdminForm->end()?>
</div>
<?php 
	$this->Js->buffer("
		setNavToggles('collapse1Button', 'collapse1');
	") 
?>
