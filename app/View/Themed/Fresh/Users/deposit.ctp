<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Add Funds')?></h2>
			<div class="uk-text-center" id="gatewaySelector">
				<p class="uk-margin"><?=__('Please Choose From Where You Want To Transfer Money:')?></p>
				<?=$this->UserForm->getGatewaysButtons($activeGateways)?>
			</div>
			<div id="addForm" style="display:none">
				<?=$this->UserForm->create(false, array('class' => 'uk-form-stacked'))?>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Current Purchase Balance')?></label>
					<input type="text" class="uk-input" placeholder="<?=h($this->Currency->format($user['User']['purchase_balance']))?>" readonly>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label" id="totalAmountLabel"><?=__('Minimum Deposit Amount Is: ')?></label>
					<input type="text" id="totalAmount" class="uk-input" readonly>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Fee')?></label>
					<input type="text" id="feeAmount" class="uk-input" placeholder="<?=h($this->Currency->format(0))?>" readonly>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Amount')?></label>
					<?=$this->UserForm->input('amount', array(
						'class' => 'uk-input',
						'step' => 'real',
						));?>
				</div>
				<?=$this->UserForm->input('gateway', array(
					'type' => 'hidden',
					'id' => 'selectedGateway',
					))?>
				<div class="uk-margin uk-text-right">
					<button type="submit" class="uk-button uk-button-primary" disabled="disabled"><?=__('Transfer Money')?></button>
				</div>
				<?=$this->UserForm->end()?>
			</div>
		</div>
	</div>
</div>
<?php
	$totalPayment = __('Total Payment: ');
	$minimum = __('Minimum deposit amount is: ');
	$accountBalanceURL = Router::url(array('action' => 'transfer'));
	$this->Js->buffer("
		var gData = $gData;
		$('button[id^=gatewayButton]').click(function(event) {
			var gateway = $(this).val();
	
			if(gateway == 'AccountBalance') {
				return window.location.replace('$accountBalanceURL');
			}
	
			$('#gatewaySelector').hide();
			$('#selectedGateway').val(gateway);
			$('#amount').attr('min', gData[gateway]['minimum_deposit_amount']);
			$('#totalAmountLabel').text('$minimum');
			$('#totalAmount').attr('placeholder', formatCurrency(gData[gateway]['minimum_deposit_amount'], true));
			$('#feeAmount').attr('placeholder', gData[gateway]['deposit_fee_percent'] + '% + ' + formatCurrency(gData[gateway]['deposit_fee_amount'], true));
			$('#addForm').show();
		});
		$('#amount').on('change input', function(event) {
			var gateway = $('#selectedGateway').val();
			var v = $(this).val();
	
			if(v == '') {
				$('#totalAmountLabel').text('$minimum');
				$('#totalAmount').attr('placeholder', formatCurrency(gData[gateway]['minimum_deposit_amount'], true));
				$('#feeAmount').attr('placeholder', gData[gateway]['deposit_fee_percent'] + '% + ' + formatCurrency(gData[gateway]['deposit_fee_amount'], true));
				$('#addForm').find(':submit').attr('disabled', true);
				return;
			}
	
			try {
				v = Big(v);
			} catch(t) {
				$('#totalAmountLabel').text('$minimum');
				$('#totalAmount').attr('placeholder', formatCurrency(gData[gateway]['minimum_deposit_amount'], true));
				$('#feeAmount').attr('placeholder', gData[gateway]['deposit_fee_percent'] + '% + ' + formatCurrency(gData[gateway]['deposit_fee_amount'], true));
				$('#addForm').find(':submit').attr('disabled', true);
				return;
			}
	
			if(v.cmp(Big(gData[gateway]['minimum_deposit_amount'])) == -1) {
				$('#totalAmountLabel').text('$minimum');
				$('#totalAmount').attr('placeholder', formatCurrency(gData[gateway]['minimum_deposit_amount']));
				$('#feeAmount').attr('placeholder', gData[gateway]['deposit_fee_percent'] + '% + ' + formatCurrency(gData[gateway]['deposit_fee_amount'], true));
				$('#addForm').find(':submit').attr('disabled', true);
				return;
			}
	
			try {
				fee = Big(calculateFee(v, gData[gateway]['deposit_fee_percent'], gData[gateway]['deposit_fee_amount']));
				total = v.add(fee).toFixed(8);
				fee = formatCurrency(fee, true);
				total = formatCurrency(total, true);
				$('#totalAmountLabel').text('$totalPayment');
				$('#totalAmount').attr('placeholder', total);
				$('#feeAmount').attr('placeholder', fee);
				$('#addForm').find(':submit').attr('disabled', false);
			} catch(t) {
				$('#totalAmount').text('');
			}
		});
	");
	?>
