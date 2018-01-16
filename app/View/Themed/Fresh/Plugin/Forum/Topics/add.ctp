<?php
	if (!empty($forum['Parent']['slug'])) {
		$this->Breadcrumb->add($forum['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Parent']['slug']));
	}
	
	$this->Breadcrumb->add($forum['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug']));
	$this->Breadcrumb->add($pageTitle, array('controller' => 'topics', 'action' => 'add', $forum['Forum']['slug'])); ?>
<div class="panel">
	<div class="panel-head">
		<h3><?php echo $pageTitle; ?></h3>
	</div>
	<?php
		echo $this->Form->create('Topic', array('class' => 'uk-form-stacked'));?>
	<?php
		echo $this->Form->input('title', array('div' => 'uk-margin', 'class' => 'uk-input', 'label' => array('class' => 'uk-label-form', 'text' => __d('forum', 'Title'))));
		echo $this->Form->input('forum_id', array('div' => 'uk-margin', 'class' => 'uk-select', 'options' => $forums, 'empty' => '-- ' . __d('forum', 'Select a Forum') . ' --', 'label' => array('class' => 'uk-label-form', 'text' => __d('forum', 'Forum'))));
		if ($this->Forum->isMod($forum['Forum']['id'])) {
			echo $this->Form->input('status', array('div' => 'uk-margin', 'class' => 'uk-select', 'options' => $this->Utility->enum('Forum.Topic', 'status'), 'label' => array('class' => 'uk-label-form', 'text' => __d('forum', 'Status'))));
			echo $this->Form->input('type', array('div' => 'uk-margin', 'class' => 'uk-select', 'options' => $this->Utility->enum('Forum.Topic', 'type'), 'label' => array('class' => 'uk-label-form', 'text' => __d('forum', 'Type'))));
		}
		if ($type === 'poll') {
			echo $this->Form->input('options', array(
				'div' => 'uk-margin',
				'type' => 'textarea',
				'class' => 'uk-textarea',
				'label' => array('class' => 'uk-label-form', 'text' => __d('forum', 'Poll Options')),
				'after' => '<span class="uk-label-form">' . __d('forum', 'One option per line.<br /> Max 10 options.') . '</span>',
				'rows' => 5
			));
			echo $this->Form->input('expires', array(
				'div' => 'uk-margin',
				'label' => array('class' => 'uk-label-form', 'text' => __d('forum', 'Expiration Date')),
				'after' => '<span class="uk-label-form">' . __d('forum', 'How many days till expiration? Leave blank to last forever.') . '</span>',
				'class' => 'uk-select'
			));
		}
		
		?>
	<div class="uk-text-center">
		<?php
			if ($forum['Forum']['excerpts']) {
				$chars = isset($this->request->data['Topic']['excerpt']) ? strlen($this->request->data['Topic']['excerpt']) : 0;
				$maxLength = $settings['excerptLength'];
			
				echo $this->Form->input('excerpt', array(
					'div' => 'uk-margin',
					'class' => 'uk-textarea',
					'label' =>  array('class' => 'uk-label-form', 'text' => __d('forum', 'Excerpt')),
					'type' => 'textarea',
					'rows' => 5,
					'onkeyup' => 'Forum.charsRemaining(this, ' . $maxLength . ');',
					'after' => '<span class="uk-label-form">' . __d('forum', '%s characters remaining', '<span id="TopicExcerptCharsRemaining">' . ($maxLength - $chars) . '</span>') . '</span>',
				));
			}
			
			echo $this->Form->input('content', array(
				'label' => false,
				'type' => 'textarea',
				'rows' => 15,
				'class' => 'uk-textarea'
			));
			echo $this->element('decoda', array('id' => 'TopicContent'));
			echo $this->Form->submit($pageTitle, array('class' => 'uk-button uk-button-primary'));
			echo $this->Form->end(); ?>
	</div>
</div>
