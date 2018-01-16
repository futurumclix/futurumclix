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
<p>
	<?php printf(__d('forum', 'Are you sure you want to report the post (below) in the topic %s? If so, please add a comment as to why you are reporting it, 255 max characters.'),
		$this->Html->link($post['Topic']['title'], array('controller' => 'forum', 'action' => 'jump', $post['Topic']['id'], $post['Post']['id']))); ?>
</p>

<?php
echo $this->Form->create('Report');
echo $this->Form->input('post', array('div' => 'input-group', 'class' => 'form-control', 'type' => 'textarea', 'readonly' => 'readonly', 'escape' => false, 'label' => array('class' => 'input-group-addon', 'text' => __d('forum', 'Post'))));
echo '<br />';
echo $this->Form->input('type', array('options' => $this->Utility->enum('ItemReport', 'type'), 'div' => 'input-group', 'class' => 'form-control', 'label' => array('class' => 'input-group-addon', 'text' => __d('forum', 'Type'))));
echo '<br />';
echo $this->Form->input('comment', array('div' => 'input-group', 'class' => 'form-control', 'type' => 'textarea', 'label' => array('class' => 'input-group-addon', 'text' => __d('forum', 'Comment'))));
echo '<br />';
echo $this->Form->submit(__d('forum', 'Report'), array('class' => 'btn btn-primary', 'div' => 'text-xs-right'));
echo $this->Form->end(); 
echo '<br />'; ?>
	</div>
</div>