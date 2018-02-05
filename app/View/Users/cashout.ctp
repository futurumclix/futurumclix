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
							<h5><?=__('Withdraw Funds')?></h5>
						</div>
						<?php if(isset($gData)): ?>
						<div id="gatewaySelector">
							<h6 class="text-xs-center" >
								<?=__('Please choose to which account you want to transfer money:')?>
							</h6>
							<div class="text-xs-center gatewaybuttons">
								<?php foreach($gData as $k => $v): ?>
								<?php if($v['state'] == 'disabled'): ?>
								<div class="tooltip-wrapper" data-title="<?=__('You can not pay on %s as an existing payment and the total sum of your existing %s payments exceeded the amount of purchases made with %s.', $v['humanizedName'], $v['humanizedName'], $v['humanizedName'])?>" data-toggle="tooltip" data-placement="top">
									<button class="btn btn-primary disabled" disabled="disabled"><?=$v['humanizedName']?></button>
								</div>
								<?php elseif($v['state'] == 'noAccount'): ?>
								<div class="tooltip-wrapper" data-title="<?=__('You can not pay on %s unless you fill an payment account address in User Profile.', $v['humanizedName'])?>" data-toggle="tooltip" data-placement="top">
									<button class="btn btn-primary disabled" disabled="disabled"><?=$v['humanizedName']?></button>
								</div>
								<?php elseif($v['state'] == 'blocked'): ?>
								<div class="tooltip-wrapper" data-title="<?=__('You can not use %s due to recent changes in your account. Please wait until %s', $v['humanizedName'], $this->Time->niceShort($v['available']))?>" data-toggle="tooltip" data-placement="top">
									<button class="btn btn-primary disabled" disabled="disabled"><?=$v['humanizedName']?></button>
								</div>
								<?php else: ?>
								<div class="tooltip-wrapper">
									<button id="gatewayButton<?=$k?>" value="<?=$k?>" class="btn btn-primary" title="<?=$v['humanizedName']?>" data-toggle="tooltip" data-placement="left"><?=$v['humanizedName']?></button>
								</div>
								<?php endif; ?>
								<?php endforeach; ?>
							</div>
						</div>
						<div class="col-sm-12" id="addForm" style="display:none">
							<fieldset class="form-group col-sm-4">
								<label><?=__('Amount')?></label>
								<input type="text" class="form-control" id="amount" readonly>
							</fieldset>
							<fieldset class="form-group col-sm-4">
								<label><?=__('Maximum Withdraw Amount')?></label>
								<input type="text" class="form-control" id="maxAmount" readonly>
							</fieldset>
							<fieldset class="form-group col-sm-4">
								<label><?=__('Our Fees')?></label>
								<input type="text" class="form-control" id="fee" readonly>
							</fieldset>
							<fieldset class="form-group col-sm-4">
								<label><?=__('You Will Receive')?></label>
								<input type="text" class="form-control" id="receive" readonly>
							</fieldset>
							<fieldset class="form-group col-sm-4">
								<label><?=__('Payment Account')?></label>
								<input type="text" class="form-control" id="account" readonly>
							</fieldset>
							<?=$this->UserForm->create(false)?>
							<?php if($googleAuthenticator):?>
							<fieldset class="form-group col-sm-4">
								<label><?=__('Google Authenticator Code')?></label>
								<?=$this->UserForm->input('ga_code', array('class' => 'form-control'))?>
							</fieldset>
							<?php endif; ?>
							<div class="clearfix"></div>
							<div class="text-xs-center">
								<?=$this->UserForm->input('gateway', array('type' => 'text', 'style' => 'display:none', 'read-only' => true, 'id' => 'selectedGateway'))?>
								<button type="submit" class="btn btn-primary"><?=__('Withdraw Money')?></button>
							</div>
							<?=$this->UserForm->end()?>
						</div>
						<?php endif; ?>
						<?php 
							$this->UserForm->create(false); // NOTE: no echo, so do not print <form>, just init helper
							$deposits = $this->UserForm->input('total_external_deposits', array(
								'type' => 'money',
								'default' => $totals['UserStatistic']['total_external_deposits'],
							));
							$this->UserForm->end(); // NOTE: no echo, so do not print </form>, just deinit helper
							if(!isset($limit)) $limit = '0';
							$result = '<span id="result">'.$this->Currency->format($limit).'</span>';
							$uroi = '<span id="userROI">'.$userROI.'</span>';
							$percent = bcmul($totals['UserStatistic']['total_cashouts'], 100);
							
							if(Membership::TOTAL_CASHOUTS_LIMIT_VALUE == $mode):
								echo __("You've been paid %s, with your current limit of %s you can still withdraw %s", $this->Currency->format($userROI), $this->Currency->format($maxROI), $this->Currency->format(bcsub($maxROI, $userROI)));
							elseif(Membership::TOTAL_CASHOUTS_LIMIT_PERCENTAGE_DEPOSITS == $mode):
								if(bccomp($totals['UserStatistic']['total_external_deposits'], '0') == 0) {
									$result = '<span id="result">'.__('unlimited').'</span>';
								} 
								echo __("You've deposited %s and cashed out %s so your ROI is: %s. With your current limit of %s you can still withdraw %s.", $deposits, $this->Currency->format($totals['UserStatistic']['total_cashouts']), $uroi, $maxROI, $result);
								
								$l = bcdiv($user['ActiveMembership']['Membership']['total_cashouts_limit_percentage'], 100);
								$this->Js->buffer("
									$('#total_external_deposits').on('change keyup paste', function() {
										if(Big($('#total_external_deposits').val()).eq('0')) {
											$('#result').html('unlimited');
										}
										userROI = Big('$percent').div(Big($('#total_external_deposits').val()));
										$('#userROI').html(''+userROI);
										result = Big('{$l}').mul(Big($('#total_external_deposits').val())).sub(Big('{$totals['UserStatistic']['total_cashouts']}'));
										$('#result').html(formatCurrency(result));
									});
								");
							elseif(Membership::TOTAL_CASHOUTS_LIMIT_PERCENTAGE_RR_INCOME == $mode):
								if((bccomp($totals['UserStatistic']['total_rrefs_clicks_earned'], '0') <= 0 && bccomp($totals['UserStatistic']['total_external_deposits'], '0') <= 0)):
									echo __("You've deposited %s and have %s income from RR so your ROI is 0% and you can withdraw with no limit.", $this->Currency->format('0'), $this->Currency->format('0'));
								elseif(bccomp($totals['UserStatistic']['total_external_deposits'], '0') <= 0):
									echo __("You have %s income from RR and paid out %s so your ROI is %s. With your current ROI limit of %s you can still withdraw %s", $totals['UserStatistic']['total_rrefs_clicks_earned'], $this->Currency->format($totals['UserStatistic']['total_cashouts']), $userROI, $maxROI, $this->Currency->format($limit));
								elseif(bccomp($totals['UserStatistic']['total_rrefs_clicks_earned'], '0') <= 0):
									echo __("You've deposited %s and cashed out %s so your ROI is %s. With your current ROI limit of %s you can still withdraw %s.", $deposits, $this->Currency->format($totals['UserStatistic']['total_cashouts']), $uroi, $maxROI, $result);
									$this->Js->buffer("
										$('#total_external_deposits').on('change keyup paste', function() {
											userROI = Big('$percent').div(Big($('#total_external_deposits').val()));
											$('#userROI').html(''+userROI);
											leftROI = Big('$maxROI').sub(userROI);
											result = leftROI.div(100).mul(Big($('#total_external_deposits').val()));
											$('#result').html(formatCurrency(result));
										});
									");
								else:
									echo __("You've deposited %s and have %s income from RR so your ROI is %s. With your current ROI limit of %s you can still withdraw %s.", $deposits, $this->Currency->format($totals['UserStatistic']['total_rrefs_clicks_earned']), $uroi, $maxROI, $result);
									$this->Js->buffer("
										$('#total_external_deposits').on('change keyup paste', function() {
											percent = Big('{$totals['UserStatistic']['total_rrefs_clicks_earned']}').mul(100);
											userROI = percent.div(Big($('#total_external_deposits').val()));
											console.log(userROI);
											$('#userROI').html(''+userROI);
											leftROI = Big('$maxROI').sub(userROI);
											result = leftROI.div(100).mul(Big($('#total_external_deposits').val()));
											$('#result').html(formatCurrency(result));
										});
									");
								endif;
							endif;
							?>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	if(isset($gData)) {
		$data = json_encode($gData);
		$this->Js->buffer("
			var gData = $data;
			$('button[id^=gatewayButton]').click(function(event) {
				var gateway = $(this).val();
				$('#gatewaySelector').hide();
				$('#selectedGateway').val(gateway);
				$('#amount').attr('placeholder', gData[gateway]['amount']);
				$('#fee').attr('placeholder', gData[gateway]['fee']);
				$('#receive').attr('placeholder', gData[gateway]['receive']);
				$('#account').attr('placeholder', gData[gateway]['account']);
				$('#maxAmount').attr('placeholder', gData[gateway]['maxAmount']);
				$('#addForm').show();
			});
		");
	}
	?>
