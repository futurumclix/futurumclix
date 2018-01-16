<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Payouts list')?></h2>
	</div>
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'));?>
	<?=$this->AdminForm->input('page', array('value' => 1, 'type' => 'hidden'))?>
	<div class="form-group searchform col-sm-4">
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
			$this->AdminForm->input('Filter.Cashout.payment_account', array(
				'data-toggle' => 'tooltip',
				'data-placement' => 'top',
				'title' => __d('admin', 'Enter payment email to search'),
			))
		?>
	</div>
	<div class="form-group searchform col-sm-3">
		<label><?=__d('admin', 'Status')?></label>
		<?=
			$this->AdminForm->input('Filter.Cashout.status', array(
				'type' => 'select',
				'options' => $statuses,
				'empty' => __d('admin', 'Show all'),
			))
		?>
	</div>
	<div class="form-group searchform col-sm-1 text-right">
		<a style="position: relative; top: 30px;" data-toggle="collapse" href="#collapse1">
			<i id="collapse1Button" title="<?=__d('admin', 'Click to show more search options')?>" data-toggle="tooltip" data-placement="top" class="fa fa-plus-circle fa-lg"></i>
		</a>
	</div>
	<div id="collapse1" class="panel-collapse collapse <?=$searchCollapse?>">
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'From')?></label>
			<?=
				$this->AdminForm->input('Filter.Cashout.created >=', array(
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
				$this->AdminForm->input('Filter.Cashout.created <=', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Select to date'),
					'type' => 'datetime',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'Method')?></label>
			<?=
				$this->AdminForm->input('Filter.Cashout.gateway', array(
					'type' => 'select',
					'options' => $gateways,
					'empty' => __d('admin', 'Show all'),
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
					<th><?=$this->Paginator->sort('User.username', __d('admin', 'User'))?></th>
					<th><?=$this->Paginator->sort('User.location', __d('admin', 'Location'))?></th>
					<th><?=$this->Paginator->sort('created')?></th>
					<th><?=$this->Paginator->sort('payment_account')?></th>
					<th><?=$this->Paginator->sort('amount')?></th>
					<th><?=$this->Paginator->sort('gateway')?></th>
					<th><?=$this->Paginator->sort('status')?></th>
					<th><?=__d('admin', 'Actions')?></th>
				</tr>
				<?php foreach($cashouts as $cashout): ?>
				<tr>
					<td>
						<?=
							$this->AdminForm->checkbox('Cashout.'.$cashout['Cashout']['id'], array(
								'value' => $cashout['Cashout']['id'],
								'class' => 'ActionCheckbox',
							))
						?>
					</td>
					<?php if($cashout['User']['username']): ?>
						<td><?=$this->Html->link($cashout['User']['username'], array('controller' => 'users', 'action' => 'edit', $cashout['Cashout']['user_id']))?></td>
						<td><?=h($cashout['User']['location'])?></td>
					<?php else: ?>
						<td colspan="2" class="text-center"><?=__d('admin', 'User deleted')?></td>
					<?php endif; ?>
					<td><?=h($cashout['Cashout']['created'])?></td>
					<td><?=h($cashout['Cashout']['payment_account'])?></td>
					<td><?=$this->Currency->format($cashout['Cashout']['amount'])?></td>
					<td><?=h(PaymentsInflector::humanize(PaymentsInflector::underscore($cashout['Cashout']['gateway'])))?></td>
					<td><?=h($cashout['Cashout']['status'])?></td>
					<td>
						<?=
							$this->AdminForm->postLink(
								'<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Cancel request').'"></i>',
								array('action' => 'mark', $cashout['Cashout']['id'], 'Cancelled'),
								array('escape' => false),
								__d('admin', 'Are you sure you want to cancel # %s', $cashout['Cashout']['id'])
							)
						?>
						<?=
							$this->AdminForm->postLink(
								'<i class="fa fa-check-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Mark as paid').'"></i>',
								array('action' => 'mark', $cashout['Cashout']['id'], 'Completed'),
								array('escape' => false),
								__d('admin', 'Are you sure you want to mark as paid # %s', $cashout['Cashout']['id'])
							)
						?>
						<?=
							$this->AdminForm->postLink(
								'<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete request').'"></i>',
								array('action' => 'delete', $cashout['Cashout']['id']),
								array('escape' => false),
								__d('admin', 'Are you sure you want to delete # %s', $cashout['Cashout']['id'])
							)
						?>
						<?php if($cashout['Cashout']['status'] == 'New'): ?>
							<?=
								$this->AdminForm->postLink(
									'<i class="fa fa-money fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Pay this request').'"></i>',
									array('action' => 'cashout', $cashout['Cashout']['id']),
									array('escape' => false),
									__d('admin', 'Are you sure you want to pay # %s', $cashout['Cashout']['id'])
								)
							?>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="col-sm-5 text-left paddingten">
		<div class="input-group">
			<label for="selectMassAction" class="input-group-addon"><?=__d('admin', 'Mass Action')?></label>
			<?=
				$this->AdminForm->input('Action', array(
					'empty' => __d('admin', '--Choose--'),
					'required' => true,
					'id'=> 'selectMassAction',
					'options' => array(
						'markPaid' => __d('admin', 'Mark as Paid'),
						'markUnpaid' => __d('admin', 'Mark as New'),
						'delete' => __d('admin', 'Delete requests'),
						'cancel' => __d('admin', 'Cancel requests'),
						'cashout' => __d('admin', 'Automatically pay'),
						'list' => __d('admin', 'Download MassPay list(s)'),
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
</div>
<?php 
	$this->Js->buffer("
		setNavToggles('collapse1Button', 'collapse1');
	") 
?>
