<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Add Funds')?></h2>
			<div class="uk-text-center" id="addForm">
				<?=$this->UserForm->create(false, array('class' => 'uk-form-stacked'))?>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Current Purchase Balance')?></label>
					<input type="text" class="uk-input" placeholder="<?=h($this->Currency->format($user['User']['purchase_balance']))?>" readonly>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label" id="minimumAmountLabel"><?=__('Minimum Transfer Amount Is')?></label>
					<input type="text" id="minimumAmount" class="uk-input" placeholder="<?=h($this->Currency->format($gData['AccountBalance']['minimum_deposit_amount']))?>" readonly>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label" id="maximumAmountLabel"><?=__('Maximum Transfer Amount Is')?></label>
					<input type="text" id="maximumAmount" class="uk-input" placeholder="<?=h(bccomp('0', $gData['AccountBalance']['maximum_deposit_amount']) == 0 ? __('Unlimited') : $this->Currency->format($gData['AccountBalance']['maximum_deposit_amount']))?>" readonly>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label" id="totalAmountLabel"><?=__('Total Payment')?></label>
					<input type="text" id="totalAmount" class="uk-input" readonly>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Fee')?></label>
					<input type="text" id="feeAmount" class="uk-input" placeholder="<?=h($this->Currency->format(0))?>" readonly>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Amount')?></label>
					<div class="input-group">
						<?=$this->UserForm->input('amount', array(
							'class' => 'uk-input',
							'placeholder' => __('Amount'),
							'step' => 'real',
							));?>
					</div>
				</div>
				<?=$this->UserForm->input('gateway', array(
					'type' => 'hidden',
					'id' => 'selectedGateway',
					))?>
				<div class="uk-text-center">
					<button type="submit" class="uk-button uk-button-primary" disabled="disabled"><?=__('Transfer Money')?></button>
				</div>
			</div>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
<?php
	$accountBalanceURL = Router::url(array('action' => 'transfer'));
	$gData = json_encode($gData);
	$this->Js->buffer("
		var gData = $gData;
		$('#amount').on('change input', function(event) {
			var gateway = 'AccountBalance'
			var v = $(this).val();
	
			if(v == '') {
				$('#totalAmount').attr('placeholder', '');
				$('#feeAmount').attr('placeholder', gData[gateway]['deposit_fee_percent'] + '% + ' + formatCurrency(gData[gateway]['deposit_fee_amount'], true));
				$('#addForm').find(':submit').attr('disabled', true);
				return;
			}
	
			try {
				v = Big(v);
			} catch(t) {
				$('#totalAmount').attr('placeholder', '');
				$('#feeAmount').attr('placeholder', gData[gateway]['deposit_fee_percent'] + '% + ' + formatCurrency(gData[gateway]['deposit_fee_amount'], true));
				$('#addForm').find(':submit').attr('disabled', true);
				return;
			}
	
			if(v.cmp(Big(gData[gateway]['minimum_deposit_amount'])) == -1) {
				$('#totalAmount').attr('placeholder', '');
				$('#feeAmount').attr('placeholder', gData[gateway]['deposit_fee_percent'] + '% + ' + formatCurrency(gData[gateway]['deposit_fee_amount'], true));
				$('#addForm').find(':submit').attr('disabled', true);
				return;
			}
	
			try {
				fee = Big(calculateFee(v, gData[gateway]['deposit_fee_percent'], gData[gateway]['deposit_fee_amount']));
				total = v.add(fee).toFixed(8);
				fee = formatCurrency(fee, true);
				total = formatCurrency(total, true);
				$('#totalAmount').attr('placeholder', total);
				$('#feeAmount').attr('placeholder', fee);
				$('#addForm').find(':submit').attr('disabled', false);
			} catch(t) {
				$('#totalAmount').text('');
			}
		});
	");
	?>
