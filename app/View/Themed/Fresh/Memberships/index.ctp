<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('Upgrade Your Membership')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container dashpage">
	<div uk-grid>
		<div class="uk-width-1-1">
			<?=$this->Notice->show()?>
		</div>
	</div>
	<div class="uk-child-width-1-3@m uk-grid-small uk-grid-match" uk-grid>
		<div>
			<div class="uk-card uk-card-body pb">
				<h6><?=__('Purchase balance')?></h6>
				<h3><?=h($this->Currency->format($user['User']['purchase_balance']))?></h3>
				<?=$this->Html->link('<i title="Add funds" class="mdi mdi-arrow-up-drop-circle mdi-18px" uk-tooltip></i>', array('controller' => 'users', 'action' => 'deposit'), array(
					'escape' => false,
					))?>
			</div>
		</div>
		<div>
			<div class="uk-card uk-card-body">
				<h6><?=__('Current membership')?></h6>
				<h3><?=h($user['ActiveMembership']['Membership']['name'])?></h3>
			</div>
		</div>
		<div>
			<div class="uk-card uk-card-body">
				<h6><?=__('Membership valid until')?></h6>
				<h3><?=$user['ActiveMembership']['ends'] == null ? __('Unlimited') : h($user['ActiveMembership']['ends'])?></h3>
			</div>
		</div>
	</div>
	<div uk-grid>
		<div class="uk-width-1-1 uk-overflow-auto uk-margin-bottom">
			<?php $this->Currency->setFormatDefaults(array('zeroValue' => __('Free'))) ?>
			<table class="uk-table uk-table-striped uk-table-small">
				<thead>
					<tr>
						<th><?=__('Select membership for your needs')?></th>
						<?php foreach($memberships as $membership): ?>
						<th><?=h($membership['Membership']['name'])?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?=__('Direct referrals limit')?></td>
						<?php foreach($memberships as $membership): ?>
						<?php if($membership['Membership']['direct_referrals_limit'] == -1): ?>
						<td><?=__('Unlimited')?></td>
						<?php else: ?>
						<td><?=h($membership['Membership']['direct_referrals_limit'])?></td>
						<?php endif; ?>
						<?php endforeach; ?>
					</tr>
					<?php if(Configure::read('enableRentingReferrals')): ?>
					<tr>
						<td><?=__('Rented referrals limit')?></td>
						<?php foreach($memberships as $membership): ?>
						<?php if($membership['Membership']['rented_referrals_limit'] == -1): ?>
						<td><?=__('Unlimited')?></td>
						<?php else: ?>
						<td><?=h($membership['Membership']['rented_referrals_limit'])?></td>
						<?php endif; ?>
						<?php endforeach; ?>
					</tr>
					<?php endif; ?>
					<tr>
						<td><?=__('Direct referrals delete cost')?></td>
						<?php foreach($memberships as $membership): ?>
						<td><?=h($this->Currency->format($membership['Membership']['direct_referrals_delete_cost']))?></td>
						<?php endforeach; ?>
					</tr>
					<?php if(Configure::read('enableRentingReferrals')): ?>
					<tr>
						<td><?=__('Rented referrals expiry fee')?></td>
						<?php foreach($memberships as $membership): ?>
						<td><?=h($this->Currency->format($membership['Membership']['rented_referral_expiry_fee']))?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><?=__('Rented referrals recycle cost')?></td>
						<?php foreach($memberships as $membership): ?>
						<td><?=h($this->Currency->format($membership['Membership']['referral_recycle_cost'])); ?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><?=__('Inactive rented referral recycle time')?></td>
						<?php foreach($memberships as $membership): ?>
						<td><?=h($membership['Membership']['autorecycle_time']); ?> <?=__('days')?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><?=__('Time between renting')?></td>
						<?php foreach($memberships as $membership): ?>
						<td><?=__('%d days', $membership['Membership']['time_between_renting'])?></td>
						<?php endforeach; ?>
					</tr>
					<?php endif; ?>
					<tr>
						<td><?=__('Minimum payout')?></td>
						<?php foreach($memberships as $membership): ?>
						<td><?=h($membership['Membership']['minimum_cashout']); ?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><?=__('Maximum single payout')?></td>
						<?php foreach($memberships as $membership): ?>
						<td><?=h($this->Currency->format($membership['Membership']['maximum_cashout_amount'], array('zeroValue' => __('Unlimited'))))?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><?=__('Payout waiting time')?></td>
						<?php foreach($memberships as $membership): ?>
						<td><?=h($membership['Membership']['cashout_waiting_time']); ?> <?=__('days')?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><?=__('Instant payouts')?></td>
						<?php foreach($memberships as $membership): ?>
						<?php if($membership['Membership']['instant_cashouts']):?>
						<td><?=__('Yes')?></td>
						<?php else: ?>
						<td><?=__('No')?></td>
						<?php endif; ?>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><?=__('Referral upgrade commission')?></td>
						<?php foreach($memberships as $membership): ?>
						<td><?=h($this->Currency->format($membership['Membership']['upgrade_commission'])); ?></td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><?=__('Referral purchase commission')?></td>
						<?php foreach($memberships as $membership): ?>
						<td><?=h($membership['Membership']['purchase_commission']); ?>%</td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><?=__('Price / Duration')?></td>
						<?php foreach($memberships as $membership): ?>
						<?php if($membership['Membership']['status'] == 'Default'): ?>
						<td><?=__('Free / Unlimited')?></td>
						<?php else: ?>
						<td>
							<?=$this->UserForm->select('duration', $membership['Membership']['duration_select_data'], array(
								'empty' => false,
								'data-membership-id' => $membership['Membership']['id'],
								))?>
							<?php endif; ?>
						</td>
						<?php endforeach; ?>
					</tr>
					<tr>
						<td><?=__('Buy')?></td>
						<?php foreach($memberships as $membership): ?>
						<?php if($membership['Membership']['status'] != 'Default'): ?>
						<td>
							<?=$this->Html->link(__('Buy'), array('action' => 'buy', $membership['Membership']['id'], key($membership['Membership']['duration_select_data'])), array(
								'class' => 'uk-button uk-button-primary',
								'id' => 'buy_'.$membership['Membership']['id'],
								))?>
						</td>
						<?php else: ?>
						<td>
							<div class="uk-button uk-button-secondary"><?=__('Free');?></div>
						</td>
						<?php endif; ?>
						<?php endforeach; ?>
					</tr>
				</tbody>
			</table>
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
