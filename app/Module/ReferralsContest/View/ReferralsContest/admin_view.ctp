<div class="col-md-12">
	<div class="title">
		<h2><?=__d('referrals_contest_admin', 'Referral contest - "%s"', h($contest['ReferralsContest']['title']))?></h2>
	</div>
	<div>
		<?=h($contest['ReferralsContest']['description'])?>
	</div>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<tbody>
				<tr>
					<th><?=__d('referrals_contest_admin', 'Place')?></th>
					<th><?=__d('referrals_contest_admin', 'Nickname')?></th>
					<th><?=__d('referrals_contest_admin', 'Referrals')?></th>
					<th><?=__d('referrals_contest_admin', 'Prize')?></th>
					<th><?=__d('referrals_contest_admin', 'Actions')?></th>
				</tr>
				<?php foreach($list as $user): ?>
					<tr>
						<td><?=__d('referrals_contest_admin', '%d.', $user[0]['rank2'])?></td>
						<td><?=$this->Html->link($user['Upline']['username'], array('plugin' => null, 'controller' => 'users', 'action' => 'edit', $user['Upline']['id']))?></td>
						<td>
							<?=$this->Html->link($user[0]['score'], array('plugin' => null, 'controller' => 'users', 'action' => 'index', 'Upline.id' => $user['Upline']['id'], 'User.dref_since >=' => $contest['ReferralsContest']['starts'], 'User.dref_since <=' => $contest['ReferralsContest']['ends']))?>
						</td>
						<td>
							<?php if(isset($contest['ReferralsContest']['prizes'][$user[0]['rank2'] - 1])): ?>
								<?=__d('referrals_contest_admin', '%s (%s)', $this->Currency->format($contest['ReferralsContest']['prizes'][$user[0]['rank2'] - 1]['prize']), __d('referrals_contest_admin', '%s Balance', ucfirst($contest['ReferralsContest']['prizes'][$user[0]['rank2'] - 1]['credit'])))?>
							<?php else: ?>
								<?=__d('referrals_contest_admin', '---')?>
							<?php endif; ?>
						</td>
						<td>
							<?php if(in_array($user['Upline']['id'], $banned)): ?>
								<?=
									$this->AdminForm->postLink('<i class="fa fa-play"></i>',
										array('action' => 'unban', $user['Upline']['id'], $contest['ReferralsContest']['id']),
										array('escape' => false),
										__d('referrals_contest_admin', 'Are you sure you want to unban "%s" from "%s"?', $user['Upline']['username'], $contest['ReferralsContest']['title'])
									)
								?>
							<?php else: ?>
								<?=
									$this->AdminForm->postLink('<i class="fa fa-pause"></i>',
										array('action' => 'ban', $user['Upline']['id'], $contest['ReferralsContest']['id']),
										array('escape' => false),
										__d('referrals_contest_admin', 'Are you sure you want to ban "%s" from "%s"?', $user['Upline']['username'], $contest['ReferralsContest']['title'])
									)
								?>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
