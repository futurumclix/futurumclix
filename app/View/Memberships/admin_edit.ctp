<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Edit membership - details')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#details"><?=__d('admin', 'Details')?></a></li>
		<li><a data-toggle="tab" href="#referrals"><?=__d('admin', 'Referrals Settings')?></a></li>
		<li><a data-toggle="tab" href="#referralsPrices"><?=__d('admin', 'Referrals Prices')?></a></li>
		<li><a data-toggle="tab" href="#bonus"><?=__d('admin', 'Commissions And Bonuses')?></a></li>
		<li><a data-toggle="tab" href="#points"><?=__d('admin', 'Points')?></a></li>
		<?php if($this->request->data['Membership']['status'] != 'Default'): ?>
			<li><a data-toggle="tab" href="#prices"><?=__d('admin', 'Prices And Availability')?></a></li>
		<?php endif; ?>
	</ul>
	<div class="tab-content">
		<div id="details" class="tab-pane fade in active">
			<?=$this->AdminForm->create('Membership', array('class' => 'form-horizontal'))?>
			<?=$this->AdminForm->input('Membership.id')?>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Membership Name')?></label>
				<div class="col-sm-6">
					<?=$this->AdminForm->input('name')?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Direct Referrals Limit')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input('direct_referrals_limit', array(
							'data-toggle' => 'popover', 
							'data-placement' => 'top',
							'data-trigger' => 'focus',
							'data-content' => __d('admin', 'Put -1 to no limit, 0 to disable direct referrals'),
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Direct Referrals Delete Cost')?></label>
				<div class="col-sm-6">
					<div class="input-group">
						<?=
							$this->AdminForm->input('direct_referrals_delete_cost', array(
								'data-toggle' => 'popover', 
								'data-placement' => 'top',
								'data-trigger' => 'focus',
								'data-content' => __d('admin', 'How much user will pay for every direct referral manual removal.'),
							))
						?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Rented Referrals Limit')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input('rented_referrals_limit', array(
							'data-toggle' => 'popover', 
							'data-placement' => 'top',
							'data-trigger' => 'focus',
							'data-content' => __d('admin', 'Put -1 to no limit, 0 to disable rented referrals'),
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Minimum Payout')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input('minimum_cashout', array(
							'data-toggle' => 'popover', 
							'data-placement' => 'top',
							'data-trigger' => 'focus',
							'data-content' => __d('admin', 'Please put minimum payout for this membership. You can use stepped cashout, just put another value after comma like: 2,4,6.'),
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Payout Waiting Time')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input('cashout_waiting_time', array(
							'data-toggle' => 'popover', 
							'data-placement' => 'top',
							'data-trigger' => 'focus',
							'data-content' => __d('admin', 'Please put how long user have to wait in days between cashing out.'),
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Allow More Than One Pending Payout Request')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input('allow_more_cashouts', array(
							'data-toggle' => 'popover', 
							'data-placement' => 'top',
							'data-trigger' => 'hover',
							'data-content' => __d('admin', 'Allow user to place another payout request even if he has already placed one and it is still pending.'),
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Instant Payouts')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input('instant_cashouts', array(
							'data-toggle' => 'popover', 
							'data-placement' => 'top',
							'data-trigger' => 'hover',
							'data-content' => __d('admin', 'Activate instant payouts for this membership.'),
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Maximum Single Withdraw Amount')?></label>
				<div class="col-sm-6">
					<div class="input-group">
						<?=
							$this->AdminForm->input('maximum_cashout_amount', array(
								'data-toggle' => 'popover', 
								'data-placement' => 'top',
								'data-trigger' => 'focus',
								'data-content' => __d('admin', 'Please put how much user can withdraw per one cashout. Put 0 to disable.'),
							))
						?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Limit Total Withdraw Amount')?></label>
				<div class="col-sm-6">
				<?php
					$value = $this->AdminForm->input('total_cashouts_limit_value', array(
						'data-toggle' => 'popover',
						'data-trigger' => 'focus',
						'data-placement' => 'top',
						'data-content' => __d('admin', 'This is the maximum amount that user having this membership can payout in total.'),
					));
					$percent = $this->AdminForm->input('total_cashouts_limit_percentage', array(
						'data-toggle' => 'popover',
						'data-trigger' => 'focus',
						'data-placement' => 'top',
						'data-content' => __d('admin', 'This is the maximum amount that user having this membership can payout in total. This options sums all payment coming in like deposits or purchases done with payment processors. You can set how much percent of a deposits sum you allow to payout.'),
					));
					$percent_rr = $this->AdminForm->input('maximum_roi', array(
						'data-toggle' => 'popover',
						'data-trigger' => 'focus',
						'data-placement' => 'top',
						'data-content' => __d('admin', 'This is the maximum amount that user having this membership can payout in total. This options sums all payment coming in like deposits or purchases done with payment processors. You can set how much percent of a deposits sum you allow to payout.'),
					));
					echo $this->AdminForm->radio('total_cashouts_limit_mode', array(
						'<div class="radio">'.__d('admin', 'None').'</div>',
						'<div class="radio"><div class="input-group" style="top: 13px;"><div class="input-group-addon">'.__d('admin', 'Value').'</div>'.$value.'</div></div>',
						'<div class="radio"><div class="input-group" style="top: 13px;"><div class="input-group-addon">'.__d('admin', 'Percentage of Deposits').'</div>'.$percent.'</div></div>',
						'<div class="radio"><div class="input-group" style="top: 13px;"><div class="input-group-addon">'.__d('admin', 'Percentage of Rented Referrals Income').'</div>'.$percent_rr.'</div></div>',
					), array(
						'separator' => '<br />',
						'legend' => false,
						'label' => false,
						'class' => 'form-inline',
					))
				?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Results Per Page')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input('results_per_page', array(
							'data-toggle' => 'popover', 
							'data-placement' => 'top',
							'data-trigger' => 'focus',
							'data-content' => __d('admin', 'How many results do you want to show per page (like direct referrals, rented referrals)'),
						))
					?>
				</div>
			</div>
			<div class="col-sm-12 text-center paddingten">
				<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
			</div>
			<?=$this->AdminForm->end()?>
		</div>
		<div id="referrals" class="tab-pane fade in">
			<?=$this->AdminForm->create('Membership', array('class' => 'form-horizontal'))?>
			<?=$this->AdminForm->input('Membership.id')?>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Time between Renting')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input('time_between_renting', array(
							'data-toggle' => 'popover', 
							'data-placement' => 'top',
							'data-trigger' => 'focus',
							'data-content' => __d('admin', 'Please put how long user have to wait in days between renting.'),
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Available Referrals Pack')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input('available_referrals_packs', array(
							'data-toggle' => 'popover', 
							'data-placement' => 'top',
							'data-trigger' => 'focus',
							'data-content' => __d('admin', 'Please set how many referrals will be available in one pack, you can put different values after decimal like: 10,20,50.'),
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Referral Recycle Cost')?></label>
				<div class="col-sm-6">
					<div class="input-group">
						<?=$this->AdminForm->input('referral_recycle_cost')?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Referral Expire Cost')?></label>
				<div class="col-sm-6">
					<div class="input-group">
						<?=
							$this->AdminForm->input('rented_referral_expiry_fee', array(
								'data-toggle' => 'popover', 
								'data-placement' => 'top',
								'data-trigger' => 'focus',
								'data-content' => __d('admin', 'If referral will be expired and user will not renew it, this is a fee for referral expiry.'),
							))
						?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Autorecycle Time')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input('autorecycle_time', array(
							'data-toggle' => 'popover', 
							'data-placement' => 'top',
							'data-trigger' => 'focus',
							'data-content' => __d('admin', 'After how many days do you want to replace inactive rented referral. Put 0 to disable autorecycling.'),
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'AutoPay Trigger Days')?></label>
				<div class="col-sm-6">
					<?=$this->AdminForm->input('autopay_trigger_days')?>
				</div>
			</div>
			<div class="col-sm-12 text-center paddingten">
				<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
			</div>
			<?=$this->AdminForm->end()?>
		</div>
		<div id="referralsPrices" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('admin', 'Referral prices')?>
					<div style="float: right;">
						<?=
							$this->AdminForm->postLink('<i title="'.__d('admin', 'Click to delete all ranges').'" data-toggle="tooltip" data-placement="left" class="fa fa-minus-circle fa-lg"></i>',
								array('controller' => 'RentedReferralsPrices', 'action' => 'deleteByMembership', $this->request->data['Membership']['id']),
								array('escape' => false),
								__d('admin', 'Are you sure you want to delete all ranges?')
							)
						?>
					</div>
				</h2>
			</div>
			<?=$this->AdminForm->create('Membership', array('class' => 'form-horizontal'))?>
			<?=$this->AdminForm->input('Membership.id')?>
			<div id="rentingPricesTable">
				<?php $prices = &$this->request->data['RentedReferralsPrice']; $max = count($prices); ?>
				<?php if($max == 0): ?>
					<div id="addRowButton" class="text-center"><i title="<?=__d('admin', 'Click to add another range')?>" data-toggle="tooltip" data-placement="left" class="fa fa-plus-circle fa-lg"></i></div>
				<?php endif; ?> 
				<?php for($i = 0; $i < $max; ++$i): $p = &$prices[$i];?>
				<div class="form-group">
					<?php if(isset($p['id'])): ?>
					<?=
						$this->AdminForm->input("RentedReferralsPrice.$i.id", array(
							'type' => 'hidden',
							'value' => $p['id'],
						));
					?>
					<?php endif; ?>
					<div class="col-sm-3">
						<div class="input-group">
							<div class="input-group-addon"><?=__d('admin', 'From')?></div>
							<?=
								$this->AdminForm->input("RentedReferralsPrice.$i.min", array(
									'type' => 'number',
									'readonly' => true,
									'data-toggle' => 'popover', 
									'data-placement' => 'top',
									'data-trigger' => 'focus',
									'data-content' => __d('admin', 'Minimum range of referrals for this price'),
									'value' => isset($p['min']) ? $p['min'] : '',
								));
							?>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="input-group">
							<div class="input-group-addon"><?=__d('admin', 'To')?></div>
								<?php if($p['max'] == 65535): ?>
									<?=
										$this->AdminForm->button(__d('admin', 'Infinite'), array(
											'class' => 'btn btn-default',
											'type' => 'button',
											'id' => "RentedReferralsPrice{$i}Infinite",
											'data-toggle' => 'popover', 
											'data-placement' => 'top',
											'data-trigger' => 'hover',
											'data-content' => __d('admin', 'Click to specify range'),
											'data-row' => $i,
										));
									?>
								<?=
									$this->AdminForm->input("RentedReferralsPrice.$i.max", array(
										'type' => 'hidden',
										'value' =>'65535',
									));
								?>
								<?php else: ?>
									<?=
										$this->AdminForm->input("RentedReferralsPrice.$i.max", array(
											'type' => 'number',
											'data-toggle' => 'popover', 
											'data-placement' => 'top',
											'data-trigger' => 'focus',
											'data-content' => __d('admin', 'Please put maximum range of referrals for this price'),
											'value' => isset($p['max']) ? $p['max'] : '',
										));
									?>
								<?php endif; ?>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="input-group">
							<div class="input-group-addon"><?=__d('admin', 'Price')?></div>
							<?=
								$this->AdminForm->input("RentedReferralsPrice.$i.price", array(
									'data-toggle' => 'popover', 
									'data-placement' => 'top',
									'data-trigger' => 'focus',
									'data-content' => __d('admin', 'Please put price for this range'),
									'value' => isset($p['price']) ? $p['price'] : '',
								));
							?>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="input-group">
							<div class="input-group-addon"><?=__d('admin', 'AutoPay Price')?></div>
							<?=
								$this->AdminForm->input("RentedReferralsPrice.$i.autopay_price", array(
									'data-toggle' => 'popover', 
									'data-placement' => 'top',
									'data-trigger' => 'focus',
									'data-content' => __d('admin', 'Please put autopay price for this range'),
									'value' => isset($p['autopay_price']) ? $p['autopay_price'] : '',
								));
							?>
						</div>
					</div>
					<?php if($i == $max - 1): ?>
					<div id="addRowButton" class="col-sm-12 text-center paddingten"><i title="<?=__d('admin', 'Click to add another range')?>" data-toggle="tooltip" data-placement="left" class="fa fa-plus-circle fa-lg"></i></div>
					<?php endif; ?>
				</div>
				<?php endfor; ?>
			</div>
			<div class="col-sm-12 text-center paddingten">
				<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
			</div>
			<?=$this->AdminForm->end()?>
		</div>
		<div id="bonus" class="tab-pane fade in">
			<?=$this->AdminForm->create('Membership', array('class' => 'form-horizontal'))?>
			<?=$this->AdminForm->input('Membership.id')?>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Direct Referral\'s Upgrade Commission')?></label>
				<div class="col-sm-6">
					<div class="input-group">
						<?=
							$this->AdminForm->input('upgrade_commission', array(
								'data-toggle' => 'popover', 
								'data-placement' => 'top',
								'data-trigger' => 'focus',
								'data-content' => __d('admin', 'How much (in %s) this user will get for every upgrade bought by his referrals.', $this->Currency->name()),
							))
						?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Direct Referral\'s Fund Purchase Balance Commission')?></label>
				<div class="col-sm-6">
					<div class="input-group">
						<?=
							$this->AdminForm->input('fund_commission', array(
								'data-toggle' => 'popover',
								'data-placement' => 'top',
								'data-trigger' => 'focus',
								'data-content' => __d('admin', 'How much (in percent) this user will get for every purchase done by his referrals.'),
								'min' => 0,
								'max' => 100,
								'step' => '0.001',
							))
						?>
						<div class="input-group-addon">%</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Direct Referral\'s Purchases Commission')?></label>
				<div class="col-sm-6">
					<div class="input-group">
						<?=
							$this->AdminForm->input('purchase_commission', array(
								'data-toggle' => 'popover', 
								'data-placement' => 'top',
								'data-trigger' => 'focus',
								'data-content' => __d('admin', 'How much (in percent) this user will get for every  purchase of his referral like referrals, advertisements, excluding upgrades.'),
								'min' => 0,
								'max' => 100,
								'step' => '0.001',
							))
						?>
						<div class="input-group-addon">%</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Choose Items Which Purchase Will Be Commissioned')?></label>
				<div class="col-sm-6">
					<div class="input-group" style="width: 100%;">
						<?=
							$this->AdminForm->input('commission_items', array(
								'type' => 'select',
								'class' => 'fancy form-control',
								'multiple' => 'multiple',
								'options' => $availableItems,
								'style' => 'height: 200px;',
							))
						?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Count Commission While Transferring From Main Balance To Purchase Balance')?></label>
				<div class="col-sm-6">
						<?=$this->AdminForm->input('transfering_commission')?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Max Purchase Commission Per Referral')?></label>
				<div class="col-sm-6">
					<div class="input-group">
						<?=
							$this->AdminForm->input('max_purchase_commission_referral', array(
								'data-content' => __d('admin', 'How much (in %s) this user will get in total per one referral\'s purchases. Put 0 for no limit.', $this->Currency->name()),
								'data-trigger' => 'focus',
								'data-toggle' => 'popover', 
								'data-placement' => 'top',
							))
						?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Max Purchase Commission Per Transaction')?></label>
				<div class="col-sm-6">
					<div class="input-group">
						<?=
							$this->AdminForm->input('max_purchase_commission_transaction', array(
								'data-content' => __d('admin', 'What maximum amount (in %s) this user will get per one referral\'s purchase. Put 0 for no limit.', $this->Currency->name()),
								'data-trigger' => 'focus',
								'data-toggle' => 'popover', 
								'data-placement' => 'top',
							))
						?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-6 control-label"><?=__d('admin', 'Add Commission After')?></label>
				<div class="col-sm-6">
					<div class="input-group">
						<?=
							$this->AdminForm->input('commission_delay', array(
								'data-content' => __d('admin', 'After how many days add upgrade or purchase commission to upline\'s account balance (put 0 for instant)'),
								'data-trigger' => 'focus',
								'data-toggle' => 'popover', 
								'data-placement' => 'top',
							))
						?>
						<div class="input-group-addon"><?=__d('admin', 'days')?></div>
					</div>
				</div>
			</div>
			<div class="col-sm-12 text-center paddingten">
				<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
			</div>
			<?=$this->AdminForm->end()?>
		</div>
		<div id="points" class="tab-pane fade in">
			<?=$this->AdminForm->create('Membership', array('class' => 'form-horizontal'))?>
				<?=$this->AdminForm->input('Membership.id')?>
				<div class="form-group">
					<label class="col-sm-6 control-label"><?=__d('admin', 'Enable points for this membership')?></label>
					<div class="col-sm-6">
						<?=
							$this->AdminForm->input('points_enabled', array(
								'data-toggle' => 'popover', 
								'data-placement' => 'bottom',
								'data-trigger' => 'hover',
								'data-content' => __d('admin', 'Enable earning points for this membership.'),
								'type' => 'checkbox',
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label"><?=__d('admin', 'Points per direct referral')?></label>
					<div class="col-sm-6">
						<div class="input-group">
							<?=$this->AdminForm->input('points_per_dref')?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label"><?=__d('admin', 'Points per one rented referral')?></label>
					<div class="col-sm-6">
						<div class="input-group">
							<?=$this->AdminForm->input('points_per_rref')?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label"><?=__d('admin', 'Points per forum topic')?></label>
					<div class="col-sm-6">
						<div class="input-group">
							<?=$this->AdminForm->input('points_per_topic')?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label"><?=__d('admin', 'Points per forum post')?></label>
					<div class="col-sm-6">
						<div class="input-group">
							<?=$this->AdminForm->input('points_per_post')?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label"><?=__d('admin', 'Points per approved paid offer')?></label>
					<div class="col-sm-6">
						<div class="input-group">
							<?=$this->AdminForm->input('points_per_paid_offer')?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label"><?=__d('admin', 'Points for upgrading to this membership')?></label>
					<div class="col-sm-6">
						<div class="input-group">
							<?=$this->AdminForm->input('points_for_upgrade')?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label"><?=__d('admin', 'Points for deposit')?></label>
					<div class="col-sm-6">
						<div class="input-group">
							<?=$this->AdminForm->input('points_per_deposit')?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label"><?=__d('admin', 'Converting points')?></label>
					<div class="col-sm-6">
						<?=
							$this->AdminForm->input('points_conversion', array(
								'options' => $this->Utility->enum('Membership', 'points_conversion'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label"><?=__d('admin', 'One point value (for converting)')?></label>
					<div class="col-sm-6">
						<div class="input-group">
							<?=$this->AdminForm->input('points_value')?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-6 control-label"><?=__d('admin', 'Minimum points amount to convert')?></label>
					<div class="col-sm-6">
						<div class="input-group">
							<?=$this->AdminForm->input('points_value')?>
						</div>
					</div>
				</div>
				<div class="col-sm-12 text-center paddingten">
					<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<?php if($this->request->data['Membership']['status'] != 'Default'): ?>
			<div id="prices" class="tab-pane fade in">
				<?=$this->AdminForm->create('Membership', array('class' => 'form-horizontal'))?>
				<?=$this->AdminForm->input('Membership.id')?>
				<div class="title2">
					<h2><?=__d('admin', 'Period and prices')?></h2>
				</div>
				<div class="form-group">
					<label class="col-sm-4 text-center"><?=__d('admin', 'Period')?></label>
					<label class="col-sm-5 text-center"><?=__d('admin', 'Price')?></label>
					<label class="col-sm-3 text-center"><?=__d('admin', 'Availability')?></label>
				</div>
				<div class="form-group">
					<div class="col-sm-4 text-center">
						<?=__d('admin', '1 month')?>
					</div>
					<div class="col-sm-5 text-center">
						<div class="input-group">
							<?=$this->AdminForm->input('1_month_price')?>
						</div>
					</div>
					<div class="col-sm-3 text-center">
						<div class="input-group">
							<span class="input-group-addon">
							<?=$this->AdminForm->input('1_month_active', array('onclick' => "enablePriceInput('Membership1MonthPrice', this.checked)", 'class' => ''))?>
							</span>
							<div class="form-control"><?=__d('admin', 'Enabled')?></div>
						</div>
					</div>
				</div>
				<?php for($i = 2; $i <= 12; $i++): ?>
				<div class="form-group">
					<div class="col-sm-4 text-center">
						<?=__d('admin', '%d months', $i)?>
					</div>
					<div class="col-sm-5 text-center">
						<div class="input-group">
							<?=$this->AdminForm->input($i.'_months_price')?>
						</div>
					</div>
					<div class="col-sm-3 text-center">
						<div class="input-group">
							<span class="input-group-addon">
							<?=$this->AdminForm->input($i.'_months_active', array('onclick' => "enablePriceInput('Membership{$i}MonthsPrice', this.checked)", 'class' => ''))?>
							</span>
							<div class="form-control"><?=__d('admin', 'Enabled')?></div>
						</div>
					</div>
				</div>
				<?php endfor; ?>
				<div class="col-sm-12 text-center paddingten">
					<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
				</div>
				<?=$this->AdminForm->end()?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php
$from = __d('admin', 'From');
$to = __d('admin', 'To');
$price = __d('admin', 'Price');
$autoPayPrice = __d('admin', 'AutoPay Price');
$fromInput = $this->AdminForm->input('RentedReferralsPrice.NUM.min', array(
					'type' => 'number',
					'disabled' => true,
				 ));
$toInput = $this->AdminForm->input('RentedReferralsPrice.NUM.max', array(
					'type' => 'number',
					'data-toggle' => 'popover', 
					'data-placement' => 'top',
					'data-trigger' => 'focus',
					'data-content' => __d('admin', 'Please put maximum range of referrals for this price'),
			  ));
$priceInput = $this->AdminForm->input('RentedReferralsPrice.NUM.price', array(
						'type' => 'money',
						'data-toggle' => 'popover', 
						'data-placement' => 'top',
						'data-trigger' => 'focus',
						'data-content' => __d('admin', 'Please put price for this range'),
					));
$autoPayPriceInput = $this->AdminForm->input('RentedReferralsPrice.NUM.autopay_price', array(
								'type' => 'money',
								'data-toggle' => 'popover', 
								'data-placement' => 'top',
								'data-trigger' => 'focus',
								'data-content' => __d('admin', 'Please put autopay price for this range'),
							));
$button = $this->AdminForm->button(__d('admin', 'Infinite'), array(
				'class' => 'btn btn-default',
				'type' => 'button',
				'id' => 'RentedReferralsPriceNUMInfinite',
				'data-toggle' => 'popover', 
				'data-placement' => 'top',
				'data-trigger' => 'hover',
				'data-content' => __d('admin', 'Click to specify range'),
			));
$infinite = $this->AdminForm->input('RentedReferralsPrice.NUM.max', array(
				'type' => 'hidden',
				'value' =>'65535',
			));
$this->Js->buffer("
	enablePriceInput('Membership1MonthPrice', $('#Membership1MonthActive').is(':checked'));
	for(var i = 2; i <= 12; i++) {
		enablePriceInput('Membership' + i + 'MonthsPrice', $('#Membership' + i + 'MonthsActive').is(':checked'));
	}
	var no = $max;

	function buttonInfiniteClick(event) {
		event.preventDefault();

		var toAdd = $('<div class=\"input-group-addon\">$to</div>' + '$toInput'.replace(/NUM/g, '' + (no - 1)));
		$(this).parent().empty().html(toAdd);

		if(no >= 2) {
			var newVal = parseInt($('#RentedReferralsPrice' + (no - 2) + 'Max').val()) + 2;
			$('#RentedReferralsPrice' + (no - 1) + 'Max').attr('min', newVal).val(newVal);
		}
		$('#RentedReferralsPrice' + (no - 1) + 'Max').on('change input', maxChange).popover();
	}

	function maxChange() {
		var number = parseInt($(this).attr('id').slice('RentedReferralsPrice'.length));

		for(;number < 250; number++) {
			var max = parseInt($('#RentedReferralsPrice' + number + 'Max').val()) + 1;

			$('#RentedReferralsPrice' + (number + 1) + 'Min').val(max);
			$('#RentedReferralsPrice' + (number + 1) + 'Max').attr('min', max + 1);

			if(parseInt($('#RentedReferralsPrice' + (number + 1) + 'Max').val()) <= max) {
				$('#RentedReferralsPrice' + (number + 1) + 'Max').val(max + 1);
			}

			if(!$('#RentedReferralsPrice' + (number + 1) + 'Min').length) {
				break;
			}
		}
	}

	$('#addRowButton').click(function clickFunc() {
		var row = '<div class=\"form-group\"><div class=\"col-sm-3\"><div class=\"input-group\"><div class=\"input-group-addon\">$from</div>$fromInput</div></div><div class=\"col-sm-3\"><div class=\"input-group\"><div class=\"input-group-addon\">$to</div>$button$infinite</div></div><div class=\"col-sm-3\"><div class=\"input-group\"><div class=\"input-group-addon\">$price</div>$priceInput</div></div><div class=\"col-sm-3\"><div class=\"input-group\"><div class=\"input-group-addon\">$autoPayPrice</div>$autoPayPriceInput</div></div></div>';
		var newMinVal = 1;

		if(no >= 1) {
			newMinVal = $('#RentedReferralsPrice' + (no - 1) + 'Max').val();
			if(typeof newMinVal == 'undefined' || !newMinVal) {
				$('#RentedReferralsPrice' + (no - 1) + 'Max').addClass('form-error');
				return;
			} else if(newMinVal == 65535) {
				$('#RentedReferralsPrice' + (no - 1) + 'Infinite').addClass('form-error');
				return;
			}
			$('#RentedReferralsPrice' + (no - 1) + 'Max').removeClass('form-error');
			newMinVal = parseInt(newMinVal) + 1;
		}

		row = $(row.replace(/NUM/g, '' + no++));

		$(row).find('#RentedReferralsPrice' + (no - 1) + 'Min').val(newMinVal);

		if($(row).find('#RentedReferralsPrice' + (no - 1) + 'Max').attr('type') != 'hidden') {
			$(row).find('#RentedReferralsPrice' + (no - 1) + 'Max').val(newMinVal + 1);
			$(row).find('#RentedReferralsPrice' + (no - 1) + 'Max').attr('min', newMinVal + 1);
		}

		var button = $('#addRowButton');
		button.find('i').mouseleave();
		$('#rentingPricesTable').find(':last').append($('<td colspan=\"2\"></td>'));
		$('#rentingPricesTable').append(row.append(button));
		if($('#rentingPricesTable').find(':first').find('input').length == 0) {
			$('#rentingPricesTable').find(':first').remove();
		}

		$('#RentedReferralsPrice' + (no - 1) + 'Price').popover();
		$('#RentedReferralsPrice' + (no - 1) + 'AutopayPrice').popover();
		$('#RentedReferralsPrice' + (no - 1) + 'Infinite').popover().click(buttonInfiniteClick);
	});
	jQuery(function ($) {
		$('form').bind('submit', function () {
			$(this).find('input[id$=Min]').prop('disabled', false);
		});
	});
	$(this).find('button[id$=Infinite]').click(buttonInfiniteClick);
	$(this).find('input[id$=Max]').on('change input', maxChange);
");
?>



