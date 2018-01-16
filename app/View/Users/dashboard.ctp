<?=$this->element('userSidebar')?>
<div class="page">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 padding30-sides">
				<?=$this->element('userBreadcrumbs')?>
				<?=$this->Notice->show()?>
				<div class="panel">
					<div class="padding30-col">
						<div class="col-md-12">
							<h5><?=__('Your Account In Numbers')?></h5>
						</div>
						<div class="col-sm-3 moneypanel">
							<h6><?=__('Account Balance')?></h6>
							<h3><?=h($this->Currency->format($user['User']['account_balance']))?></h3>
						</div>
						<div class="col-sm-3 moneypanel">
							<h6><?=__('Purchase Balance')?></h6>
							<h3><?=h($this->Currency->format($user['User']['purchase_balance']))?></h3>
						</div>
						<div class="col-sm-3 moneypanel">
							<h6><?=__('Direct Referrals')?></h6>
							<h3><?=h($user['User']['refs_count'])?></h3>
						</div>
						<div class="col-sm-3 moneypanel">
							<h6><?=__('Rented Referrals')?></h6>
							<h3><?=h($user['User']['rented_refs_count'])?></h3>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="padding30-col">
						<div class="col-md-12">
							<h5 class="padding30-bottom"><?=__('Your Clicks This Week')?></h5>
							<div class="chartpanel">
								<?php
									echo $this->Chart->show(array(
											__('Your Clicks') => $myclicks,
										), array(
											'mode' => 'week',
											'label' => array(
												'content' => false,
												'class' => 'myclicks',
											),
											'width' => '100%',
											'height' => '150px'),
											array(
												'colors' => array('#2ecc71'), 
												'xAxis' => array('visible' => true, 'lineColor' => '#ffffff', 'tickWidth' => '0' , 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '12px', 'font-weight' => '700', 'font-family' => 'Lato'))),
												'yAxis' => array('gridLineColor' => '#ffffff', 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '14px', 'font-weight' => '700', 'font-family' => 'Lato'))),
										)
									)
									?>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="panel margin30-top">
					<div class="padding30-col">
						<div class="col-sm-6">
							<h5><?=__('Other Statistics')?></h5>
							<?php if($user['ActiveMembership']['Membership']['points_enabled']): ?>
								<div class="stats_line">
									<?=__('Points')?><span><?=h($user['User']['points'])?></span>
								</div>
							<?php endif; ?>
							<div class="stats_line">
								<?=__('Total Earinings')?><span><?=h($this->Currency->format($user['UserStatistic']['total_earnings']))?></span>
							</div>
							<div class="stats_line">
								<?=__('Your Own Clicks')?><span><?=h($user['UserStatistic']['total_clicks'])?></span>
							</div>
							<div class="stats_line">
								<?=__('Personal Earnings')?><span><?=h($this->Currency->format($user['UserStatistic']['total_clicks_earned']))?></span>
							</div>
							<div class="stats_line">
								<?=__('Rented Referral Clicks')?><span><?=h($user['UserStatistic']['total_rrefs_clicks'])?></span>
							</div>
							<div class="stats_line">
								<?=__('Direct Referral Clicks')?><span><?=h($user['UserStatistic']['total_drefs_clicks'])?></span>
							</div>
							<div class="stats_line">
								<?=__('Earned From Direct Referrals')?><span><?=h($this->Currency->format($user['UserStatistic']['total_drefs_clicks_earned']))?></span>
							</div>
							<div class="stats_line">
								<?=__('Earned From Rented Referrals')?><span><?=h($this->Currency->format($user['UserStatistic']['total_rrefs_clicks_earned']))?></span>
							</div>
							<div class="stats_line">
								<?=__('Credited Referral Commissions')?><span><?=h($this->Currency->format($user['UserStatistic']['credited_commissions']))?></span>
							</div>
							<?php if(bccomp($user['UserStatistic']['waiting_commissions'], '0') == 1): ?>
							<div class="stats_line">
								<?=__('Pending Referral Commissions')?><span><?=h($this->Currency->format($user['UserStatistic']['waiting_commissions']))?></span>
							</div>
							<?php endif; ?>
							<div class="stats_line">
								<?=__('Total Purchases')?><span><?=h($this->Currency->format($user['UserStatistic']['total_deposits']))?></span>
							</div>
							<div class="stats_line">
								<?=__('Total Received')?><span><?=h($this->Currency->format($user['UserStatistic']['total_cashouts']))?></span>
							</div>
							<?php if(bccomp($user['UserStatistic']['waiting_cashouts'], '0') == 1): ?>
							<div class="stats_line">
								<?=__('Pending Cashout')?><span><?=h($this->Currency->format($user['UserStatistic']['waiting_cashouts']))?></span>
							</div>
							<?php endif;?>
						</div>
						<div class="col-sm-6">
							<h5 class="padding30-bottom"><?=__('Direct Referrals Clicks')?></h5>
							<div class="chartpanel">
								<?php
									echo $this->Chart->show(array(
											__('Clicks') => $drclicks,
											__('Clicks credited') => $drclicksCredited,
										), array(
											'mode' => 'week',
											'label' => array(
												'content' => false,
												'class' => 'directrefs',
											),
											'width' => '100%',
											'height' => '150px'),
											array(
											'colors' => array('#e74c3c'), 
											'xAxis' => array('visible' => true, 'lineColor' => '#ffffff', 'tickWidth' => '0' , 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '8px', 'font-weight' => '700', 'font-family' => 'Lato'))),
											'yAxis' => array('gridLineColor' => '#ffffff', 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '14px', 'font-weight' => '700', 'font-family' => 'Lato'))),
										)
									)
									?>
							</div>
							<h5 class="padding30-bottom margin30-top"><?=__('Rented Referrals Clicks')?></h5>
							<?php
								echo $this->Chart->show(array(
										__('Clicks') => $rrclicks,
										__('Clicks credited') => $rrclicksCredited,
									), array(
										'mode' => 'week',
										'label' => array(
											'content' => false,
											'class' => 'directrefs',
										),
										'width' => '100%',
										'height' => '150px'),
										array(
										'colors' => array('#9b59b6'), 
										'xAxis' => array('visible' => true, 'lineColor' => '#ffffff', 'tickWidth' => '0' , 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '8px', 'font-weight' => '700', 'font-family' => 'Lato'))),
										'yAxis' => array('gridLineColor' => '#ffffff', 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '14px', 'font-weight' => '700', 'font-family' => 'Lato'))),
									)
								)
								?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
