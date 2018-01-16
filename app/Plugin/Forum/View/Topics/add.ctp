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
		echo $this->Form->create('Topic');?>
	<div class="col-md-12">
		<?php
			echo $this->Form->input('title', array('div' => 'input-group', 'class' => 'form-control', 'label' => array('class' => 'input-group-addon', 'text' => __d('forum', 'Title'))));
			echo '<br />';
			echo $this->Form->input('forum_id', array('div' => 'input-group', 'class' => 'form-control', 'options' => $forums, 'empty' => '-- ' . __d('forum', 'Select a Forum') . ' --', 'label' => array('class' => 'input-group-addon', 'text' => __d('forum', 'Forum'))));
			echo '<br />';
			if ($this->Forum->isMod($forum['Forum']['id'])) {
				echo $this->Form->input('status', array('div' => 'input-group', 'class' => 'form-control', 'options' => $this->Utility->enum('Forum.Topic', 'status'), 'label' => array('class' => 'input-group-addon', 'text' => __d('forum', 'Status'))));
				echo '<br />';
				echo $this->Form->input('type', array('div' => 'input-group', 'class' => 'form-control', 'options' => $this->Utility->enum('Forum.Topic', 'type'), 'label' => array('class' => 'input-group-addon', 'text' => __d('forum', 'Type'))));
			}
			echo '<br />';
			if ($type === 'poll') {
				echo $this->Form->input('options', array(
					'div' => 'input-group',
					'type' => 'textarea',
					'class' => 'form-control',
					'label' => array('class' => 'input-group-addon', 'text' => __d('forum', 'Poll Options')),
					'after' => '<span class="input-group-addon">' . __d('forum', 'One option per line.<br /> Max 10 options.') . '</span>',
					'rows' => 5
				));
				echo '<br />';
				echo $this->Form->input('expires', array(
					'div' => 'input-group',
					'label' => array('class' => 'input-group-addon', 'text' => __d('forum', 'Expiration Date')),
					'after' => '<span class="input-group-addon">' . __d('forum', 'How many days till expiration? Leave blank to last forever.') . '</span>',
					'class' => 'form-control'
				));
				echo '<br />';
			}
			
			?>
	</div>
	<div class="col-md-12 text-xs-center padding10">
		<?php
			if ($forum['Forum']['excerpts']) {
				$chars = isset($this->request->data['Topic']['excerpt']) ? strlen($this->request->data['Topic']['excerpt']) : 0;
				$maxLength = $settings['excerptLength'];
			
				echo $this->Form->input('excerpt', array(
					'div' => 'input-group',
					'class' => 'form-control',
					'label' =>  array('class' => 'input-group-addon', 'text' => __d('forum', 'Excerpt')),
					'type' => 'textarea',
					'rows' => 5,
					'onkeyup' => 'Forum.charsRemaining(this, ' . $maxLength . ');',
					'after' => '<span class="input-group-addon">' . __d('forum', '%s characters remaining', '<span id="TopicExcerptCharsRemaining">' . ($maxLength - $chars) . '</span>') . '</span>',
				));
				echo '<br />';
			}
			
			echo $this->Form->input('content', array(
				'label' => false,
				'type' => 'textarea',
				'rows' => 15,
				'class' => 'form-control'
			));
			echo '<br />';
			echo $this->element('decoda', array('id' => 'TopicContent'));
			echo $this->Form->submit($pageTitle, array('class' => 'btn btn-primary'));
			echo $this->Form->end(); ?>
	</div>
</div>