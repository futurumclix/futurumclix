<?php
	if (!empty($post['Forum']['Parent']['slug'])) {
		$this->Breadcrumb->add($post['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $post['Forum']['Parent']['slug']));
	}
	
	$this->Breadcrumb->add($post['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $post['Forum']['slug']));
	$this->Breadcrumb->add($post['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $post['Topic']['slug']));
	$this->Breadcrumb->add(__d('forum', 'Edit Post'), array('action' => 'edit', $post['Topic']['slug'])); ?>
<div class="panel-head">
	<h5><?php echo __d('forum', 'Edit Post'); ?></h5>
</div>
<div class="panel">
	<?php
		echo $this->Form->create('Post');
		echo $this->Form->input('content', array('type' => 'textarea', 'rows' => 15, 'label' => false, 'class' => 'uk-textarea', 'div' => 'uk-text-center'));
		echo $this->element('decoda', array('id' => 'PostContent'));
		echo '<br />';
		echo $this->Form->submit(__d('forum', 'Update Post'), array('class' => 'uk-button uk-button-primary', 'div' => 'uk-text-right'));
		echo $this->Form->end(); 
		echo '<br />'; ?>
</div>
