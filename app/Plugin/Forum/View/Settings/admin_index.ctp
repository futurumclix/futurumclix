<div class="col-md-12">
	<div class="title">
		<h2><?=__d('forum', 'Forums List')?></h2>
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
					<th><?=$this->Paginator->sort('slug')?></th>
					<th><?=$this->Paginator->sort('icon')?></th>
					<th><?=$this->Paginator->sort('status')?></th>
					<th><?=$this->Paginator->sort('topic_count')?></th>
					<th><?=$this->Paginator->sort('post_count')?></th>
					<th><?=$this->Paginator->sort('open')?></th>
					<th><?=$this->Paginator->sort('parent_id')?></th>
					<th><?=__d('forum_admin', 'Actions')?></th>
				</tr>
				<?php foreach($forums as $forum): ?>
				<tr>
					<td>
						<?=
							$this->AdminForm->checkbox('Forum.'.$forum['Forum']['id'], array(
								'value' => $forum['Forum']['id'],
								'class' => 'ActionCheckbox',
							))
						?>
					</td>
					<td><?=h($forum['Forum']['title'])?></td>
					<td><?=h($forum['Forum']['slug'])?></td>
					<td><?=h($forum['Forum']['icon'])?></td>
					<td><?=h($this->Utility->enum('Forum', 'status', $forum['Forum']['status']))?></td>
					<td><?=h($forum['Forum']['topic_count'])?></td>
					<td><?=h($forum['Forum']['post_count'])?></td>
					<td><?=h($forum['Forum']['status_enum'] == 'OPEN' ? __d('forum', 'Yes') : __d('forum', 'No'))?></td>
					<td><?=$forum['Forum']['parent_id'] === null ? __d('forum', '-') : h($forum['Parent']['title'])?></td>
					<td class="actions">
						<?=
							$this->Html->link('<i class="fa fa-pencil fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('forum', 'Edit forum').'"></i>',
								array('action' => 'edit', $forum['Forum']['id']),
								array('escape'=> false)
							)
						?>
						<?=
							$this->AdminForm->postLink(
								'<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('forum', 'Delete forum').'"></i>',
								array('action' => 'delete', $forum['Forum']['id']), 
								array('escape' => false), 
								__d('forum_admin', 'Are you sure you want to delete %s?', $forum['Forum']['title'])
							);
						?>
						<?=
							$this->AdminForm->postLink(
								'<i class="fa fa-ban fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('forum', 'Remove icon').'"></i>',
								array('action' => 'removeIcon', $forum['Forum']['id']),
								array('escape' => false), 
								__d('forum_admin', 'Are you sure you want to delete %s icon?', $forum['Forum']['title'])
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
			<label for="selectMassAction" class="input-group-addon"><?=__d('forum_admin', 'Mass Action')?></label>
			<?=
				$this->AdminForm->input('Action', array(
					'empty' => __d('forum_admin', '--Choose--'),
					'required' => true,
					'id'=> 'selectMassAction',
					'options' => array(
						'delete' => __d('forum_admin', 'Delete'),
					)
				))
			?>
			<div class="input-group-btn">
				<button class="btn btn-danger"><?=__d('forum_admin', 'Perform Action')?></button>
			</div>
		</div>
	</div>
	<div class="col-sm-7 text-right">
		<?=
			$this->Paginator->counter(array(
				'format' => __d('forum_admin', 'Page {:page} of {:pages}')
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

