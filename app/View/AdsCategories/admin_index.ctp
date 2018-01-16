<!-- Begin of paid to click ads list -->
<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Paid To Click categories')?></h2>
	</div>
	<?=
		$this->AdminForm->create(false, array(
			'url' => array('action' => 'massaction'),
			'class' => 'form-horizontal',
		))
		?>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<tbody>
				<tr>
					<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
					<th><?=$this->Paginator->sort('name', __d('admin', 'Category Name'))?></th>
					<th><?=$this->Paginator->sort('time', __d('admin', 'Timer'))?></th>
					<th><?=$this->Paginator->sort('allow_description', __d('admin', 'Show Description'))?></th>
					<th><?=$this->Paginator->sort('geo_targetting')?></th>
					<th><?=$this->Paginator->sort('referrals_earnings')?></th>
					<th><?=$this->Paginator->sort('status')?></th>
					<th><?=__d('admin', 'Actions')?></th>
				</tr>
				<?php foreach($adsCategories as $adsCategory): ?>
				<tr>
					<td>
						<?=
							$this->AdminForm->checkbox('AdsCategories.'.$adsCategory['AdsCategory']['id'], array(
								'value' => $adsCategory['AdsCategory']['id'],
								'class' => 'ActionCheckbox'
							))
							?>
					</td>
					<td><?=h($adsCategory['AdsCategory']['name'])?></td>
					<td><?=h($adsCategory['AdsCategory']['time']).__d('admin', ' seconds')?></td>
					<td><?=h($adsCategory['AdsCategory']['allow_description'] ? __d('admin', 'Yes') : __d('admin', 'No'))?></td>
					<td><?=h($adsCategory['AdsCategory']['geo_targetting'] ? __d('admin', 'Yes') : __d('admin', 'No'))?></td>
					<td><?=h($adsCategory['AdsCategory']['referrals_earnings'] ? __d('admin', 'Yes') : __d('admin', 'No'))?></td>
					<td><?=h($adsCategory['AdsCategory']['status'])?></td>
					<td>
						<?=
							$this->Html->link('<i class="fa fa-pencil fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Edit category').'"></i>',
								array('action' => 'edit', $adsCategory['AdsCategory']['id']),
								array('escape'=> false)
							)
						?>
						<?=
							$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete category').'"></i>',
								array('action' => 'delete', $adsCategory['AdsCategory']['id']),
								array('escape' => false),
								__d('admin', 'Are You sure you want to delete # %s', $adsCategory['AdsCategory']['id'])
							)
						?>
						<?=
							$this->AdminForm->postLink('<i class="fa fa-check-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Enable').'"></i>',
								array('action' => 'enable', $adsCategory['AdsCategory']['id']),
								array('escape' => false),
								__d('admin', 'Are You sure you want to enable # %s', $adsCategory['AdsCategory']['id'])
							)
						?>
						<?=
							$this->AdminForm->postLink('<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Disable').'"></i>',
								array('action' => 'disable', $adsCategory['AdsCategory']['id']),
								array('escape' => false),
								__d('admin', 'Are You sure you want to disable # %s', $adsCategory['AdsCategory']['id'])
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
						'delete' => __d('admin', 'Delete categories'),
						'enable' => __d('admin', 'Enable categories'),
						'disable' => __d('admin', 'Disable categories'),
					)
				))
			?>
			<div class="input-group-btn">
				<button class="btn btn-danger"><?=__d('admin', 'Perform Action')?></button>
			</div>
		</div>
	</div>
	<div class="col-sm-7 text-right">
		<?=$this->Paginator->counter(array('format' => __d('admin', 'Page {:page} of {:pages}')))?>
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
			$this->Html->link(__d('admin', 'Add new category'),
				array('action' => 'add'),
				array('class' => 'btn btn-primary')
			)
		?>
	</div>
</div>
<?=$this->AdminForm->end()?>
