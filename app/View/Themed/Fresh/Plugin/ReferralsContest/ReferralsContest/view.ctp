<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__d('referrals_contest', 'Referral\'s contest - "%s"', $contest['ReferralsContest']['title'])?></h2>
			<p><?=h($contest['ReferralsContest']['description'])?></p>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-small">
					<thead>
						<tr>
							<th><?=__d('referrals_contest', 'Place')?></th>
							<th><?=__d('referrals_contest', 'Nickname')?></th>
							<th><?=__d('referrals_contest', 'Referrals')?></th>
							<th><?=__d('referrals_contest', 'Prize')?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($list as $user): ?>
						<tr>
							<td><?=__d('referrals_contest', '%d.', $user[0]['rank2'])?></td>
							<td><?=h($user['Upline']['username'])?></td>
							<td><?=__d('referrals_contest', '%d', $user[0]['score'])?></td>
							<td>
								<?php if(isset($contest['ReferralsContest']['prizes'][$user[0]['rank2'] - 1])): ?>
								<?=__d('referrals_contest', '%s (%s)', $this->Currency->format($contest['ReferralsContest']['prizes'][$user[0]['rank2'] - 1]['prize']), __d('referrals_contest', '%s Balance', ucfirst($contest['ReferralsContest']['prizes'][$user[0]['rank2'] - 1]['credit'])))?>
								<?php else: ?>
								<?=__d('referrals_contest', '---')?>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
