<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 padding30-sides">
			<?=$this->Notice->show()?>
			<div class="panel margin30-top">
				<div class="padding30-col">
					<div class="col-sm-3 moneypanel">
						<h6><?=__('Purchase balance')?></h6>
						<h3><?=h($this->Currency->format($user['User']['purchase_balance']))?></h3>
					</div>
					<div class="col-sm-1 moneypanel moneypanelicon">
						<?=$this->Html->link('<i class="fa fa-chevron-up"></i>', array('controller' => 'users', 'action' => 'deposit'), array(
							'title' => __('Add funds'),
							'data-toggle' => 'tooltip',
							'data-placement' => 'left',
							'escape' => false,
							))?>
					</div>
					<div class="col-sm-4 moneypanel">
						<h6><?=__('Current membership')?></h6>
						<h3><?=h($user['ActiveMembership']['Membership']['name'])?></h3>
					</div>
					<div class="col-sm-4 moneypanel">
						<h6><?=__('Membership valid until')?></h6>
						<h3><?=$user['ActiveMembership']['ends'] == null ? __('Unlimited') : h($user['ActiveMembership']['ends'])?></h3>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			<div class="col-md-12 text-xs-center margin30-top">
				<?php $this->Currency->setFormatDefaults(array('zeroValue' => __('Free'))) ?>
				<table class="upgradetable titles">
					<tbody>
						<tr>
							<th>
								<h4><?=__('Select membership<br /> for your needs')?></h4>
							</th>
						</tr>
						<tr>
							<td><?=__('Direct referrals limit')?></td>
						</tr>
						<?php if(Configure::read('enableRentingReferrals')): ?>
							<tr>
								<td><?=__('Rented referrals limit')?></td>
							</tr>
						<?php endif; ?>
						<tr>
							<td><?=__('Direct referrals delete cost')?></td>
						</tr>
						<?php if(Configure::read('enableRentingReferrals')): ?>
							<tr>
								<td><?=__('Rented referrals expiry fee')?></td>
							</tr>
							<tr>
								<td><?=__('Rented referrals recycle cost')?></td>
							</tr>
							<tr>
								<td><?=__('Inactive rented referral recycle time')?></td>
							</tr>
							<tr>
								<td><?=__('Time between renting')?></td>
							</tr>
						<?php endif; ?>
						<tr>
							<td><?=__('Minimum payout')?></td>
						</tr>
						<tr>
							<td><?=__('Maximum single payout')?></td>
						</tr>
						<tr>
							<td><?=__('Payout waiting time')?></td>
						</tr>
						<tr>
							<td><?=__('Instant payouts')?></td>
						</tr>
						<tr>
							<td><?=__('Referral upgrade commission')?></td>
						</tr>
						<tr>
							<td><?=__('Referral purchase commission')?></td>
						</tr>
						<tr>
							<td><?=__('Price / Duration')?></td>
						</tr>
						<tr>
							<td><?=__('Buy')?></td>
						</tr>
					</tbody>
				</table>
				<?php foreach($memberships as $membership): ?>
				<table class="upgradetable memberships panel">
					<tbody>
						<tr>
							<th>
								<h4><?=h($membership['Membership']['name'])?><br />&nbsp;</h4>
							</th>
						</tr>
						<tr>
							<?php if($membership['Membership']['direct_referrals_limit'] == -1): ?>
							<td><?=__('Unlimited')?></td>
							<?php else: ?>
							<td><?=h($membership['Membership']['direct_referrals_limit'])?></td>
							<?php endif; ?>
						</tr>
						<?php if(Configure::read('enableRentingReferrals')): ?>
							<tr>
								<?php if($membership['Membership']['rented_referrals_limit'] == -1): ?>
								<td><?=__('Unlimited')?></td>
								<?php else: ?>
								<td><?=h($membership['Membership']['rented_referrals_limit'])?></td>
								<?php endif; ?>
							</tr>
						<?php endif; ?>
						<tr>
							<td><?=h($this->Currency->format($membership['Membership']['direct_referrals_delete_cost']))?></td>
						</tr>
						<?php if(Configure::read('enableRentingReferrals')): ?>
							<tr>
								<td><?=h($this->Currency->format($membership['Membership']['rented_referral_expiry_fee']))?></td>
							</tr>
							<tr>
								<td><?=h($this->Currency->format($membership['Membership']['referral_recycle_cost'])); ?></td>
							</tr>
							<tr>
								<td><?=h($membership['Membership']['autorecycle_time']); ?> <?=__('days')?></td>
							</tr>
							<tr>
								<td><?=__('%d days', $membership['Membership']['time_between_renting'])?></td>
							</tr>
						<?php endif; ?>
						<tr>
							<td><?=h($membership['Membership']['minimum_cashout']); ?></td>
						</tr>
						<tr>
							<td><?=h($this->Currency->format($membership['Membership']['maximum_cashout_amount'], array('zeroValue' => __('Unlimited'))))?></td>
						</tr>
						<tr>
							<td><?=h($membership['Membership']['cashout_waiting_time']); ?> <?=__('days')?></td>
						</tr>
						<tr>
							<td>
								<?php if($membership['Membership']['instant_cashouts']):?>
								<?=__('Yes')?>
								<?php else: ?>
								<?=__('No')?>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td><?=h($this->Currency->format($membership['Membership']['upgrade_commission'])); ?></td>
						</tr>
						<tr>
							<td><?=h($membership['Membership']['purchase_commission']); ?>%</td>
						</tr>
						<tr>
							<td>
								<?php if($membership['Membership']['status'] == 'Default'): ?>
								<?=__('Free / Unlimited')?>
								<?php else: ?>
								<?=$this->UserForm->select('duration', $membership['Membership']['duration_select_data'], array(
									'empty' => false,
									'data-membership-id' => $membership['Membership']['id'],
									))?>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td>
								<?php if($membership['Membership']['status'] != 'Default'): ?>
								<?=$this->Html->link(__('Buy'), array('action' => 'buy', $membership['Membership']['id'], key($membership['Membership']['duration_select_data'])), array(
									'class' => 'btn btn-membership',
									'id' => 'buy_'.$membership['Membership']['id'],
									))?>
								<?php else: ?>
								<div class="btn btn-membership free"><?=__('Free');?></div>
								<?php endif; ?>
							</td>
						</tr>
					</tbody>
				</table>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
</div>
<?php
	$this->Js->buffer("
		$(this).find('select[data-membership-id]').change(function() {
			var but = $(document).find('#buy_' + $(this).attr('data-membership-id'));
			but.attr('href', but.attr('href').substring(0, but.attr('href').lastIndexOf('/') + 1) + $(this).find('option:selected').val());
		});
	");
	?>
