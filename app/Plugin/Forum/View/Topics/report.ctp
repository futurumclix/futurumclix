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
	<div class="col-md-12">
    <p>
        <?php printf(__d('forum', 'Are you sure you want to report the topic %s? If so, please add a comment as to why you are reporting it, 255 max characters.'),
            $this->Html->link($topic['Topic']['title'], array('action' => 'view', $topic['Topic']['slug']))); ?>
    </p>

    <?php
    echo $this->Form->create('Report');
    echo $this->Form->input('type', array('options' => $this->Utility->enum('ItemReport', 'type'), 'div' => 'input-group', 'class' => 'form-control', 'label' => array('class' => 'input-group-addon', 'text' => __d('forum', 'Type'))));
    echo $this->Form->input('comment', array('div' => 'input-group', 'class' => 'form-control', 'type' => 'textarea', 'label' => array('class' => 'input-group-addon', 'text' => __d('forum', 'Comment'))));
	echo '<br />';
    echo $this->Form->submit(__d('forum', 'Report'), array('class' => 'btn btn-primary', 'div' => 'text-xs-right'));
    echo $this->Form->end();
	echo '<br />'; ?>
	</div>
</div>