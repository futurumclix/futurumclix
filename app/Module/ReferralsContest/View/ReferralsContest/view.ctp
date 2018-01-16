<?=$this->element('userSidebar')?>
<div class="page">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 padding30-sides">
				<?=$this->element('userBreadcrumbs')?>
				<?=$this->Notice->show()?>
				<div class="panel">
					<div class="padding30-col">
						<div class="col-md-12 paidoffers">
							<h5><?=__d('referrals_contest', 'Referral\'s contest - "%s"', $contest['ReferralsContest']['title'])?></h5>
							<div class="margin30-top">
								<div class="row">
									<div class="col-md-12">
										<p><?=h($contest['ReferralsContest']['description'])?></p>
									</div>
									<div class="col-md-12 table-responsive">
										<table class="table table-striped table-hover">
											<thead class="thead-default">
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
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
