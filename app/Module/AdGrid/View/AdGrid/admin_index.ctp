<div class="col-md-12">
	<div class="title">
		<h2><?=__d('ad_grid_admin', 'Search advertisements')?></h2>
	</div>
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('ad_grid_admin', 'Advertiser')?></label>
			<?=
				$this->AdminForm->input('Filter.AdGridAd.advertiser_name', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('ad_grid_admin', 'Enter username to search'),
					'default' => isset($this->request->params['named']['AdGridAd.advertiser_name']) ? $this->request->params['named']['AdGridAd.advertiser_name'] : '',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('ad_grid_admin', 'URL')?></label>
			<?=
				$this->AdminForm->input('Filter.AdGridAd.url', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('ad_grid_admin', 'Enter URL to search'),
					'default' => isset($this->request->params['named']['AdGridAd.url']) ? $this->request->params['named']['AdGridAd.url'] : '',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('ad_grid_admin', 'Status')?></label>
				<?=
					$this->AdminForm->input('Filter.AdGridAd.status', array(
						'options' => $statuses,
						'empty' => __d('ad_grid_admin', 'All'),
					))
				?>
		</div>
		<div class="col-sm-12 text-center">
			<button class="btn btn-primary btn-sm"><?=__d('ad_grid_admin', 'Search')?></button>
		</div>
	<?=$this->AdminForm->end()?>
	<div class="clearfix"></div>
	<div class="title">
		<h2><?=__d('ad_grid_admin', 'AdGrid ads advertisements')?></h2>
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
						<th><?=$this->Paginator->sort('advertiser_name', __d('ad_grid_admin', 'Advertiser'))?></th>
						<th><?=$this->Paginator->sort('url')?></th>
						<th><?=$this->Paginator->sort('total_clicks')?></th>
						<th><?=$this->Paginator->sort('expiry')?></th>
						<th><?=$this->Paginator->sort('status')?></th>
						<th><?=__d('ad_grid_admin', 'Actions')?></th>
					</tr>
					<?php foreach($ads as $ad): ?>
						<tr>
							<td>
								<?=
									$this->AdminForm->checkbox('AdGridAd.'.$ad['AdGridAd']['id'], array(
										'class' => 'ActionCheckbox',
									))
								?>
							</td>
							<td>
								<?php if($ad['AdGridAd']['advertiser_id']): ?>
									<?=$this->Html->link($ad['Advertiser']['username'], array('plugin' => null, 'controller' => 'users', 'action' => 'edit', $ad['AdGridAd']['advertiser_id']))?>
								<?php else: ?>
									<?=h($ad['AdGridAd']['advertiser_name'])?>
								<?php endif; ?>
							</td>
							<td><?=$this->Html->link($ad['AdGridAd']['url'])?></td>
							<td><?=h($ad['AdGridAd']['total_clicks'])?></td>
							<td>
								<?php if($ad['AdGridAd']['package_type'] == 'Days'): ?>
									<?=$this->Time->nice(strtotime("{$ad['AdGridAd']['start']} + {$ad['AdGridAd']['expiry']} day"))?>
								<?php elseif(empty($ad['AdGridAd']['package_type'])): ?>
									<?=__d('ad_grid_admin', 'Not assigned')?>
								<?php else: ?>
									<?=__d('ad_grid_admin', '%d %s', $ad['AdGridAd']['expiry'] - $ad['AdGridAd'][lcfirst($ad['AdGridAd']['package_type'])], lcfirst($ad['AdGridAd']['package_type']))?>
								<?php endif; ?>
							</td>
							<td><?=__d('ad_grid_admin', $ad['AdGridAd']['status'])?></td>
							<td>
								<?=
									$this->Html->link('<i class="fa fa-pencil fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('ad_grid_admin', 'Edit advertisement').'"></i>', array('action' => 'edit',  $ad['AdGridAd']['id']), array('escape' => false))
								?>
								<?=
									$this->AdminForm->postLink(
										'<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('ad_grid_admin', 'Inactivate advertisement').'"></i>',
										array('action' => 'inactivate', $ad['AdGridAd']['id']), 
										array('escape' => false), 
										__d('ad_grid_admin', 'Are you sure you want to inactivate "%s"?', $ad['AdGridAd']['url'])
									);
								?>
								<?=
									$this->AdminForm->postLink(
										'<i class="fa fa-check-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('ad_grid_admin', 'Activate advertisement').'"></i>',
										array('action' => 'activate', $ad['AdGridAd']['id']), 
										array('escape' => false), 
										__d('ad_grid_admin', 'Are you sure you want to activate "%s"?', $ad['AdGridAd']['url'])
									);
								?>
								<?=
									$this->AdminForm->postLink(
										'<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('ad_grid_admin', 'Delete advertisement').'"></i>',
										array('action' => 'delete', $ad['AdGridAd']['id']), 
										array('escape' => false), 
										__d('ad_grid_admin', 'Are you sure you want to delete "%s"?', $ad['AdGridAd']['url'])
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
				<label for="selectMassAction" class="input-group-addon"><?=__d('ad_grid_admin', 'Mass action')?></label>
				<?=
					$this->AdminForm->input('Action', array(
						'empty' => __d('ad_grid_admin', '--Choose--'),
						'required' => true,
						'id'=> 'selectMassAction',
						'options' => array(
							'activate' => __d('ad_grid_admin', 'Activate advertisements'),
							'inactivate' => __d('ad_grid_admin', 'Inactivate advertisements'),
							'delete' => __d('ad_grid_admin', 'Delete advertisements'),
						)
					))
				?>
				<div class="input-group-btn">
					<button class="btn btn-danger"><?=__d('ad_grid_admin', 'Perform action')?></button>
				</div>
			</div>
		</div>
	<?=$this->AdminForm->end()?>
	<div class="col-sm-7 text-right">
		<?=
			$this->Paginator->counter(array(
				'format' => __d('ad_grid_admin', 'Page {:page} of {:pages}')
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
			$this->Html->link(__d('ad_grid_admin', 'Add new advertisement'), array('action' => 'add'), array(
				'class' => 'btn btn-primary',
			))
		?>
	</div>
</div>
