<div class="col-md-12">
	<div class="title">
		<h2><?=__d('offerwalls_admin', 'Postback log')?></h2>
	</div>
	<?=
		$this->AdminForm->create(false, array(
			'class' => 'form-horizontal',
		))
	?>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('offerwalls_admin', 'Username')?></label>
			<?=
				$this->AdminForm->input('Filter.User.username', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('offerwalls_admin', 'Enter username to search'),
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('offerwalls_admin', 'Offer ID')?></label>
			<?=
				$this->AdminForm->input('Filter.OfferwallsOffer.transactionid', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('offerwalls_admin', 'Enter offer ID to search'),
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('offerwalls_admin', 'Offerwall')?></label>
			<?=$this->AdminForm->input('Filter.OfferwallsOffer.offerwall', array('empty' => __d('offerwalls_admin', 'Please select...')))?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('offerwalls_admin', 'Status')?></label>
			<?=$this->AdminForm->input('Filter.OfferwallsOffer.status', array('empty' => __d('offerwalls_admin', 'Please select...'), 'options' => $this->Utility->enum('Oferwalls.OfferwallsOffer', 'status')))?>
		</div>
		<div class="col-sm-12 text-center paddingten">
			<button class="btn btn-primary"><?=__d('offerwalls_admin', 'Search')?></button>
		</div>
	<?=$this->AdminForm->end()?>
	<div class="clearfix"></div>
	<div class="title2">
		<h2><?=__d('offerwalls_admin', 'Detailed Log')?></h2>
	</div>
	<?=
		$this->AdminForm->create(false, array(
			'url' => array('action' => 'massaction'),
		))
	?>
	<div class="table-horizontal">
		<table class="table table-striped table-hover">
			<tbody>
				<tr>
					<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
					<th><?=$this->Paginator->sort('User.username', __d('offerwalls_admin', 'Username'))?></th>
					<th><?=$this->Paginator->sort('amount')?></th>
					<th><?=$this->Paginator->sort('complete_date', __d('offerwalls_admin', 'Date completed'))?></th>
					<th><?=$this->Paginator->sort('credit_date', __d('offerwalls_admin', 'Date credited'))?></th>
					<th><?=$this->Paginator->sort('offerwall')?></th>
					<th><?=$this->Paginator->sort('transactionid', __d('offerwalls_admin', 'ID'))?></th>
					<th><?=$this->Paginator->sort('status')?></th>
					<th><?=__d('offerwalls_admin', 'Actions')?></th>
				</tr>
				<?php foreach($offers as $offer): ?>
					<tr>
						<td>
							<?=
								$this->AdminForm->checkbox('OfferwallsOffer.'.$offer['OfferwallsOffer']['id'], array(
									'value' => $offer['OfferwallsOffer']['id'],
									'class' => 'ActionCheckbox'
								))
							?>
						</td>
						<td>
							<?php if($offer['User']['username']): ?>
								<?=$this->Html->link($offer['User']['username'], array('plugin' => null, 'controller' => 'users', 'action' => 'edit', $offer['OfferwallsOffer']['user_id']))?>
							<?php else: ?>
								<?=__d('offerwalls_admin', 'Deleted')?>
							<?php endif; ?>
						</td>
						<td><?=h($offer['OfferwallsOffer']['amount'])?></td>
						<td><?=$this->Time->nice($offer['OfferwallsOffer']['complete_date'])?></td>
						<td>
							<?php if($offer['OfferwallsOffer']['credit_date']): ?>
								<?=$this->Time->nice($offer['OfferwallsOffer']['credit_date'])?>
							<?php else: ?>
								<?=__d('offerwalls_admin', 'Not yet')?>
							<?php endif; ?>
						</td>
						<td><?=h($offer['OfferwallsOffer']['offerwall'])?></td>
						<td><?=h($offer['OfferwallsOffer']['transactionid'])?></td>
						<td><?=$this->Utility->enum('Offerwalls.OfferwallsOffer', 'status', $offer['OfferwallsOffer']['status'])?></td>
						<td>
							<?=
								$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('offerwalls_admin', 'Delete').'"></i>',
									array('action' => 'delete', $offer['OfferwallsOffer']['id']),
									array('escape' => false),
									__d('offerwalls_admin', 'Are you sure you want to delete # %s?', $offer['OfferwallsOffer']['id'])
								)
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<div class="col-sm-5 text-left">
			<div class="input-group">
				<label for="selectMassAction" class="input-group-addon"><?=__d('offerwalls_admin', 'Mass action')?></label>
				<?=
					$this->AdminForm->input('Action', array(
						'empty' => __d('offerwalls_admin', '--Choose--'),
						'required' => true,
						'id'=> 'actionSelect',
						'options' => array(
							'delete' => __d('offerwalls_admin', 'Delete'),
						)
					))
				?>
				<div class="input-group-btn">
					<button class="btn btn-danger"><?=__d('offerwalls_admin', 'Perform action')?></button>
				</div>
			</div>
		</div>
		<?=$this->AdminForm->end()?>
		<div class="col-sm-7 text-right">
			<?=$this->Paginator->counter(array('format' => __d('offerwalls_admin', 'Page {:page} of {:pages}')))?>
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
</div>
