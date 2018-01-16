<?php
	if (!empty($forum['Parent']['slug'])) {
		$this->Breadcrumb->add($forum['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Parent']['slug']));
	}
	
	$this->Breadcrumb->add($forum['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug']));
	$this->Breadcrumb->add(__d('forum', 'Moderate'), array('action' => 'moderate', $forum['Forum']['slug'])); ?>
<div class="panel-head">
	<h3><?php echo __d('forum', 'Moderate'); ?>: <?php echo h($forum['Forum']['title']); ?></h3>
	<div class="action-buttons">
		<?php echo $this->Html->link(__d('forum', 'Return to Forum'), array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug']), array('class' => 'uk-button uk-button-secondary uk-button-small')); ?>
	</div>
</div>
<?php echo $this->Form->create('Topic', array('class' => 'form--inline')); ?>
<div class="panel" id="topics">
	<?php echo $this->element('Forum.pagination', array('class' => 'insidenav')); ?>
	<div class="panel-body uk-overflow-auto">
		<table class="uk-table-small uk-table">
			<thead>
				<tr>
					<th><span><input type="checkbox" onclick="Forum.toggleCheckboxes(this);"></span></th>
					<th><?php echo $this->Paginator->sort('Topic.title', __d('forum', 'Topic')); ?></th>
					<th><?php echo $this->Paginator->sort('Topic.status', __d('forum', 'Status')); ?></th>
					<th><?php echo $this->Paginator->sort('User.' . $userFields['username'], __d('forum', 'Author')); ?></th>
					<th class="uk-text-right"><?php echo $this->Paginator->sort('Topic.created', __d('forum', 'Created')); ?></th>
					<th class="uk-text-center"><?php echo $this->Paginator->sort('Topic.post_count', __d('forum', 'Posts')); ?></th>
					<th class="uk-text-center"><?php echo $this->Paginator->sort('Topic.view_count', __d('forum', 'Views')); ?></th>
					<th class="uk-text-right"><?php echo $this->Paginator->sort('LastPost.created', __d('forum', 'Activity')); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ($topics) {
					foreach ($topics as $counter => $topic) {
						echo $this->element('tiles/topic_row', array(
							'topic' => $topic,
							'counter' => $counter,
							'columns' => array('status')
						));
					}
					} else { ?>
				<tr>
					<td colspan="8" class="uk-text-center"><?php echo __d('forum', 'There are no topics within this forum'); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php echo $this->element('Forum.pagination', array('class' => 'insidenav')); ?>
	</div>
</div>
<div class="panel">
	<div class="panel-head">
		<h5><?php echo __d('forum', 'Action'); ?></h5>
	</div>
	<?php
		echo $this->Form->input('action', array(
			'options' => array(
				'open' => __d('forum', 'Open Topic(s)'),
				'close' => __d('forum', 'Close Topic(s)'),
				'move' => __d('forum', 'Move Topic(s)'),
				'delete' => __d('forum', 'Delete Topic(s)')
			),
			'class' => 'uk-select',
			'label' => array('class' => 'uk-form-label', 'text' => __d('forum', 'Perform Action')),
		));
		echo "<br />";
		echo $this->Form->input('move_id', array('options' => $forums, 'class' => 'uk-select', 'div' => 'uk-margin-top', 'label' => array('class' => 'uk-form-label', 'text' => __d('forum', 'Move To'))));
		echo "<br /><div class=\"uk-text-right\">";
		echo $this->Form->submit(__d('forum', 'Process'), array('div' => false, 'class' => 'uk-button uk-button-primary'));
		echo "</div>";
		echo "<br />"; ?>
</div>
<?php echo $this->Form->end(); ?>
