<div class="col-md-12">
	<div class="title">
		<h2><?=__d('forum', 'Forums list')?></h2>
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
					<th><?=$this->Paginator->sort('User.username', __d('forum', 'Moderator'))?></th>
					<th><?=$this->Paginator->sort('Forum.title')?></th>
					<th><?=$this->Paginator->sort('created')?></th>
					<th><?=$this->Paginator->sort('modified')?></th>
					<th><?=__d('forum_admin', 'Actions')?></th>
				</tr>
				<?php foreach($moderators as $moderator): ?>
				<tr>
					<td>
						<?=
							$this->AdminForm->checkbox('Moderator.'.$moderator['Moderator']['id'], array(
								'value' => $moderator['Moderator']['id'],
								'class' => 'ActionCheckbox',
							))
						?>
					</td>
					<td>
						<?=$this->Html->link($moderator['User']['username'], array('plugin' => '', 'controller' => 'users', 'action' => 'edit', $moderator['Moderator']['user_id']))?>
					</td>
					<td>
						<?=$this->Html->link($moderator['Forum']['title'], array('controller' => 'settings', 'action' => 'view', $moderator['Moderator']['forum_id']))?>
					</td>
					<td><?=h($moderator['Moderator']['created'])?></td>
					<td><?=h($moderator['Moderator']['modified'])?></td>
					<td class="actions">
						<?=
							$this->AdminForm->postLink(
								'<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('forum', 'Delete moderator').'"></i>',
								array('action' => 'delete', $moderator['Moderator']['id']), 
								array('escape' => false), 
								__d('forum', 'Are you sure you want to delete "%s" from moderators list of "%s"?', $moderator['User']['username'], $moderator['Forum']['title'])
							);
						?>
						<?=
							$this->Html->link('<i class="fa fa-pencil fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('forum', 'Edit moderator').'"></i>',
								array('action' => 'edit', $moderator['Moderator']['id']),
								array('escape'=> false)
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
			<label for="selectMassAction" class="input-group-addon"><?=__d('forum_admin', 'Mass action')?></label>
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
				<button class="btn btn-danger"><?=__d('forum_admin', 'Perform action')?></button>
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

