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
							<h5><?=__d('referrals_contest', 'Referral\'s contest')?></h5>
							<div class="margin30-top">
								<ul class="nav nav-tabs">
									<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#current"><?=__d('referrals_contest', 'Current contests')?></a></li>
									<?php if(isset($old)): ?>
									<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#old"><?=__d('referrals_contest', 'Old contests')?></a></li>
									<?php endif; ?>
									<?php if(isset($future)): ?>
									<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#incoming"><?=__d('referrals_contest', 'Incoming contests')?></a></li>
									<?php endif; ?>
								</ul>
							</div>
							<div class="tab-content">
								<div id="current" class="tab-pane fade in active">
									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-condensed">
												<thead class="thead-default">
													<tr>
														<th><?=__d('referrals_contest', 'Title')?></th>
														<th><?=__d('referrals_contest', 'From')?></th>
														<th><?=__d('referrals_contest', 'To')?></th>
														<th><?=__d('referrals_contest', 'Places')?></th>
														<th><?=__d('referrals_contest', 'Actions')?></th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($current as $contest): ?>
													<tr>
														<td><?=h($contest['ReferralsContest']['title'])?></td>
														<td><?=$this->Time->nice($contest['ReferralsContest']['starts'])?></td>
														<td><?=$this->Time->nice($contest['ReferralsContest']['ends'])?></td>
														<td><?=count($contest['ReferralsContest']['prizes'])?></td>
														<td>
															<?=
																$this->Html->link('<i class="fa fa-eye"></i>', array(
																	'action' => 'view',
																	$contest['ReferralsContest']['id'],
																), array(
																	'escape' => false,
																))
																?>
														</td>
													</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<?php if(isset($old)): ?>
								<div id="old" class="tab-pane fade in">
									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-condensed">
												<thead class="thead-default">
													<tr>
														<th><?=__d('referrals_contest', 'Title')?></th>
														<th><?=__d('referrals_contest', 'From')?></th>
														<th><?=__d('referrals_contest', 'To')?></th>
														<th><?=__d('referrals_contest', 'Places')?></th>
														<th><?=__d('referrals_contest', 'Actions')?></th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($old as $contest): ?>
													<tr>
														<td><?=h($contest['ReferralsContest']['title'])?></td>
														<td><?=$this->Time->nice($contest['ReferralsContest']['starts'])?></td>
														<td><?=$this->Time->nice($contest['ReferralsContest']['ends'])?></td>
														<td><?=count($contest['ReferralsContest']['prizes'])?></td>
														<td>
															<?=
																$this->Html->link('<i class="fa fa-eye"></i>', array(
																	'action' => 'view',
																	$contest['ReferralsContest']['id'],
																), array(
																	'escape' => false,
																))
																?>
														</td>
													</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<?php endif;?>
								<?php if(isset($future)): ?>
								<div id="incoming" class="tab-pane fade in">
									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-condensed">
												<thead class="thead-default">
													<tr>
														<th><?=__d('referrals_contest', 'Title')?></th>
														<th><?=__d('referrals_contest', 'From')?></th>
														<th><?=__d('referrals_contest', 'To')?></th>
														<th><?=__d('referrals_contest', 'Places')?></th>
														<th><?=__d('referrals_contest', 'Actions')?></th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($future as $contest): ?>
													<tr>
														<td><?=h($contest['ReferralsContest']['title'])?></td>
														<td><?=$this->Time->nice($contest['ReferralsContest']['starts'])?></td>
														<td><?=$this->Time->nice($contest['ReferralsContest']['ends'])?></td>
														<td><?=count($contest['ReferralsContest']['prizes'])?></td>
														<td>
															<?=
																$this->Html->link('<i class="fa fa-eye"></i>', array(
																	'action' => 'view',
																	$contest['ReferralsContest']['id'],
																), array(
																	'escape' => false,
																))
																?>
														</td>
													</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<?php endif;?>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
