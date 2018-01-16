<?php
	$this->OpenGraph->description($this->Text->truncate($this->Decoda->strip($topic['FirstPost']['content']), 150));
	
	if (!empty($topic['Forum']['Parent']['slug'])) {
		$this->Breadcrumb->add($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
	}
	
	$this->Breadcrumb->add($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
	$this->Breadcrumb->add($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']));
	
	$canReply = ($user && $topic['Topic']['status'] && $this->Forum->hasAccess('Forum.Post', 'create', $topic['Forum']['accessReply'])); ?>
<div class="panel-head">
	<h3>
		<?php if ($topic['Topic']['type'] > Topic::NORMAL) {
			echo '<span>' . $this->Utility->enum('Forum.Topic', 'type', $topic['Topic']['type']) . ':</span> ';
			
			} else if ($topic['Topic']['status'] == Topic::CLOSED) {
			echo '<span>' . __d('forum', 'Closed') . ':</span> ';
			}
			
			echo h($topic['Topic']['title']); ?>
	</h3>
	<?php echo $this->element('tiles/topic_controls', array('topic' => $topic)); ?>
</div>
<?php if (!empty($topic['Poll']['id'])) { ?>
<div id="poll" class="panel">
	<div class="subforumtitle">
		<h5><?php echo __d('forum', 'Poll'); ?></h5>
	</div>
	<div class="panel-body">
		<?php echo $this->Form->create('Poll'); ?>
		<table class="uk-table uk-table-small">
			<tbody>
				<?php if (!$topic['Poll']['hasVoted']) {
					foreach ($topic['Poll']['PollOption'] as $counter => $option) { ?>
				<tr>
					<?php if ($user) { ?>
					<td class="col-icon">
						<input type="radio" name="data[Poll][option]" value="<?php echo $option['id']; ?>"<?php if ($counter == 0) echo ' checked="checked"'; ?>>
					</td>
					<?php } ?>
					<td colspan="2">
						<?php echo $option['option']; ?>
					</td>
				</tr>
				<?php } ?>
				<tr class="divider">
					<td colspan="<?php echo $user ? 3 : 2; ?>" class="uk-text-center">
						<?php if ($user) {
							if (!empty($topic['Poll']['expires']) && $topic['Poll']['expires'] <= date('Y-m-d H:i:s')) {
								echo __d('forum', 'Voting on this poll has been closed');
							} else {
								echo $this->Form->submit(__d('forum', 'Vote'), array('div' => false, 'class' => 'uk-button uk-button-primary'));
							}
							} else {
							echo __d('forum', 'Please login to vote!');
							} ?>
					</td>
				</tr>
				<?php } else {
					foreach ($topic['Poll']['PollOption'] as $counter => $option) { ?>
				<tr>
					<td class="align-right">
						<b><?php echo $option['option']; ?></b>
					</td>
					<td style="width: 50%">
						<div class="progressbar">
							<progress class="progress progress-striped" style="width: <?php echo $option['percentage']; ?>%"></progress>
						</div>
					</td>
					<td>
						<?php echo sprintf(__d('forum', '%d votes'), number_format($option['poll_vote_count'])); ?> (<?php echo $option['percentage']; ?>%)
						<?php if ($topic['Poll']['hasVoted'] == $option['id']) {
							echo '<em>(' . __d('forum', 'Your Vote') . ')</em>';
							} ?>
					</td>
				</tr>
				<?php } } ?>
			</tbody>
		</table>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
<?php } ?>
<div class="panel" id="posts">
	<?php echo $this->element('Forum.pagination', array('class' => 'insidenav')); ?>
	<div class="panel-body">
		<table class="uk-table uk-table-small">
			<tbody>
				<?php foreach ($posts as $post) {
					$post_id = $post['Post']['id'];
					$hasRated = in_array($post_id, $ratings);
					$isBuried = ($post['Post']['score'] <= $settings['ratingBuryThreshold']); ?>
				<tr class="postline <?php if ($isBuried) echo 'is-buried'; ?>" id="post-<?php echo $post_id; ?>">
					<td class="post-time">
						<?php echo $this->Html->tag('time',
							$this->Time->timeAgoInWords($post['Post']['created'], array('timezone' => $this->Forum->timezone())),
							array('uk-tooltip' => '', 'title' => $post['Post']['created'], 'datetime' => $post['Post']['created'])
							); ?>
					</td>
					<td>
						<div class="post-actions">
							<?php
								$links = array();
								
								if ($user) {
									$isMod = $this->Forum->isMod($topic['Forum']['id']);
								
									if ($topic['Topic']['firstPost_id'] == $post_id) {
										if ($isMod || ($topic['Topic']['status'] && $user['id'] == $post['Post']['user_id'])) {
											$links[] = $this->Html->link('<span class="mdi mdi-wrench"></span>', array('controller' => 'topics', 'action' => 'edit', $topic['Topic']['slug'], (!empty($topic['Poll']['id']) ? 'poll' : '')), array('escape' => false, 'uk-tooltip' => '', 'title' => __d('forum', 'Edit Topic')));
										}
								
										if ($isMod) {
											$links[] = $this->Html->link('<span class="mdi mdi-trash"></span>', array('controller' => 'topics', 'action' => 'delete', $topic['Topic']['slug']), array('escape' => false, 'confirm' => __d('forum', 'Are you sure you want to delete?'), 'uk-tooltip' => '', 'title' => __d('forum', 'Delete Topic')));
										}
								
										$links[] = $this->Html->link('<span class="mdi mdi-flag"></span>', array('controller' => 'topics', 'action' => 'report', $topic['Topic']['slug']), array('escape' => false, 'uk-tooltip' => '', 'title' => __d('forum', 'Report Topic')));
									} else {
										if ($isMod || ($topic['Topic']['status'] && $user['id'] == $post['Post']['user_id'])) {
											$links[] = $this->Html->link('<span class="mdi mdi-wrench"></span>', array('controller' => 'posts', 'action' => 'edit', $post_id), array('escape' => false, 'uk-tooltip' => '', 'title' => __d('forum', 'Edit Post')));
											$links[] = $this->Html->link('<span class="mdi mdi-trash"></span>', array('controller' => 'posts', 'action' => 'delete', $post_id), array('escape' => false, 'confirm' => __d('forum', 'Are you sure you want to delete?'), 'uk-tooltip' => '', 'title' => __d('forum', 'Delete Post')));
										}
								
										$links[] = $this->Html->link('<span class="mdi mdi-flag"></span>', array('controller' => 'posts', 'action' => 'report', $post_id), array('escape' => false, 'uk-tooltip' => '', 'title' => __d('forum', 'Report Post')));
									}
								
									if ($canReply) {
										$links[] = $this->Html->link('<span class="mdi mdi-format-quote-close"></span>', array('controller' => 'posts', 'action' => 'add', $topic['Topic']['slug'], $post_id), array('escape' => false, 'uk-tooltip' => '', 'title' => __d('forum', 'Quote')));
									}
								}
								
								$links[] = $this->Html->link('<span class="mdi mdi-link-variant"></span>', '#post-' . $post_id, array('escape' => false, 'uk-tooltip' => '', 'title' => __d('forum', 'Link To This')));
								
								if ($links) {
									echo implode(' ', $links);
								} ?>
						</div>
						<?php if ($user) {
							if ($settings['enablePostRating'] && (!$hasRated || $settings['showRatingScore'])) { ?>
						<div id="post-ratings-<?php echo $post_id; ?>" class="post-ratings<?php if ($hasRated) echo ' has-rated'; ?>">
							<?php if (!$hasRated) { ?>
							<a href="javascript:;" onclick="return Forum.ratePost(<?php echo $post_id; ?>, 'up');" class="rate-up" title="<?php echo __d('forum', 'Rate Up'); ?>" uk-tooltip>
							<span class="mdi mdi-arrow-up"></span>
							</a>
							<?php }
								$score = number_format($post['Post']['score']);
								if ($settings['showRatingScore'] && $score >= 0) { ?>
							<span class="rating"><i class="mdi mdi-thumb-up"></i> <?php echo number_format($post['Post']['score']); ?></span>
							<?php } else { ?>
							<span class="rating"><i class="mdi mdi-thumb-down"></i> <?php echo number_format($post['Post']['score']); ?></span>
							<?php }
								if (!$hasRated) { ?>
							<a href="javascript:;" onclick="return Forum.ratePost(<?php echo $post_id; ?>, 'down');" class="rate-down" title="<?php echo __d('forum', 'Rate Down'); ?>" uk-tooltip>
							<span class="mdi mdi-arrow-down"></span>
							</a>
							<?php } ?>
						</div>
						<?php } ?>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="span-2">
						<h4 class="username">
							<?php echo $this->Forum->getCountryFlag($post['User']['location']); ?>
							<?php echo $this->Html->link($post['User'][$userFields['username']], $this->Forum->profileUrl($post['User'])); ?>
						</h4>
						<div class="uk-badge uk-margin"><?php echo $post['User']['ActiveMembership']['Membership']['name']; ?></div>
						<?php if (!$isBuried) {
							echo $this->Forum->avatar($post) ?>
						<?php if (!empty($post['User']['ForumModerator'])) { ?>
						<div class="uk-badge">Moderator</div>
						<?php } ?>
						<div class="avatar-stats">
							<?php if (!empty($post['User'][$userFields['totalTopics']])) { ?>
							<?php echo __d('forum', 'Total Topics'); ?>: <?php echo number_format($post['User'][$userFields['totalTopics']]); ?><br>
							<?php } ?>
							<?php if (!empty($post['User'][$userFields['totalPosts']])) { ?>
							<?php echo __d('forum', 'Total Posts'); ?>: <?php echo number_format($post['User'][$userFields['totalPosts']]); ?><br>
							<?php }
								if($post['User']['forum_statistics']) { ?>
							<?php echo __d('forum', 'Referrals: %d', $post['User']['refs_count'] + $post['User']['rented_refs_count']); ?><br>
							<?php echo __d('forum', 'Paid Out: %s', $this->Currency->format($post['User']['UserStatistic']['total_cashouts'])); ?><br>
							<?php echo __d('forum', 'Earned: %s', $this->Currency->format($post['User']['UserStatistic']['total_earned']));?><br>
							<?php }
								} ?>
						</div>
					</td>
					<td>
						<div class="post">
							<?php if ($isBuried) { ?>
							<div class="buried-text text-muted">
								<?php echo __d('forum', 'This post has been buried.'); ?>
								<a href="javascript:;" onclick="return Forum.toggleBuried(<?php echo $post_id; ?>);"><?php echo __d('forum', 'View the buried post?'); ?></a>
							</div>
							<div class="post-buried" id="post-buried-<?php echo $post_id; ?>" style="display: none">
								<?php echo $this->Decoda->parse($post['Post']['content']); ?>
							</div>
							<?php
								} elseif($post['User'][Configure::read('User.fieldMap.status')] == Configure::read('User.statusMap.banned')) {
									echo __d('forum', 'This user has been banned.');
								} else {
									echo $this->Decoda->parse($post['Post']['content']);
								} ?>
						</div>
						<?php if (!$isBuried && !empty($post['User'][$userFields['signature']])) { ?>
						<div class="signature">
							<?php echo $this->Decoda->parse($post['User'][$userFields['signature']]); ?>
						</div>
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php echo $this->element('Forum.pagination', array('class' => 'insidenav')); ?>
</div>
<?php
	echo $this->element('tiles/topic_controls', array('topic' => $topic));
	
	if ($settings['enableQuickReply'] && $canReply) { ?>
<div id="quick-reply" class="panel quick-reply">
	<div class="panel-head">
		<h5><?php echo __d('forum', 'Quick Reply'); ?></h5>
	</div>
	<div class="panel-body uk-margin">
		<?php
			echo $this->Form->create('Post', array('url' => array('controller' => 'posts', 'action' => 'add', $topic['Topic']['slug'])));
			echo $this->Form->input('content', array(
				'type' => 'textarea',
				'class' => 'uk-textarea',
				'rows' => 5,
				'div' => false,
				'error' => false,
				'label' => false
			));
			echo $this->element('decoda', array('id' => 'PostContent'));
			echo '<div class="uk-text-right uk-margin">';
			echo $this->Form->submit(__d('forum', 'Post Reply'), array('class' => 'uk-button uk-button-primary'));
			echo '</div>';
			echo $this->Form->end(); ?>
	</div>
</div>
<?php } ?>
