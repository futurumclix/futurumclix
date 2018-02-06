

<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Withdraw Funds')?></h2>
			<?php if(isset($gData)): ?>
			<div class="uk-text-center" id="gatewaySelector">
				<h6>
					<?=__('Please choose to which account you want to transfer money:')?>
				</h6>
				<div class="uk-inline">
					<?php foreach($gData as $k => $v): ?>
					<?php if($v['state'] == 'disabled'): ?>
					<button class="uk-button uk-button-primary uk-button-primary-disabled" title="<?=__('You can not pay on %s as an existing payment and the total sum of your existing %s payments exceeded the amount of purchases made with %s.', $v['humanizedName'], $v['humanizedName'], $v['humanizedName'])?>" data-uk-tooltip><?=$v['humanizedName']?></button>
					<?php elseif($v['state'] == 'noAccount'): ?>
					<button class="uk-button uk-button-primary uk-button-primary-disabled" title="<?=__('You can not pay on %s unless you fill an payment account address in User Profile.', $v['humanizedName'])?>" data-uk-tooltip><?=$v['humanizedName']?></button>
					<?php elseif($v['state'] == 'blocked'): ?>
					<button class="uk-button uk-button-primary uk-button-primary-disabled" title="<?=__('You can not use %s due to recent changes in your account. Please wait until %s', $v['humanizedName'], $this->Time->niceShort($v['available']))?>" data-uk-tooltip><?=$v['humanizedName']?></button>
					<?php else: ?>
					<button id="gatewayButton<?=$k?>" value="<?=$k?>" class="uk-button uk-button-primary" title="<?=$v['humanizedName']?>" data-uk-tooltip><?=$v['humanizedName']?></button>
					<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="uk-form-stacked" id="addForm" style="display:none">
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Amount')?></label>
					<input type="text" class="uk-input" id="amount" readonly>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Maximum Withdraw Amount')?></label>
					<input type="text" class="uk-input" id="maxAmount" readonly>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Our Fees')?></label>
					<input type="text" class="uk-input" id="fee" readonly>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('You Will Receive')?></label>
					<input type="text" class="uk-input" id="receive" readonly>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Payment Account')?></label>
					<input type="text" class="uk-input" id="account" readonly>
				</div>
				<?=$this->UserForm->create(false)?>
				<?php if($googleAuthenticator):?>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Google Authenticator Code')?></label>
					<?=$this->UserForm->input('ga_code', array('class' => 'uk-input'))?>
				</div>
				<?php endif; ?>
				<div class="uk-text-right">
					<?=$this->UserForm->input('gateway', array('type' => 'text', 'style' => 'display:none', 'read-only' => true, 'id' => 'selectedGateway'))?>
					<button type="submit" class="uk-button uk-button-primary"><?=__('Withdraw Money')?></button>
				</div>
				<?=$this->UserForm->end()?>
			</div>
			<?php endif; ?>
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

