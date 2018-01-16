<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Purchases list')?></h2>
	</div>
	<?=
		$this->AdminForm->create(false, array(
			'url' => array('action' => 'purchases'),
			'class' => 'form-horizontal',
		))
	?>
	<div class="form-group searchform col-sm-3">
		<label><?=__d('admin', 'Username')?></label>
		<?=
			$this->AdminForm->input('Filter.User.username', array(
				'data-toggle' => 'tooltip',
				'data-placement' => 'top',
				'title' => __d('admin', 'Enter username to search'),
			))
		?>
	</div>
	<div class="form-group searchform col-sm-4">
		<label><?=__d('admin', 'Payment Account')?></label>
		<?=
			$this->AdminForm->input('Filter.Deposit.account', array(
				'data-toggle' => 'tooltip',
				'data-placement' => 'top',
				'title' => __d('admin', 'Enter payment email to search'),
			))
		?>
	</div>
	<div class="form-group searchform col-sm-4">
		<label><?=__d('admin', 'Method')?></label>
		<?=
			$this->AdminForm->input('Filter.Deposit.gateway', array(
				'options' => $gateways,
				'empty' => __d('admin', 'Select one'),
			));
		?>
	</div>
	<div class="form-group searchform col-sm-1 text-right">
		<a href="#collapse1" data-toggle="collapse" style="position: relative; top: 30px;">
			<i id="collapse1Button" title="<?=__d('admin', 'Click to show more search options')?>" data-toggle="tooltip" data-placement="top" class="fa fa-plus-circle fa-lg"></i>
		</a>
	</div>
	<div id="collapse1" class="panel-collapse collapse <?=$searchCollapse?>">
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Title')?></label>
			<?=
				$this->AdminForm->input('Filter.Deposit.item', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Select type'),
					'empty' => __d('admin', 'Select one'),
					'options' => $availableItems,
				));
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'From')?></label>
			<?=
				$this->AdminForm->input('Filter.Deposit.begins', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Select from date'),
					'type' => 'datetime',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'To')?></label>
			<?=
				$this->AdminForm->input('Filter.Deposit.ends', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Select to date'),
					'type' => 'datetime',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Payment ID')?></label>
			<?=
				$this->AdminForm->input('Filter.Deposit.gatewayid', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', "Put payment's ID"),
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
					<th><?=$this->Paginator->sort('user_id'); ?></th>
					<th><?=$this->Paginator->sort('gateway'); ?></th>
					<th><?=$this->Paginator->sort('amount'); ?></th>
					<th><?=$this->Paginator->sort('account'); ?></th>
					<th><?=$this->Paginator->sort('status'); ?></th>
					<th><?=$this->Paginator->sort('title'); ?></th>
					<th><?=$this->Paginator->sort('gatewayid', __d('admin', 'Payment ID')); ?></th>
					<th><?=$this->Paginator->sort('date'); ?></th>
					<th><?=__d('admin', 'Actions')?></th>
				</tr>
				<?php foreach ($deposits as $deposit): ?>
				<tr>
					<td>
						<?=
							$this->AdminForm->checkbox('Deposit.'.$deposit['Deposit']['id'], array(
								'value' => $deposit['Deposit']['id'],
								'class' => 'ActionCheckbox',
							))
						?>
					</td>
					<td>
						<?php if($deposit['User']['id'] != null): ?>
							<?=$this->Html->link($deposit['User']['username'], array('controller' => 'users', 'action' => 'edit', $deposit['User']['id'])); ?>
						<?php else: ?>
							<?=__d('admin', 'Anonymous / Deleted User')?>
						<?php endif; ?>
					</td>
					<td><?=h(PaymentsInflector::humanize(PaymentsInflector::underscore($deposit['Deposit']['gateway']))); ?>&nbsp;</td>
					<td><?=h($this->Currency->format($deposit['Deposit']['amount'])); ?>&nbsp;</td>
					<td><?=h($deposit['Deposit']['account']); ?>&nbsp;</td>
					<td><?=__d('admin', h($deposit['Deposit']['status'])); ?>&nbsp;</td>
					<td><?=h($deposit['Deposit']['title']); ?>&nbsp;</td>
					<td><?=h($deposit['Deposit']['gatewayid']); ?>&nbsp;</td>
					<td><?=h($deposit['Deposit']['date']); ?>&nbsp;</td>
					<td class="actions">
						<?=
							$this->AdminForm->postLink(
								'<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete').'"></i>',
								array('action' => 'delete', $deposit['Deposit']['id']),
								array('escape' => false), 
								__d('admin', 'Are you sure you want to delete # %s?', $deposit['Deposit']['id'])
							);
						?>
						<?=
							$this->Html->link('<i class="fa fa-pencil fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Edit').'"></i>',
								array('action' => 'edit', $deposit['Deposit']['id']),
								array('escape' => false)
							)
						?>
						<?php if($deposit['Deposit']['status'] == 'Pending'): ?>
							<?=
								$this->AdminForm->postLink(
									'<i class="fa fa-plus fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Accept payment').'"></i>',
									array('action' => 'accept', $deposit['Deposit']['id']), 
									array('escape' => false), 
									__d('admin', 'Are you sure you want to accept # %s?', $deposit['Deposit']['id'])
								);
							?>
						<?php endif; ?>
						<?php if($deposit['Deposit']['refunds'] && $deposit['User']['id'] && $deposit['Deposit']['status'] != 'Refunded'): ?>
							<?=
								$this->AdminForm->postLink(
									'<i class="fa fa-refresh fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Refund').'"></i>',
									array('action' => 'refund', $deposit['Deposit']['id']), 
									array('escape' => false), 
									__d('admin', 'Are you sure you want to refund # %s?', $deposit['Deposit']['id'])
								);
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
						'delete' => __d('admin', 'Delete'),
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
