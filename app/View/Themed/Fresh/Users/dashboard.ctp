<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Your Account In Numbers')?></h2>
			<div class="uk-child-width-1-4@m uk-grid-small uk-grid-match" uk-grid>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('Account Balance')?></h6>
						<h3><?=h($this->Currency->format($user['User']['account_balance']))?></h3>
					</div>
				</div>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('Purchase Balance')?></h6>
						<h3><?=h($this->Currency->format($user['User']['purchase_balance']))?></h3>
					</div>
				</div>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('Direct Referrals')?></h6>
						<h3><?=h($user['User']['refs_count'])?></h3>
					</div>
				</div>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('Rented Referrals')?></h6>
						<h3><?=h($user['User']['rented_refs_count'])?></h3>
					</div>
				</div>
			</div>
			<div uk-grid>
				<div class="uk-width-1-1">
					<h2><?=__('Your Clicks This Week')?></h2>
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
										'colors' => array('#8d99a5'), 
										'xAxis' => array('visible' => true, 'lineColor' => '#ffffff', 'tickWidth' => '0' , 'labels' => array('style' => array('color' => '#62687e', 'font-size' => '12px', 'font-weight' => '700', 'font-family' => 'Roboto'))),
										'yAxis' => array('gridLineColor' => '#ffffff', 'labels' => array('style' => array('color' => '#62687e', 'font-size' => '14px', 'font-weight' => '700', 'font-family' => 'Roboto'))),
								)
							)
							?>
					</div>
				</div>
			</div>
			<div class="uk-grid">
				<div class="uk-width-1-2@m">
					<h2 class="uk-margin-top"><?=__('Other Statistics')?></h2>
					<ul class="uk-list uk-list-divider otherstats">
						<?php if($user['ActiveMembership']['Membership']['points_enabled']): ?>
							<li><?=__('Points')?><span><?=h($user['User']['points'])?></span></li>
						<?php endif; ?>
						<li><?=__('Total Earinings')?><span><?=h($this->Currency->format($user['UserStatistic']['total_earnings']))?></span></li>
						<li><?=__('Your Own Clicks')?><span><?=h($user['UserStatistic']['total_clicks'])?></span></li>
						<li><?=__('Personal Earnings')?><span><?=h($this->Currency->format($user['UserStatistic']['total_clicks_earned']))?></span></li>
						<li><?=__('Rented Referral Clicks')?><span><?=h($user['UserStatistic']['total_rrefs_clicks'])?></span></li>
						<li><?=__('Direct Referral Clicks')?><span><?=h($user['UserStatistic']['total_drefs_clicks'])?></span></li>
						<li><?=__('Earned From Direct Referrals')?><span><?=h($this->Currency->format($user['UserStatistic']['total_drefs_clicks_earned']))?></span></li>
						<li><?=__('Earned From Rented Referrals')?><span><?=h($this->Currency->format($user['UserStatistic']['total_rrefs_clicks_earned']))?></span></li>
						<li><?=__('Credited Referral Commissions')?><span><?=h($this->Currency->format($user['UserStatistic']['credited_commissions']))?></span></li>
						<?php if(bccomp($user['UserStatistic']['waiting_commissions'], '0') == 1): ?>
						<li><?=__('Pending Referral Commissions')?><span><?=h($this->Currency->format($user['UserStatistic']['waiting_commissions']))?></span></li>
						<?php endif; ?>
						<li><?=__('Total Purchases')?><span><?=h($this->Currency->format($user['UserStatistic']['total_deposits']))?></span></li>
						<li><?=__('Total Received')?><span><?=h($this->Currency->format($user['UserStatistic']['total_cashouts']))?></span></li>
						<?php if(bccomp($user['UserStatistic']['waiting_cashouts'], '0') == 1): ?>
						<li><?=__('Pending Cashout')?><span><?=h($this->Currency->format($user['UserStatistic']['waiting_cashouts']))?></span></li>
						<?php endif;?>
					</ul>
				</div>
				<div class="uk-width-1-2@m">
					<h2 class="uk-margin-top"><?=__('Direct Referrals Clicks')?></h2>
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
									'colors' => array('#3abfbe'), 
									'xAxis' => array('visible' => true, 'lineColor' => '#ffffff', 'tickWidth' => '0' , 'labels' => array('style' => array('color' => '#62687e', 'font-size' => '8px', 'font-weight' => '700', 'font-family' => 'Roboto'))),
									'yAxis' => array('gridLineColor' => '#ffffff', 'labels' => array('style' => array('color' => '#62687e', 'font-size' => '12px', 'font-weight' => '700', 'font-family' => 'Roboto'))),
								)
							)
							?>
					</div>
					<h2 class="uk-margin-large-top"><?=__('Rented Referrals Clicks')?></h2>
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
								'colors' => array('#1d9ff4'), 
								'xAxis' => array('visible' => true, 'lineColor' => '#ffffff', 'tickWidth' => '0' , 'labels' => array('style' => array('color' => '#62687e', 'font-size' => '8px', 'font-weight' => '700', 'font-family' => 'Roboto'))),
								'yAxis' => array('gridLineColor' => '#ffffff', 'labels' => array('style' => array('color' => '#62687e', 'font-size' => '12px', 'font-weight' => '700', 'font-family' => 'Roboto'))),
							)
						)
						?>
				</div>
			</div>
		</div>
	</div>
</div>
