<?php $data = &$this->request->data ?>
<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Details Of User:')?> <?=h($data['User']['username'])?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#personal"><?=__d('admin', 'Personal Data')?></a></li>
		<li><a data-toggle="tab" href="#earnings"><?=__d('admin', 'Earnings And Stats')?></a></li>
		<li><a data-toggle="tab" href="#groups"><?=__d('admin', 'Groups')?></a></li>
		<li><a data-toggle="tab" href="#actions"><?=__d('admin', 'Actions')?></a></li>
	</ul>
	<div class="tab-content">
		<div id="personal" class="tab-pane fade active in">
			<div class="title2">
				<h2><?=__d('admin', 'Personal')?></h2>
			</div>
			<?=
				$this->AdminForm->create('User', array(
					'class' => 'form-horizontal',
				))
			?>
			<?=$this->AdminForm->input('id')?>
			<?=$this->AdminForm->input('UserProfile.user_id')?>
			<div class="form-group">
				<div class="col-sm-3">
					<label><?=__d('admin', 'First Name')?></label>
					<?=$this->AdminForm->input('first_name')?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Last Name')?></label>
					<?=$this->AdminForm->input('last_name')?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'E-mail')?></label>
					<?=$this->AdminForm->input('email')?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Location')?></label>
					<?=$this->AdminForm->input('location')?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-3">
					<label><?=__d('admin', 'Address')?></label>
					<?=$this->AdminForm->input('UserProfile.address')?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Password')?></label>
					<?=
						$this->AdminForm->input('password', array(
							'data-toggle' => 'tooltip',
							'data-placement' => 'top',
							'title' => __d('admin', 'Please left empty if you do not want to change')
						))
					?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Sex')?></label>
					<div class="input-group gender">
						<?=
							$this->AdminForm->radio('UserProfile.gender', $genders, array(
								'required' => false,
								'legend' => false,
								'value' => $data['UserProfile']['gender']
							))
						?>
					</div>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'First Click')?></label>
					<div class="input-group gender">
						<?=$this->AdminForm->input('first_click')?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<?php foreach($gateways as $gateway => $name): ?>
					<div class="col-sm-3">
						<label><?=__d('admin', '%s e-mail', h($name))?></label>
						<?=$this->AdminForm->input('UserProfile.'.$gateway)?>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="form-group">
				<div class="col-sm-3">
					<label><?=__d('admin', 'Signup IP')?></label>
					<?=$this->AdminForm->input('signup_ip')?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Last Login IP')?></label>
					<?=$this->AdminForm->input('last_ip')?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Status')?></label>
					<?=
						$this->AdminForm->input('role', array(
							'type' => 'select',
							'options' => $userRoles,
						))
					?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Verification Date')?></label>
					<?=
						$this->AdminForm->input('UserMetadata.verify_date', array(
							'value' => empty($data['UserMetadata']['verify_date']) ? __d('admin', 'Not verified') : $data['UserMetadata']['verify_date']
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-3">
					<label><?=__d('admin', 'Last Login')?></label>
					<?=$this->AdminForm->input('last_log_in')?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Last Click')?></label>
					<?=$this->AdminForm->input('UserStatistic.last_click_date')?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Account Security')?></label>
					<div class="input-group">
						<?=$this->AdminForm->input('UserSecret.id')?>
						<?=
							$this->AdminForm->input('UserSecret.mode', array(
								'options' => $this->Utility->enum('UserSecret', 'mode'),
							))
						?>
					</div>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Comes From')?></label>
					<div class="input-group">
						<?=h($data['User']['comes_from'])?>
					</div>
				</div>
			</div>
			<div class="title2">
				<h2><?=__d('admin', 'Referrals And Membership')?></h2>
			</div>
			<div class="form-group">
				<div class="col-sm-2">
					<label><?=__d('admin', 'Direct Referrals')?></label>
					<?php if($data['User']['refs_count'] > 0): ?>
						<div class="input-group">
							<?=$this->Html->link(h($data['User']['refs_count']), array('action' => 'index', 'Upline.username' => $data['User']['username']))?>
						</div>
					<?php else: ?>
						<div class="input-group">
							<?=h($data['User']['refs_count'])?>
						</div>
					<?php endif; ?>
				</div>
				<div class="col-sm-2">
					<label><?=__d('admin', 'Rented Referrals')?></label>
					<?php if($data['User']['rented_refs_count'] > 0): ?>
						<div class="input-group">
						<?=$this->Html->link(h($data['User']['rented_refs_count']), array('action' => 'index', 'RentedUpline.username' => $data['User']['username']))?>
					</div>
					<?php else: ?>
						<div class="input-group">
							<?=h($data['User']['rented_refs_count'])?>
						</div>
					<?php endif; ?>
				</div>
				<div class="col-sm-2">
					<label><?=__d('admin', 'Rented')?></label>
					<?php if($data['User']['rented_upline_id'] !== null): ?>
						<div class="input-group">
							<?=__d('admin', 'Yes')?>
						</div>
					<?php else: ?>
						<div class="input-group">
							<?=__d('admin', 'No')?>
						</div>
					<?php endif; ?>
				</div>
				<div class="col-sm-2">
					<label><?=__d('admin', 'Rented Expiry Date')?></label>
					<?=$this->AdminForm->input('User.rent_ends')?>
				</div>
				<div class="col-sm-2">
					<label><?=__d('admin', 'Direct Upline')?></label>
					<?=
						$this->AdminForm->input('upline', array(
							'value' => isset($data['Upline']['username']) ? $data['Upline']['username'] : '',
						))
					?>
				</div>
				<div class="col-sm-2">
					<label><?=__d('admin', 'Rented Upline')?></label>
					<?=
						$this->AdminForm->input('rentedUpline', array(
							'value' => isset($data['RentedUpline']['username']) ? $data['RentedUpline']['username'] : '',
						))
					?>
				</div>
			</div>
			<?php if($data['ActiveMembership']['period'] != 'Default'): ?>
				<div class="title3">
					<h2><?=__d('admin', 'Edit Active Membership')?></h2>
				</div>
				<div class="form-group">
					<div class="col-sm-4">
						<label><?=__d('admin', 'Membership')?></label>
						<?=$this->AdminForm->input('ActiveMembership.id')?>
						<?=
							$this->AdminForm->input('ActiveMembership.membership_id', array(
								'type' => 'select',
								'options' => $memberships,
							))
						?>
					</div>
					<div class="col-sm-4">
						<label><?=__d('admin', 'From: ')?></label>
						<?=$this->AdminForm->input('ActiveMembership.begins')?>
					</div>
					<div class="col-sm-4">
						<label><?=__d('admin', 'To')?></label>
						<?=$this->AdminForm->input('ActiveMembership.ends')?>
					</div>
				</div>
				<div class="col-sm-12 text-center padding10">
					<?=__d('admin', 'Add New Membership: ')?><a data-toggle="collapse" href="#collapse2" ><i id="collapse2Button" title="<?=__d('admin', 'Click to add another membership to the member')?>" data-toggle="tooltip" data-placement="right" class="fa fa-plus-circle fa-lg"></i></a>
					<?=__d('admin', 'Show All Memberships: ')?><a data-toggle="collapse" href="#collapse3" ><i id="collapse3Button" title="<?=__d('admin', 'Click to show all memberships')?>" data-toggle="tooltip" data-placement="right" class="fa fa-plus-circle fa-lg"></i></a>
				</div>
				<div class="clearfix"></div>
				<?php else: ?>
				<div class="col-sm-12 text-center">
					<label class="label label-info">
						<?=__d('admin', 'This User Has Standard Membership.')?>
					</label>
				</div>
				<div class="clearfix"></div>
			<?php endif; ?>
			<div id="collapse2" <?php if($data['ActiveMembership']['period'] != 'Default'): ?>class="panel-collapse collapse"<?php endif; ?>>
				<div class="title3">
					<h2><?=__d('admin', 'Add Membership')?></h2>
				</div>
				<div class="form-group">
					<div class="col-sm-4">
						<label><?=__d('admin', 'Membership')?></label>
						<?=
							$this->AdminForm->input('NewMembership.membership_id', array(
								'type' => 'select',
								'empty' => '----',
								'options' => $memberships,
							))
						?>
					</div>
					<div class="col-sm-4">
						<label><?=__d('admin', 'From: ')?></label>
						<?=$this->AdminForm->input('NewMembership.begins', array('type' => 'datetime'))?>
					</div>
					<div class="col-sm-4">
						<label><?=__d('admin', 'To')?></label>
						<?=$this->AdminForm->input('NewMembership.ends', array('type' => 'datetime'))?>
					</div>
				</div>
			</div>
			<?php if(count($data['MembershipsUser']) > 0): ?>
				<div id="collapse3" class="panel-collapse collapse">
					<div class="title3">
						<h2><?=__d('admin', 'Edit Memberships')?></h2>
					</div>
					<?php for($i = 0, $max = count($data['MembershipsUser']); $i < $max; $i++): ?>
						<?php if($data['MembershipsUser'][$i]['period'] != 'Default'): ?>
						<div class="form-group">
							<div class="col-sm-4">
								<label><?=__d('admin', 'Membership')?></label>
								<?=$this->AdminForm->input('MembershipsUser.'.$i.'.id')?>
									<?=
										$this->AdminForm->input('MembershipsUser.'.$i.'.membership_id', array(
											'type' => 'select',
											'options' => $memberships,
										))
									?>
							</div>
							<div class="col-sm-4">
								<label><?=__d('admin', 'From: ')?></label>
								<?=$this->AdminForm->input('MembershipsUser.'.$i.'.begins', array('type' => 'datetime'))?>
							</div>
							<div class="col-sm-4">
								<label><?=__d('admin', 'To')?></label>
								<?=$this->AdminForm->input('MembershipsUser.'.$i.'.ends')?>
							</div>
						</div>
						<?php endif; ?>
					<?php endfor; ?>
				</div>
			<?php endif; ?>
			<?php if($data['ActiveMembership']['period'] != 'Default'): ?>
				<div class="title3 text-center">
					<h2>
						<?=__d('admin', 'Click to remove any membership from this member and switch him to Standard Membership.')?>
						<br />
						<?=
							$this->AdminForm->postLink('Degrade', array(
								'action' => 'admin_backToDefaultMembership', 
								$data['User']['id']
							), array(
								'class' => 'btn btn-danger',
								'style' => 'margin-top: 5px;'
							))
						?>
					</h2>
				</div>
			<?php endif; ?>
			<div class="form-group">
				<div class="col-sm-12">
					<label><?=__d('admin', 'Admin Notes')?></label>
				</div>
				<div class="col-sm-12">
				<?=
					$this->AdminForm->input('UserMetadata.admin_note', array(
						'placeholder' => __d('admin', 'Enter admin notes for this user'),
					))
				?>
				</div>
			</div>
			<div class="text-center col-md-12 paddingten">
				<button class="btn btn-primary btn-sm"><?=_('Save Info')?></button>
			</div>
			<?=$this->AdminForm->end()?>
		</div>
		<div id="earnings" class="tab-pane fade">
			<div class="title2">
				<h2><?=__d('admin', 'Earnings and purchases')?></h2>
			</div>
			<?=
				$this->AdminForm->create('User', array(
					'inputDefaults' => array('required' => false),
					'class' => 'form-horizontal',
				))
			?>
			<?=$this->AdminForm->input('id')?>
			<div class="form-group">
				<div class="col-sm-3">
					<label><?=__d('admin', 'Current Balance')?></label>
					<div class="input-group">
						<?=$this->AdminForm->input('account_balance')?>
					</div>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Purchase Balance')?></label>
					<div class="input-group">
						<?=$this->AdminForm->input('purchase_balance')?>
					</div>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Points Balance')?></label>
					<div class="input-group">
						<?=$this->AdminForm->input('points')?>
					</div>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Total Clicks')?></label>
					<?=$this->AdminForm->input('UserStatistic.total_clicks')?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Personal Earnings')?></label>
					<div class="input-group">
						<?=$this->AdminForm->input('UserStatistic.total_clicks_earned')?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-3">
					<label><?=__d('admin', 'Direct Referrals Clicks')?></label>
					<?=$this->AdminForm->input('UserStatistic.total_drefs_clicks')?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Direct Referrals Clicks Credited')?></label>
					<?=$this->AdminForm->input('UserStatistic.total_drefs_credited_clicks')?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Rented Referrals Clicks')?></label>
					<?=$this->AdminForm->input('UserStatistic.total_rrefs_clicks')?>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Rented Referrals Clicks Credited')?></label>
					<?=$this->AdminForm->input('UserStatistic.total_rrefs_credited_clicks')?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-3">
					<label><?=__d('admin', 'Direct Referrals Earnings')?></label>
					<div class="input-group">
						<?=$this->AdminForm->input('UserStatistic.total_drefs_clicks_earned')?>
					</div>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Rented Referrals Earnings')?></label>
					<div class="input-group">
						<?=$this->AdminForm->input('UserStatistic.total_rrefs_clicks_earned')?>
					</div>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Total Paid')?></label>
					<div class="input-group">
						<?=$this->AdminForm->input('UserStatistic.total_cashouts')?>
					</div>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Cashout Times')?></label>
						<?=$this->AdminForm->input('cashouts')?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-3">
					<label><?=__d('admin', 'Total Deposits')?></label>
					<div class="input-group">
						<input class="form-control" value="<?=$this->Currency->format($totalDeposits)?>" readonly>
					</div>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Total Purchases')?></label>
					<div class="input-group">
						<input class="form-control" value="<?=$this->Currency->format($totalPurchases)?>" readonly>
					</div>
				</div>
				<div class="col-sm-3">
					<label><?=__d('admin', 'Pending Payouts')?></label>
					<div class="input-group">
						<input class="form-control" value="<?=$this->Currency->format($waitingCashouts)?>" readonly>
					</div>
				</div>
			</div>
			<div class="text-center col-md-12 paddingten">
				<button class="btn btn-primary btn-sm"><?=_('Save Info')?></button>
			</div>
			<div class="clearfix"></div>
			<?=$this->AdminForm->end()?>
			<div class="title2">
				<h2><?=__d('admin', 'Click statistics')?></h2>
			</div>
			<div class="title3">
				<h2><?=__d('admin', 'Clicks This Week')?></h2>
			</div>
			<div class="col-md-12">
				<div class="chartpanel">
					<?=
						$this->Chart->show(array(
								__d('admin', 'Clicks') => $usrclicks,
							), array(
								'mode' => 'week',
								'label' => array(
									'content' => false,
									'class' => 'usrclicks',
								),
								'width' => '100%',
								'height' => '150px'
							), array(
								'colors' => array('#2ecc71'), 
								'xAxis' => array('visible' => true, 'lineColor' => '#ffffff', 'tickWidth' => '0' , 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '12px', 'font-weight' => '700', 'font-family' => 'Lato'))),
								'yAxis' => array('gridLineColor' => '#ffffff', 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '14px', 'font-weight' => '700', 'font-family' => 'Lato'))),
							)
						)
						?>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="title3">
				<h2><?=__d('admin', 'Direct Referrals Clicks')?></h2>
			</div>
			<div class="col-sm-12">
				<?=
					$this->Chart->show(array(
							__d('admin', 'Clicks') => $drclicks,
							__d('admin', 'Clicks credited') => $drclicksCredited,
						), array(
							'mode' => 'week',
							'label' => array(
								'content' => false,
								'class' => 'directrefs',
							),
							'width' => '100%',
							'height' => '150px'
						), array(
							'colors' => array('#e74c3c'), 
							'xAxis' => array('visible' => true, 'lineColor' => '#ffffff', 'tickWidth' => '0' , 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '8px', 'font-weight' => '700', 'font-family' => 'Lato'))),
							'yAxis' => array('gridLineColor' => '#ffffff', 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '14px', 'font-weight' => '700', 'font-family' => 'Lato'))),
						)
					)
				?>
			</div>
			<div class="clearfix"></div>
			<div class="title3">
				<h2><?=__d('admin', 'Rented Referrals Clicks')?></h2>
			</div>
			<div class="col-sm-12">
				<?=
					$this->Chart->show(array(
							__d('admin', 'Clicks') => $rrclicks,
							__d('admin', 'Clicks credited') => $rrclicksCredited,
						), array(
							'mode' => 'week',
							'label' => array(
								'content' => false,
								'class' => 'directrefs',
							),
							'width' => '100%',
							'height' => '150px'
						), array(
							'colors' => array('#9b59b6'), 
							'xAxis' => array('visible' => true, 'lineColor' => '#ffffff', 'tickWidth' => '0' , 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '8px', 'font-weight' => '700', 'font-family' => 'Lato'))),
							'yAxis' => array('gridLineColor' => '#ffffff', 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '14px', 'font-weight' => '700', 'font-family' => 'Lato'))),
						)
					)
				?>
			</div>
			<div class="clearfix"></div>
			<div class="title3">
				<h2><?=__d('admin', 'Autorenew')?></h2>
			</div>
			<div class="col-sm-12" id="autorenewStats">
			</div>
			<div class="clearfix"></div>
			<div class="title3">
				<h2><?=__d('admin', 'Autopay')?></h2>
			</div>
			<div class="col-sm-12" id="autopayStats">
			</div>
			<div class="clearfix"></div>
		</div>
		<div id="groups" class="tab-pane fade">
			<div class="title2">
				<h2><?=__d('admin', 'Choose to which groups this user belongs')?></h2>
			</div>
			<?=$this->AdminForm->create(false)?>
			<?=$this->AdminForm->input('groups', array('multiple' => true, 'selected' => $data['RequestObject']))?>
			<div class="text-center col-md-12 paddingten">
				<button class="btn btn-primary btn-sm"><?=_('Save Info')?></button>
			</div>
			<?=$this->AdminForm->end();?>
		</div>
		<div id="actions" class="tab-pane fade">
			<div class="text-center">
				<div class="btn-group">
					<?=
						$this->AdminForm->postLink(__d('admin', 'Suspend User'),
							array('action' => 'suspend', $data['User']['id']),
							array('class' => 'btn btn-primary nav-link'),
							__d('admin', 'Are you sure you want to suspend "%s"?',  $data['User']['username'])
						)
					?>
					<?=
						$this->AdminForm->postLink(__d('admin', 'Delete User'),
							array('action' => 'delete', $data['User']['id']),
							array('class' => 'btn btn-primary'),
							__d('admin', 'Are you sure you want to delete "%s"?',  $data['User']['username'])
						)
					?>
					<?=
						$this->AdminForm->postLink(__d('admin', 'Unhook Direct Referrals'),
							array('action' => 'unhookDirectReferrals', $data['User']['id']),
							array('class' => 'btn btn-primary'),
							__d('admin', 'Are you sure you want to unhook direct referrals of "%s"?',  $data['User']['username'])
						)
					?>
					<?=
						$this->AdminForm->postLink(__d('admin', 'Unhook Rented Referrals'),
							array('action' => 'unhookRentedReferrals', $data['User']['id']),
							array('class' => 'btn btn-primary'),
							__d('admin', 'Are you sure you want to unhook rented referrals of "%s"?',  $data['User']['username'])
						)
					?>
				</div>
			</div>
			<br />
			<div class="text-center paddingten">
				<div class="btn-group">
					<?=
						$this->AdminForm->postLink(__d('admin', 'Reset Login Data'),
							array('action' => 'resetLogin', $data['User']['id']),
							array('class' => 'btn btn-primary'),
							__d('admin', 'Are you sure you want to reset login data of "%s"?', $data['User']['username'])
						)
					?>
					<?=
						$this->AdminForm->postLink(__d('admin', 'Reset Advertisements'),
							array('action' => 'resetAdvertisements', $data['User']['id']),
							array('class' => 'btn btn-primary'),
							__d('admin', 'Are you sure you want to reset advertisements clicks of "%s"?',  $data['User']['username'])
						)
					?>
					<?php if($data['User']['forum_status'] != 2): ?>
						<?=
							$this->AdminForm->postLink(__d('admin', 'Ban From Forum'),
								array('plugin' => 'forum', 'controller' => 'users', 'action' => 'ban', $data['User']['id']),
								array('class' => 'btn btn-primary'),
								__d('admin', 'Are you sure you want to ban "%s"?',  $data['User']['username'])
							)
						?>
					<?php else: ?>
						<?=
							$this->AdminForm->postLink(__d('admin', 'Unban From Forum'),
								array('plugin' => 'forum', 'controller' => 'users', 'action' => 'unban', $data['User']['id']),
								array('class' => 'btn btn-primary'),
								__d('admin', 'Are you sure you want to unban "%s"?',  $data['User']['username'])
							)
						?>
					<?php endif; ?>
					<?=
						$this->AdminForm->postLink(__d('admin', 'Login as "%s"', $data['User']['username']),
							array('action' => 'login', $data['User']['id']),
							array('class' => 'btn btn-primary'),
							__d('admin', 'Are you sure you want to login as "%s"?',  $data['User']['username'])
						)
					?>
					<?=
						$this->Html->link(__d('admin', 'Send E-mail'), array('action' => 'sendMessage', 'user' => $data['User']['username']), array('class' => 'btn btn-primary'))
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	$autorenewStatsURL = $this->Html->url(array('action' => 'autorenewStats', $data['User']['id']));
	$autopayStatsURL = $this->Html->url(array('action' => 'autopayStats', $data['User']['id']));
	$this->Js->buffer("
		setNavToggles('collapse2Button', 'collapse2');
		setNavToggles('collapse3Button', 'collapse3');
		$('#autorenewStats').load('$autorenewStatsURL');
		$('#autopayStats').load('$autopayStatsURL');
	");
?>
