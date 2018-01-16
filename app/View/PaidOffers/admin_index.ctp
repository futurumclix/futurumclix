<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Search offers')?></h2>
	</div>
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Advertiser')?></label>
			<?=
				$this->AdminForm->input('Filter.Advertiser.username', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter username to search'),
					'default' => isset($this->request->params['named']['Advertiser.username']) ? $this->request->params['named']['Advertiser.username'] : '',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Advertisement Title')?></label>
			<?=
				$this->AdminForm->input('Filter.PaidOffer.title', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter advertisement title to search'),
					'default' => isset($this->request->params['named']['PaidOffers.title']) ? $this->request->params['named']['PaidOffers.title'] : '',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'URL')?></label>
			<?=
				$this->AdminForm->input('Filter.PaidOffer.url', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter URL to search'),
					'default' => isset($this->request->params['named']['PaidOffers.url']) ? $this->request->params['named']['PaidOffers.url'] : '',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Status')?></label>
				<?=
					$this->AdminForm->input('Filter.PaidOffer.status', array(
						'options' => $statuses,
						'empty' => __d('admin', 'All'),
					))
				?>
		</div>
		<div class="col-sm-12 text-center">
			<button class="btn btn-primary btn-sm"><?=__d('admin', 'Search')?></button>
		</div>
	<?=$this->AdminForm->end()?>
	<div class="clearfix"></div>
	<div class="title">
		<h2><?=__d('admin', 'Paid Offers')?></h2>
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
						<th><?=$this->Paginator->sort('Advertiser.username', __d('admin', 'Advertiser'))?></th>
						<th><?=$this->Paginator->sort('title')?></th>
						<th><?=$this->Paginator->sort('url', __d('admin', 'URL'))?></th>
						<th><?=$this->Paginator->sort('value')?></th>
						<th><?=$this->Paginator->sort('approved_applications')?></th>
						<th><?=$this->Paginator->sort('pending_applications')?></th>
						<th><?=$this->Paginator->sort('slots_left')?></th>
						<th><?=$this->Paginator->sort('status')?></th>
						<th><?=__d('admin', 'Actions')?></th>
					</tr>
					<?php foreach($offers as $offer): ?>
						<tr>
							<td>
								<?=
									$this->AdminForm->checkbox('PaidOffer.'.$offer['PaidOffer']['id'], array(
										'class' => 'ActionCheckbox',
									))
								?>
							</td>
							<td>
								<?php if($offer['PaidOffer']['advertiser_id']): ?>
									<?=$this->Html->link($offer['Advertiser']['username'], array('controller' => 'users', 'action' => 'edit', $offer['PaidOffer']['advertiser_id']))?>
								<?php else: ?>
									<?=__d('admin', 'Admin')?>
								<?php endif; ?>
							</td>
							<td><?=$offer['PaidOffer']['title']?></td>
							<td><?=$offer['PaidOffer']['url']?></td>
							<td>
								<?php if($offer['PaidOffer']['value']): ?>
									<?=$this->Currency->format($offer['PaidOffer']['value'])?>
								<?php else: ?>
									<?=__d('admin', 'Not assigned')?>
								<?php endif ?>
							</td>
							<td><?=$offer['PaidOffer']['accepted_applications']?></td>
							<td><?=$offer['PaidOffer']['pending_applications']?></td>
							<td><?=$offer['PaidOffer']['slots_left']?></td>
							<td><?=$offer['PaidOffer']['status']?></td>
							<td>
								<?=
									$this->Html->link('<i class="fa fa-pencil fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Edit offer').'"></i>', array('action' => 'edit',  $offer['PaidOffer']['id']), array('escape' => false))
								?>
								<?=
									$this->AdminForm->postLink(
										'<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Inactivate offer').'"></i>',
										array('action' => 'inactivate', $offer['PaidOffer']['id']), 
										array('escape' => false), 
										__d('admin', 'Are you sure you want to inactivate "%s"?', $offer['PaidOffer']['title'])
									);
								?>
								<?=
									$this->AdminForm->postLink(
										'<i class="fa fa-check-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Activate offer').'"></i>',
										array('action' => 'activate', $offer['PaidOffer']['id']), 
										array('escape' => false), 
										__d('admin', 'Are you sure you want to activate "%s"?', $offer['PaidOffer']['title'])
									);
								?>
								<?=
									$this->AdminForm->postLink(
										'<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete offer').'"></i>',
										array('action' => 'delete', $offer['PaidOffer']['id']), 
										array('escape' => false), 
										__d('admin', 'Are you sure you want to delete "%s"?', $offer['PaidOffer']['title'])
									);
								?>
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
							'activate' => __d('admin', 'Activate offers'),
							'inactivate' => __d('admin', 'Inactivate offers'),
							'delete' => __d('admin', 'Delete offers'),
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
	<div class="col-sm-12 text-center paddingten">
		<?=
			$this->Html->link(__d('admin', 'Add New Advertisement'), array('action' => 'add'), array(
				'class' => 'btn btn-primary',
			))
		?>
	</div>
</div>
