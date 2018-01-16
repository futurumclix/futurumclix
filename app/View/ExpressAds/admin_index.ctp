<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Search advertisements')?></h2>
	</div>
	<?=
		$this->AdminForm->create(false, array(
			'class' => 'form-horizontal',
		))
	?>
	<div class="form-group searchform col-sm-3">
		<label><?=__d('admin', 'Advertiser')?></label>
		<?=
			$this->AdminForm->input('Filter.ExpressAd.advertiser_name', array(
				'data-toggle' => 'tooltip',
				'data-placement' => 'top',
				'title' => __d('admin', 'Enter advertiser username to search'),
				'default' => isset($this->request->params['named']['ExpressAd.advertiser_name']) ? $this->request->params['named']['ExpressAd.advertiser_name'] : '',
			))
		?>
	</div>
	<div class="form-group searchform col-sm-4">
		<label><?=__d('admin', 'Advertisement Title')?></label>
		<?=
			$this->AdminForm->input('Filter.ExpressAd.title', array(
				'data-toggle' => 'tooltip',
				'data-placement' => 'top',
				'title' => __d('admin', 'Enter advertisement title to search'),
				'default' => isset($this->request->params['named']['ExpressAd.title']) ? $this->request->params['named']['ExpressAd.title'] : '',
			))
		?>
	</div>
	<div class="form-group searchform col-sm-4">
		<label><?=__d('admin', 'URL')?></label>
		<?=
			$this->AdminForm->input('Filter.ExpressAd.url', array(
				'data-toggle' => 'tooltip',
				'data-placement' => 'top',
				'title' => __d('admin', 'Enter url to search'),
				'default' => isset($this->request->params['named']['ExpressAd.url']) ? $this->request->params['named']['ExpressAd.url'] : '',
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
			<label><?=__d('admin', 'Status')?></label>
			<?=
				$this->AdminForm->input('Filter.ExpressAd.status', array(
					'type' => 'select',
					'empty' => '----',
				))
			?>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="text-center col-md-12">
		<button class="btn btn-primary btn-sm"><?=_('Search')?></button>
	</div>
	<?=$this->AdminForm->end()?>
	<div class="title col-md-12">
		<h2><?=__d('admin', 'Express Advertisements')?></h2>
	</div>
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
					<th><?=$this->Paginator->sort('advertiser_name', __d('admin', 'Advertiser'))?></th>
					<th><?=$this->Paginator->sort('title')?></th>
					<th><?=$this->Paginator->sort('url')?></th>
					<th><?=$this->Paginator->sort('type')?></th>
					<th><?=$this->Paginator->sort('expiry')?></th>
					<th><?=$this->Paginator->sort('clicks')?></th>
					<th><?=$this->Paginator->sort('outside_clicks')?></th>
					<th><?=$this->Paginator->sort('hide_referer')?></th>
					<th><?=$this->Paginator->sort('status')?></th>
					<th><?=__d('admin', 'Actions')?></th>
				</tr>
				<?php foreach($ads as $ad): ?>
				<tr>
					<td>
						<?=
							$this->AdminForm->checkbox('ExpressAds.'.$ad['ExpressAd']['id'], array(
								'class' => 'ActionCheckbox'
							))
						?>
					</td>
					<td>
						<?php
							if($ad['ExpressAd']['advertiser_id'])
								echo $this->Html->link($ad['Advertiser']['username'], array('controller' => 'users', 'action' => 'edit', $ad['Advertiser']['id']));
							else
								echo h($ad['ExpressAd']['advertiser_name']);
						?>
					</td>
					<td><?=h($ad['ExpressAd']['title'])?></td>
					<td><?=$this->Html->link($ad['ExpressAd']['url'])?></td>
					<td><?=h($ad['ExpressAd']['package_type'])?></td>
					<td>
						<?php if($ad['ExpressAd']['package_type'] == 'Days'): ?>
							<?=$ad['ExpressAd']['expiry_date'] ? $this->Time->nice($ad['ExpressAd']['expiry_date']) : 0 ?>
						<?php else: ?>
							<?=h($ad['ExpressAd']['expiry'])?>
						<?php endif; ?>
					</td>
					<td><?=h($ad['ExpressAd']['clicks'])?></td>
					<td><?=h($ad['ExpressAd']['outside_clicks'])?></td>
					<td><?=$ad['ExpressAd']['hide_referer'] ? __d('admin', 'Yes') : __d('admin', 'No')?></td>
					<td><?=h($ad['ExpressAd']['status'])?></td>
					<td>
						<?=
							$this->Html->link('<i class="fa fa-pencil fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Edit advertisement').'"></i>',
								array('action' => 'edit', $ad['ExpressAd']['id']),
								array('escape' => false)
							)
						?>
						<?=
							$this->AdminForm->postLink('<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Inactivate advertisement').'"></i>',
								array('action' => 'inactivate', $ad['ExpressAd']['id']),
								array('escape' => false),
								__d('admin', 'Are you sure you want to inactivate # %s?', $ad['ExpressAd']['id'])
							)
						?>
						<?=
							$this->AdminForm->postLink('<i class="fa fa-check-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Activate advertisement').'"></i>',
								array('action' => 'activate', $ad['ExpressAd']['id']),
								array('escape' => false),
								__d('admin', 'Are you sure you want to activate # %s?', $ad['ExpressAd']['id'])
							)
						?>
						<?=
							$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete advertisement').'"></i>',
								array('action' => 'delete', $ad['ExpressAd']['id']),
								array('escape' => false),
								__d('admin', 'Are you sure you want to delete # %s?', $ad['ExpressAd']['id'])
							)
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
						'activate' => __d('admin', 'Activate advertisements'),
						'inactivate' => __d('admin', 'Inactivate advertisements'),
						'delete' => __d('admin', 'Delete advertisements'),
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
	<div class="col-sm-12 text-center paddingten">
		<?=
			$this->Html->link(__d('admin', 'Add New Advertisement'), array('action' => 'add'), array(
				'class' => 'btn btn-primary',
			))
		?>
	</div>
</div>
<?=$this->AdminForm->end()?>
<?php $this->Js->buffer("setNavToggles('collapse1Button', 'collapse1');") ?>
