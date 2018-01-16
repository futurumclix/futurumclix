<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__d('referrals_contest', 'Referral\'s contest')?></h2>
			<ul class="uk-margin-top" uk-tab uk-switcher="animation: uk-animation-fade">
				<li><a href="#current"><?=__d('referrals_contest', 'Current contests')?></a></li>
				<?php if(isset($old)): ?>
				<li><a href="#old"><?=__d('referrals_contest', 'Old contests')?></a></li>
				<?php endif; ?>
				<?php if(isset($future)): ?>
				<li><a href="#incoming"><?=__d('referrals_contest', 'Incoming contests')?></a></li>
				<?php endif; ?>
			</ul>
			<ul class="uk-switcher uk-margin">
				<li id="current">
					<div class="uk-overflow-auto">
						<table class="uk-table uk-table-small">
							<thead>
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
											$this->Html->link('<i class="mdi mdi-eye"></i>', array(
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
				</li>
				<?php if(isset($old)): ?>
				<li id="old">
					<div id="old" class="uk-overflow-auto">
						<table class="uk-table uk-table-small">
							<thead>
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
											$this->Html->link('<i class="mdi mdi-eye"></i>', array(
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
					<?php endif;?>
				</li>
				<?php if(isset($future)): ?>
				<li id="incoming">
					<div id="old" class="uk-overflow-auto">
						<table class="uk-table uk-table-small">
							<thead>
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
				</li>
				<?php endif;?>
			</ul>
		</div>
	</div>
</div>
