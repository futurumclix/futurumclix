<?php
	if (!empty($post['Forum']['Parent']['slug'])) {
		$this->Breadcrumb->add($post['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $post['Forum']['Parent']['slug']));
	}
	
	$this->Breadcrumb->add($post['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $post['Forum']['slug']));
	$this->Breadcrumb->add($post['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $post['Topic']['slug']));
	$this->Breadcrumb->add(__d('forum', 'Report Post'), array('action' => 'report', $post['Post']['id'])); ?>
<div class="panel-head">
	<h5><?php echo __d('forum', 'Report Post'); ?></h5>
</div>
<div class="panel">
	<div class="col-md-12">
		<p class="uk-text-center">
			<?php printf(__d('forum', 'Are you sure you want to report the post (below) in the topic %s? If so, please add a comment as to why you are reporting it, 255 max characters.'),
				$this->Html->link($post['Topic']['title'], array('controller' => 'forum', 'action' => 'jump', $post['Topic']['id'], $post['Post']['id']))); ?>
		</p>
		<?php
			echo $this->Form->create('Report', array('class' => 'uk-form-stacked'));
			echo $this->Form->input('post', array('div' => 'uk-margin', 'class' => 'uk-textarea', 'type' => 'textarea', 'readonly' => 'readonly', 'escape' => false, 'label' => array('class' => 'uk-form-label', 'text' => __d('forum', 'Post'))));
			echo '<br />';
			echo $this->Form->input('type', array('options' => $this->Utility->enum('ItemReport', 'type'), 'div' => 'uk-margin', 'class' => 'uk-select', 'label' => array('class' => 'uk-form-label', 'text' => __d('forum', 'Type'))));
			echo '<br />';
			echo $this->Form->input('comment', array('div' => 'uk-margin', 'class' => 'uk-textarea', 'type' => 'textarea', 'label' => array('class' => 'uk-label-form', 'text' => __d('forum', 'Comment'))));
			echo '<br />';
			echo $this->Form->submit(__d('forum', 'Report'), array('class' => 'uk-button uk-button-primary', 'div' => 'uk-text-right'));
			echo $this->Form->end(); 
			echo '<br />'; ?>
	</div>
</div>
