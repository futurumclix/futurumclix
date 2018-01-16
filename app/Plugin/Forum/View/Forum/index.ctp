<?php
if ($forums) {
	foreach ($forums as $forum) { ?>

<div class="panel">
	<div class="panel-head">
		<h3><?php echo h($forum['Forum']['title']); ?></h3>
	</div>

	<div class="panel-body table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th style="witdh: 10%;"></th>
					<th><?php echo __d('forum', 'Forum'); ?></th>
					<th class="text-xs-center" style="width: 10%;"><?php echo __d('forum', 'Topics'); ?></th>
					<th class="text-xs-center" style="width: 10%;"><?php echo __d('forum', 'Posts'); ?></th>
					<th class="text-xs-right" style="width: 20%;"><?php echo __d('forum', 'Activity'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php if ($forum['Children']) {
				foreach ($forum['Children'] as $counter => $child) {
					echo $this->element('tiles/forum_row', array(
						'forum' => $child,
						'counter' => $counter
					));
				}
			} else { ?>

				<tr>
					<td colspan="5" class="no-results"><?php echo __d('forum', 'There are no categories within this forum'); ?></td>
				</tr>

			<?php } ?>

			</tbody>
		</table>
	</div>
</div>

<?php } } ?>

<div class="statistics">
	<?php if(Configure::read('Forum.indexStatistics')) { ?>
		<div class="total-stats">
			<p><?php echo __d('forum', 'Statistics'); ?>:</p> <span><?php printf(__d('forum', '%d topics, %d posts <p>and</p> %d users'), $totalTopics, $totalPosts, $totalUsers); ?></span>
		</div>
	<?php } ?>

	<?php if (Configure::read('Forum.newestUser') && $newestUser) { ?>
		<div class="newest-user">
			<p><?php echo __d('forum', 'Newest User'); ?>:</p> <span><?php echo $this->Html->link($newestUser['User'][$userFields['username']], $this->Forum->profileUrl($newestUser['User'])); ?></span>
		</div>
	<?php } ?>
</div>