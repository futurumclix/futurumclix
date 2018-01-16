<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Search applications')?></h2>
	</div>
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
		<div class="form-group searchform col-sm-4">
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
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'Applicant')?></label>
			<?=
				$this->AdminForm->input('Filter.Applicant.username', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter applicant username to search'),
					'default' => isset($this->request->params['named']['Applicant.username']) ? $this->request->params['named']['Applicant.username'] : '',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'Advertisement Title')?></label>
			<?=
				$this->AdminForm->input('Filter.PaidOffer.title', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter advertisement title to search'),
					'default' => isset($this->request->params['named']['PaidOffer.title']) ? $this->request->params['named']['PaidOffer.title'] : '',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'URL')?></label>
			<?=
				$this->AdminForm->input('Filter.PaidOffer.url', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter URL to search'),
					'default' => isset($this->request->params['named']['PaidOffer.url']) ? $this->request->params['named']['PaidOffer.url'] : '',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'Status')?></label>
				<?=
					$this->AdminForm->input('Filter.PaidOffersApplication.status', array(
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
		<h2><?=__d('admin', 'Applications')?></h2>
	</div>
	<?=
		$this->AdminForm->create(false, array(
			'url' => array('action' => 'admin_applicationsMassaction'),
		))
	?>
		<div class="table-horizontal">
			<table class="table table-striped table-hover">
				<tbody>
					<tr>
						<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
						<th><?=__d('admin', 'Date')?></th>
						<th><?=__d('admin', 'Advertiser')?></th>
						<th><?=__d('admin', 'Applicant')?></th>
						<th><?=__d('admin', 'Title')?></th>
						<th><?=__d('admin', 'URL')?></th>
						<th><?=__d('admin', 'Value')?></th>
						<th><?=__d('admin', 'Status')?></th>
						<th><?=__d('admin', 'Actions')?></th>
					</tr>
					<?php foreach($applications as $app): ?>
						<tr>
							<td>
								<?=
									$this->AdminForm->checkbox('PaidOffersApplication.'.$app['PaidOffersApplication']['id'], array(
										'class' => 'ActionCheckbox',
									))
								?>
							</td>
							<td><?=$this->Time->nice($app['PaidOffersApplication']['created'])?></td>
							<td>
								<?php if(isset($app['PaidOffer']['Advertiser']['username'])): ?>
									<?=$this->Html->link($app['PaidOffer']['Advertiser']['username'], array('plugin' => null, 'controller' => 'users', 'action' => 'edit', $app['PaidOffer']['advertiser_id']))?>
								<?php else: ?>
									<?=__d('admin', 'Admin')?>
								<?php endif; ?>
							</td>
							<td>
								<?php if(isset($app['Applicant']['username'])): ?>
									<?=$this->Html->link($app['Applicant']['username'], array('plugin' => null, 'controller' => 'users', 'action' => 'edit', $app['PaidOffersApplication']['user_id']))?>
								<?php else: ?>
									<?=__d('admin', 'Deleted')?>
								<?php endif; ?>
							</td>
							<td><?=h($app['PaidOffer']['title'])?></td>
							<td><?=$this->Html->link($app['PaidOffer']['url'])?></td>
							<td><?=$this->Currency->format($app['PaidOffer']['value'])?></td>
							<td><?=h($app['PaidOffersApplication']['status_enum'])?></td>
							<td>
								<a data-toggle="modal" data-target="#ajax-modal-container" data-ajaxsource="<?=$this->Html->url(array('action' => 'applicationDetails', $app['PaidOffersApplication']['id']))?>"><i data-toggle="tooltip" title="<?=__d('admin', 'Show details of this application')?>" class="fa fa-info-circle"></i></a>
								<?=
									$this->AdminForm->postLink(
										'<i data-toggle="tooltip" title="'.__d('admin', 'Accept application').'" class="fa fa-thumbs-up"></i>',
										array('action' => 'applicationAccept', $app['PaidOffersApplication']['id']), 
										array('escape' => false), 
										__d('admin', 'Are you sure you want to accept %s?', $app['PaidOffersApplication']['id'])
									);
								?>
								<?=
									$this->AdminForm->postLink(
										'<i data-toggle="tooltip" title="'.__d('admin', 'Reject application').'" class="fa fa-thumbs-down"></i>',
										array('action' => 'applicationReject', $app['PaidOffersApplication']['id']), 
										array('escape' => false), 
										__d('admin', 'Are you sure you want to reject %s?', $app['PaidOffersApplication']['id'])
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
							'accept' => __d('admin', 'Accept applications'),
							'reject' => __d('admin', 'Reject applications'),
							'delete' => __d('admin', 'Delete applications'),
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

