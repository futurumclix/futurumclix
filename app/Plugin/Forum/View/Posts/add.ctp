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
	<div class="col-md-12">
		<?php
			echo $this->Form->create('Post');
			echo $this->Form->input('content', array('type' => 'textarea', 'class' => 'form-control', 'rows' => 15, 'label' => false, 'div' => 'text-xs-center'));
			echo $this->element('decoda', array('id' => 'PostContent'));
			echo '<br />';
			echo $this->Form->submit(__d('forum', 'Post Reply'), array('class' => 'btn btn-primary', 'div' => 'text-xs-right'));
			echo $this->Form->end();
			echo '<br />';
			
			if ($review) { ?>
	</div>
</div>
<div class="panel">
	<div class="panel-head">
		<h5><?php echo __d('forum', 'Topic Review - Last 10 Replies'); ?></h5>
	</div>
	<div class="panel-body table-responsive col-md-12" id="topic-review">
		<table class="table table-sm">
			<?php foreach ($review as $post) { ?>
			<tr>
				<td class="span-2">
					<?php echo $this->Html->link($post['User'][$userFields['username']], $this->Forum->profileUrl($post['User'])); ?>
				</td>
				<td>
					<?php echo $this->Decoda->parse($post['Post']['content']); ?>
				</td>
				<td class="text-xs-right">
					<?php echo $this->Time->niceShort($post['Post']['created'], $this->Forum->timezone()); ?>
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>
</div>
<?php } ?>