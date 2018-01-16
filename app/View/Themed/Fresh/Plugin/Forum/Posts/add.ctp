<?php
	if (!empty($topic['Forum']['Parent']['slug'])) {
	    $this->Breadcrumb->add($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
	}
	
	$this->Breadcrumb->add($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
	$this->Breadcrumb->add($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']));
	$this->Breadcrumb->add(__d('forum', 'Post Reply'), array('action' => 'add', $topic['Topic']['slug'])); ?>
<div class="panel-head">
	<h5><?php echo __d('forum', 'Post Reply'); ?></h5>
</div>
<div class="panel">
	<?php
		echo $this->Form->create('Post');
		echo $this->Form->input('content', array('type' => 'textarea', 'class' => 'uk-textarea', 'rows' => 15, 'label' => false, 'div' => 'uk-text-center'));
		echo $this->element('decoda', array('id' => 'PostContent'));
		echo '<br />';
		echo $this->Form->submit(__d('forum', 'Post Reply'), array('class' => 'uk-button-primary uk-button', 'div' => 'uk-text-right'));
		echo $this->Form->end();
		echo '<br />';
		
		if ($review) { ?>
</div>
<div class="panel">
	<div class="panel-head">
		<h5><?php echo __d('forum', 'Topic Review - Last 10 Replies'); ?></h5>
	</div>
	<div class="panel-body uk-overflow-auto" id="topic-review">
		<table class="uk-table-small uk-table">
			<?php foreach ($review as $post) { ?>
			<tr>
				<td class="span-2">
					<?php echo $this->Html->link($post['User'][$userFields['username']], $this->Forum->profileUrl($post['User'])); ?>
				</td>
				<td>
					<?php echo $this->Decoda->parse($post['Post']['content']); ?>
				</td>
				<td class="uk-text-right">
					<?php echo $this->Time->niceShort($post['Post']['created'], $this->Forum->timezone()); ?>
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>
</div>
<?php } ?>
