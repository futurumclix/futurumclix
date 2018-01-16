<?php
	if ($forums) {
		foreach ($forums as $forum) { ?>
<div class="panel">
	<div class="panel-head">
		<h3><?php echo h($forum['Forum']['title']); ?></h3>
	</div>
	<div class="panel-body uk-overflow-auto">
		<table class="uk-table uk-table-small">
			<thead>
				<tr>
					<th style="witdh: 10%;"></th>
					<th><?php echo __d('forum', 'Forum'); ?></th>
					<th class="uk-text-center" style="width: 10%;"><?php echo __d('forum', 'Topics'); ?></th>
					<th class="uk-text-center" style="width: 10%;"><?php echo __d('forum', 'Posts'); ?></th>
					<th class="uk-text-right" style="width: 20%;"><?php echo __d('forum', 'Activity'); ?></th>
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
		<p><?php echo __d('forum', 'Statistics'); ?>: <span><?php printf(__d('forum', '%d topics, %d posts and %d users'), $totalTopics, $totalPosts, $totalUsers); ?></span></p>
	</div>
	<?php } ?>
	<?php if (Configure::read('Forum.newestUser') && $newestUser) { ?>
	<div class="newest-user">
		<p><?php echo __d('forum', 'Newest User'); ?>: <span><?php echo $this->Html->link($newestUser['User'][$userFields['username']], $this->Forum->profileUrl($newestUser['User'])); ?></span>
		</p>
	</div>
	<?php } ?>
</div>
