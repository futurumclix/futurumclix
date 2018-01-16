<?php $this->Breadcrumb->add($profile['User']['username'], array('controller' => 'users', 'action' => 'profile', $profile['User']['username'])); ?>
<div class="panel-head">
	<h5><?=__d('forum', '%s\'s profile', $profile['User']['username'])?></h5>
</div>
<div class="panel">
	<div class="uk-form-stacked">
		<?=$this->Forum->avatar($profile)?>
		<h4><?=h($profile['User']['username'])?></h4>
		<h5><?=__d('forum', 'Topics: %d', $profile['User']['topic_count'])?></h5>
		<h5><?=__d('forum', 'Posts: %d', $profile['User']['post_count'])?></h5>
		<?php if($profile['User']['forum_statistics']): ?>
		<h5><?=__d('forum', 'Referrals: %d', $profile['User']['refs_count'] + $profile['User']['rented_refs_count'])?></h5>
		<h5><?=__d('forum', 'Received: %s', $this->Currency->format($profile['UserStatistic']['total_cashouts']))?></h5>
		<h5><?=__d('forum', 'Earned: %s', $this->Currency->format($profile['UserStatistic']['total_earned']))?></h5>
		<?php endif; ?>
		<h5><?=__d('forum', 'Country: %s', $this->Forum->getCountryFlag($profile['User']['location']))?></h5>
		<h4><?=__d('forum', 'Signature:')?></h4>
		<?=$this->Decoda->parse($profile['User']['signature'])?>
	</div>
</div>
