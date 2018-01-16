<?php
if (!empty($topic['Forum']['Parent']['slug'])) {
    $this->Breadcrumb->add($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Breadcrumb->add($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
$this->Breadcrumb->add($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']));
$this->Breadcrumb->add(__d('forum', 'Moderate'), array('controller' => 'topics', 'action' => 'moderate', $topic['Topic']['slug'])); ?>

<div class="panel-head">
	
	<h3><?php echo __d('forum', 'Moderate'); ?>: <?php echo h($topic['Topic']['title']); ?></h3>
    <div class="action-buttons">
        <?php
        echo $this->Html->link(__d('forum', 'Delete Topic'), array('controller' => 'topics', 'action' => 'delete', $topic['Topic']['slug']), array('class' => 'btn btn-primary error', 'confirm' => __d('forum', 'Are you sure you want to delete?')));
        echo $this->Html->link(__d('forum', 'Return to Topic'), array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']), array('class' => 'btn btn-primary')); ?>
    </div>

   
</div>

    <?php echo $this->Form->create('Post', array('class' => 'form--inline')); ?>

    <div class="panel">
        <div class="panel-body table-responsive">
			<?php echo $this->element('Forum.pagination', array('class' => 'insidenav')); ?>
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="Forum.toggleCheckboxes(this, 'Post', 'items');"></th>
                        <th><?php echo __d('forum', 'User'); ?></th>
                        <th><?php echo __d('forum', 'Post'); ?></th>
                        <th class="text-xs-right"><?php echo __d('forum', 'Date'); ?></th>
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($posts as $counter => $post) { ?>

                    <tr>
                        <td class="col-icon">
                            <?php if ($post['Post']['id'] == $topic['Topic']['firstPost_id']) { ?>
                                <em class="text-muted">X</em>
                            <?php } else { ?>
                                <input type="checkbox" name="data[Post][items][]" value="<?php echo $post['Post']['id']; ?>">
                            <?php } ?>
                        </td>
                        <td>
                            <?php echo $this->Html->link($post['User'][$userFields['username']], $this->Forum->profileUrl($post['User'])); ?>
                        </td>
                        <td>
                            <?php echo $this->Text->truncate($this->Decoda->strip($post['Post']['content'], 100)); ?>
                        </td>
                        <td class="col-created">
                            <?php echo $this->Time->niceShort($post['Post']['created'], $this->Forum->timezone()); ?>
                        </td>
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
		<div class="col-md-12">
        <?php
        echo $this->Form->input('action', array('options' => array('delete' => __d('forum', 'Delete Post(s)')), 'class' => 'form-control', 'div' => 'input-group', 'label' => array('class' => 'input-group-addon', 'text' => __d('forum', 'Perform Action'))));
		echo "<br /><div class='text-xs-right'>";
        echo $this->Form->submit(__d('forum', 'Process'), array('div' => false, 'class' => 'btn btn-primary')); ?>
		</div>
		<br />
		</div>
		
	</div>

    <?php echo $this->Form->end(); ?>

