<?php
	if (!empty($topic['Forum']['Parent']['slug'])) {
		$this->Breadcrumb->add($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
	}
	
	$this->Breadcrumb->add($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
	$this->Breadcrumb->add($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']));
	$this->Breadcrumb->add(__d('forum', 'Report Topic'), array('controller' => 'topics', 'action' => 'report', $topic['Topic']['slug']));  ?>
<div class="panel-head">
	<h5><?php echo __d('forum', 'Report Topic'); ?></h5>
</div>
<div class="panel">
	<p class="uk-text-center">
		<?php printf(__d('forum', 'Are you sure you want to report the topic %s? If so, please add a comment as to why you are reporting it, 255 max characters.'),
			$this->Html->link($topic['Topic']['title'], array('action' => 'view', $topic['Topic']['slug']))); ?>
	</p>
	<?php
		echo $this->Form->create('Report');
		echo $this->Form->input('type', array('options' => $this->Utility->enum('ItemReport', 'type'), 'div' => 'uk-margin', 'class' => 'uk-select', 'label' => array('class' => 'uk-form-label', 'text' => __d('forum', 'Type'))));
		echo $this->Form->input('comment', array('div' => 'uk-margin', 'class' => 'uk-textarea', 'type' => 'textarea', 'label' => array('class' => 'uk-form-label', 'text' => __d('forum', 'Comment'))));
		echo $this->Form->submit(__d('forum', 'Report'), array('class' => 'uk-button uk-button-primary', 'div' => 'uk-text-right'));
		echo $this->Form->end();
		?>
</div>
