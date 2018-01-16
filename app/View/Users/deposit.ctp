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
							<h5><?=__('Add Funds')?></h5>
						</div>
						<h6 class="text-xs-center" id="gatewaySelector">
							<?=__('Please Choose From Where You Want To Transfer Money:')?>
							<div class="gatewaybuttons">
								<?=$this->UserForm->getGatewaysButtons($activeGateways)?>
							</div>
						</h6>
						<div class="col-sm-12" id="addForm" style="display:none">
							<?=$this->UserForm->create(false)?>
							<fieldset class="form-group col-sm-4">
								<label><?=__('Current Purchase Balance')?></label>
								<input type="text" class="form-control" placeholder="<?=h($this->Currency->format($user['User']['purchase_balance']))?>" readonly>
							</fieldset>
							<fieldset class="form-group col-sm-4">
								<label id="totalAmountLabel"><?=__('Minimum Deposit Amount Is: ')?></label>
								<input type="text" id="totalAmount" class="form-control" readonly>
							</fieldset>
							<fieldset class="form-group col-sm-4">
								<label><?=__('Fee')?></label>
								<input type="text" id="feeAmount" class="form-control" placeholder="<?=h($this->Currency->format(0))?>" readonly>
							</fieldset>
							<fieldset class="form-group col-sm-4 col-md-offset-4">
								<label><?=__('Amount')?></label>
								<div class="input-group">
									<?=$this->UserForm->input('amount', array(
										'type' => 'money',
										'class' => 'form-control',
										'placeholder' => __('Amount'),
										'step' => 'real',
										'symbol' => 'input-group-addon',
										));?>
								</div>
							</fieldset>
							<div class="clearfix"></div>
							<?=$this->UserForm->input('gateway', array(
								'type' => 'hidden',
								'id' => 'selectedGateway',
								))?>
							<div class="text-xs-center">
								<button type="submit" class="btn btn-primary" disabled="disabled"><?=__('Transfer Money')?></button>
							</div>
						</div>
							<?=$this->UserForm->end()?>
						<div class="clearfix"></div>
					</div>
				</div>
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
