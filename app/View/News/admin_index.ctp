<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'News list')?></h2>
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
					<th><?=$this->Paginator->sort('title')?></th>
					<th><?=$this->Paginator->sort('show_in_login_ads')?></th>
					<th><?=$this->Paginator->sort('show_in_login_ads_until')?></th>
					<th><?=$this->Paginator->sort('created')?></th>
					<th><?=$this->Paginator->sort('modified')?></th>
					<th class="text-right"><?=__d('admin', 'Actions')?></th>
				</tr>
				<?php foreach($allNews as $news): ?>
					<tr>
						<td>
							<?=
								$this->AdminForm->checkbox('News.'.$news['News']['id'], array(
									'value' => $news['News']['id'],
									'class' => 'ActionCheckbox',
								))
							?>
						</td>
						<td><?=h($news['News']['title'])?></td>
						<td><?=$news['News']['show_in_login_ads'] ? __d('admin', 'Yes') : __d('admin', 'No')?></td>
						<td><?=$this->Time->nice($news['News']['show_in_login_ads_until'])?></td>
						<td><?=$this->Time->nice($news['News']['created'])?></td>
						<td><?=$this->Time->nice($news['News']['modified'])?></td>
						<td class="actions text-right">
							<?=
								$this->Html->link('<i class="fa fa-pencil fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Edit news').'"></i>',
									array('action' => 'edit', $news['News']['id']),
									array('escape'=> false)
								)
							?>
							<?=
								$this->AdminForm->postLink(
									'<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete news').'"></i>',
									array('action' => 'delete', $news['News']['id']), 
									array('escape' => false), 
									__d('admin', 'Are you sure you want to delete "%s"?', $news['News']['title'])
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
	<div class="col-sm-12 text-center paddingten">
		<p><?=$this->Paginator->numbers(array('separator' => '&nbsp;'))?></p>
	</div>
	<?=$this->AdminForm->end()?>
	<div class="col-sm-12 text-center paddingten">
		<?=
			$this->Html->link(__d('admin', 'Add New News'), array('action' => 'add'), array(
				'class' => 'btn btn-primary',
			))
		?>
	</div>
</div>
