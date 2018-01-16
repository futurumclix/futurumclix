 <div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Payment Processors Settings')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#settings"><?=__d('admin', 'General Settings')?></a></li>
		<?php if(!empty($gatewaysActive)): ?>
			<li><a data-toggle="tab" href="#fees"><?=__d('admin', 'Fees And Others')?></a></li>
		<?php endif;?>
		<?php foreach($gatewaysActiveWithSettings as $k => $gateway): ?>
			<li><a data-toggle="tab" href="#<?=$k?>"><?=h($gateway)?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="tab-content">
		<div id="settings" class="tab-pane fade in active">
			<?=$this->AdminForm->create('Settings', array('class' => 'form-horizontal'))?>
			<?php foreach($gatewaysSupported as $k => $gateway): ?>
				<?=$this->AdminForm->hidden("PaymentsGateway.$k.name", array('value' => $k))?>
			<?php endforeach; ?>
			<div class="title2">
				<h2><?=__d('admin', 'Choose Payment Processors For incoming payments (deposits & purchasing items)')?></h2>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Please Choose Which Payment Processors Do You Want To Use For Incoming Payments')?></label>
				<div class="col-sm-4">
					<?php foreach($gatewaysSupportedDeposits as $k => $gateway): ?>
						<div class="checkbox">
							<?php if($k == 'PurchaseBalance'): ?>
							<label data-toggle="tooltip" data-placement="right" title="<?=__d('admin', 'Purchase Balance can be used only for purchasing items, it cannot be used for deposits.')?>">
							<?php else: ?>
							<label>
							<?php endif; ?>
								<?=$this->AdminForm->checkbox("PaymentsGateway.$k.deposits")?>
								<?=h($gateway)?>
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="title2">
				<h2><?=__d('admin', 'Choose Payment Processors For Cashouts')?></h2>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Please Choose Which Payment Processors Do You Want To Use For Cashouts')?></label>
				<div class="col-sm-4">
					<?php foreach($gatewaysSupportedCashouts as $k => $gateway): ?>
						<div class="checkbox">
							<label>
								<?=$this->AdminForm->checkbox("PaymentsGateway.$k.cashouts")?>
								<?=h($gateway)?>
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="title2">
				<h2><?=__d('admin', 'Payout Settings')?></h2>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Allow To Withdraw To All Processors, Ignore Deposits.')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('cashoutMode', array(
							'type' => 'checkbox',
							'data-toggle' => 'popover',
							'data-trigger' => 'focus',
							'data-content' => __d('admin', 'Allow withdrawing unlimited amounts to all processors. Do not take any deposits into consideration.'),
							'value' => 'all',
							'hiddenField' => false,
							'class' => 'radioCheckbox',
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Allow To Withdraw Only To One Processor From Which Most Of Deposits Are Made')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('cashoutMode', array(
							'type' => 'checkbox',
							'data-toggle' => 'popover',
							'data-trigger' => 'focus',
							'data-content' => __d('admin', 'Allow withdrawing only to one processors from which most of deposits are made. Other processors will be disabled.'),
							'value' => 'most',
							'hiddenField' => false,
							'class' => 'radioCheckbox',
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Allow To Withdraw Unlimited Amount To Processor From Which Most Of Deposits Are Made')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('cashoutMode', array(
							'type' => 'checkbox',
							'data-toggle' => 'popover',
							'data-trigger' => 'focus',
							'data-content' => __d('admin', 'Allow withdrawing with no limit to one processors from which most of deposits are made. Other processors will be limited to the amount of deposits made with them. If deposits will be equal for more than one processor, they will have unlimited withdraw amounts.'),
							'value' => 'mostUnlimited',
							'hiddenField' => false,
							'class' => 'radioCheckbox',
						))
					?>
				</div>
			</div>
			<div class="title2">
				<h2><?=__d('admin', 'Other Settings')?></h2>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Check, To Enable Transferring Money From Account Balance To Purchase Balance')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('enableTransfers', array(
							'type' => 'checkbox',
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Which Balance Do You Want To Credit With Purchase Commission')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('commissionTo', array(
							'type' => 'select',
							'options' => array(
								'account_balance' => __d('admin', 'Account Balance'),
								'purchase_balance' => __d('admin', 'Purchase Balance'),
							)
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Check, To Allow Upgrading From Purchase Balance')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('allowUpgradeFromPBalance', array(
							'type' => 'checkbox',
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'From Which Balance Do You Want To Take Money For Direct Referral Delete Fee')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('deleteReferralsBalance', array(
							'options' => array(
								'purchase' => __d('admin', 'Purchase balance'),
								'account' => __d('admin', 'Account balance'),
								'both' => __d('admin', 'Purchase balance or Account balance'),
							)
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'If Autocashout Failed')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('autoCashoutFail', array(
							'type' => 'select',
							'options' => array(
								'failed' => 'Set "Failed" state',
								'new' => 'Set "New" State',
								'cancelled' => 'Refund to Account Balance',
							),
						));
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'From Which Balance Do You Want To Take Money For Rented Referrals Expiry')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('expiryReferralsBalance', array(
							'options' => array(
								'account' => __d('admin', 'Account balance'),
								'both' => __d('admin', 'Purchase balance or Account balance'),
							)
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Ignore Minimum Deposit Amounts for Purchases')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('ignoreMinDeposit', array(
							'type' => 'checkbox',
							'checked' => isset($this->request->data['Settings']['ignoreMinDeposit']) ? $this->request->data['Settings']['ignoreMinDeposit'] : false,
						));
					?>
				</div>
			</div>
			<div id="accountBalanceSettings" <?php if(!$this->request->data['Settings']['enableTransfers']):?>style="display: none"<?php endif;?>>
				<div class="title2">
					<h2><?=__d('admin', 'Account Balance')?></h2>
				</div>
				<div class="form-group">
					<label class="col-sm-8 control-label"><?=__d('admin', 'Minimum Transfer Amount')?></label>
					<div class="col-sm-4">
						<div class="input-group">
							<?=$this->AdminForm->input('minimumTransfer', array('type' => 'money'))?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-8 control-label"><?=__d('admin', 'Maximum Transfer Amount')?></label>
					<div class="col-sm-4">
						<div class="input-group">
							<?=$this->AdminForm->input('maximumTransfer', array(
								'type' => 'money',
								'data-toggle' => 'popover',
								'data-trigger' => 'focus',
								'data-content' => __d('admin', 'Put 0 to disable (no maximum transfer amount)'),
							))?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12 text-center paddingten">
				<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
			</div>
			<?=$this->AdminForm->end()?>
		</div>
		<?php if(!empty($gatewaysActive)): ?>
		<div id="fees" class="tab-pane fade in">
			<?=$this->AdminForm->create('Settings', array('class' => 'form-horizontal'))?>
			<?php foreach($gatewaysActive as $k => $gateway): ?>
			<?=$this->AdminForm->hidden("PaymentsGateway.$k.name", array('value' => $k))?>
			<div class="title2">
				<h2><?=h($gateway)?></h2>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Minimum Deposit Amount')?></label>
				<div class="col-sm-4">
					<div class="input-group">
						<?=$this->AdminForm->input("PaymentsGateway.$k.minimum_deposit_amount", array('type' => 'money'))?>
					</div>
				</div>
			</div>
			<?php if($this->request->data['PaymentsGateway'][$k]['deposits']): ?>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Deposit Fee')?></label>
				<div class="col-sm-2">
					<div class="input-group">
						<?=
							$this->AdminForm->input("PaymentsGateway.$k.deposit_fee_percent", array(
								'type' => 'number',
								'step' => '0.001',
								'min' => 0,
								'max' => 100,
							))
						?>
						<div class="input-group-addon">%</div>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="input-group">
						<div class="input-group-addon">+</div>
						<?=$this->AdminForm->input("PaymentsGateway.$k.deposit_fee_amount", array('type' => 'money'))?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Withdraw Fee')?></label>
				<div class="col-sm-2">
					<div class="input-group">
						<?=
							$this->AdminForm->input("PaymentsGateway.$k.cashout_fee_percent", array(
								'type' => 'number',
								'step' => '0.001',
								'min' => 0,
								'max' => 100
							))
						?>
						<div class="input-group-addon">%</div>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="input-group">
						<div class="input-group-addon">+</div>
						<?=$this->AdminForm->input("PaymentsGateway.$k.cashout_fee_amount", array('type' => 'money'))?>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<?php endforeach; ?>
			<div class="col-md-12 text-center paddingten">
				<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
			</div>
			<?=$this->AdminForm->end()?>
		</div>
		<?php endif; ?>
		<?php
			foreach($gatewaysActiveWithSettings as $k => $v) {
				include(APP.DS.'Lib'.DS.'Payments'.DS.$k.'Settings.ctp');
			}
		?>
	</div>
</div>
<?php
	$this->Js->buffer("
		var accSettings = $('#accountBalanceSettings');
		$('#SettingsEnableTransfers').change(function(){
			if($(this).is(':checked')) {
				accSettings.show();
			} else {
				accSettings.hide();
			}
		});
		checkboxesAsRadio(0);
	");
?>
