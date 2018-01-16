<?php

$this->Breadcrumb->add(__d('forum', 'Search'), array('controller' => 'search', 'action' => 'index')); ?>

<div class="panel">
<div class="panel-head">
    <h3><?php echo __d('forum', 'Search'); ?></h3>
</div>

<?php echo $this->Form->create('Topic', array('class' => 'form--inline', 'url' => array('controller' => 'search', 'action' => 'proxy'))); ?>

<div class="form-filters" id="search">
	<fieldset class="form-group">
		<?=$this->Form->input('keywords', array('div' => 'col-md-12', 'label' => __d('forum', 'With keywords'), 'class' => 'form-control'))?>
	</fieldset>
	<fieldset class="form-group">
		<?=$this->Form->input('forum_id', array('div' => 'col-md-12', 'label' => __d('forum', 'In forum'), 'options' => $forums, 'class' => 'form-control', 'empty' => true))?>
	</fieldset>
	<fieldset class="form-group">
		<?=$this->Form->input('byUser', array('div' => 'col-md-12', 'label' => __d('forum', 'By user'), 'class' => 'form-control'))?>
	</fieldset>
	<fieldset class="form-group">
		<?=$this->Form->input('orderBy', array('div' => 'col-md-12', 'label' => __d('forum', 'Order by'), 'options' => $orderBy, 'class' => 'form-control'))?>
	</fieldset>
	<div class="padding10 text-xs-right">
		<?php echo $this->Form->submit(__d('forum', 'Search Topics'), array('class' => 'btn btn-primary')); ?>
	</div>
</div>

<?php
echo $this->Form->end();

if ($searching) {
	echo $this->element('Forum.pagination', array('class' => 'insidenav')); ?>

		<div class="panel-body">
			<table class="table table-hover">
				<thead>
					<tr>
						<th style="witdh: 10%;"></th>
						<th><?php echo $this->Paginator->sort('Topic.title', __d('forum', 'Topic')); ?></th>
						<th><?php echo $this->Paginator->sort('Topic.forum_id', __d('forum', 'Forum')); ?></th>
						<th><?php echo $this->Paginator->sort('User.' . $userFields['username'], __d('forum', 'Author')); ?></th>
						<th class="text-xs-right"><?php echo $this->Paginator->sort('Topic.created', __d('forum', 'Created')); ?></th>
						<th class="text-xs-center"><?php echo $this->Paginator->sort('Topic.post_count', __d('forum', 'Posts')); ?></th>
						<th class="text-xs-center"><?php echo $this->Paginator->sort('Topic.view_count', __d('forum', 'Views')); ?></th>
						<th class="text-xs-right"><?php echo $this->Paginator->sort('LastPost.created', __d('forum', 'Activity')); ?></th>
					</tr>
				</thead>
				<tbody>

				<?php if (!$topics) { ?>

					<tr>
						<td colspan="8" class="no-results"><?php echo __d('forum', 'No results were found, please refine your search criteria'); ?></td>
					</tr>

				<?php } else {
					foreach ($topics as $counter => $topic) {
						echo $this->element('tiles/topic_row', array(
							'topic' => $topic,
							'counter' => $counter,
							'columns' => array('forum')
						));
					}
				} ?>

				</tbody>
			</table>
		</div>
		<?php echo $this->element('Forum.pagination', array('class' => 'insidenav')); ?>
	</div>
<?php } ?>
