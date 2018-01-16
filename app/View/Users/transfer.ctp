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
							<h5><?=__('Add funds')?></h5>
						</div>
						<div class="col-sm-12" id="addForm">
							<?=$this->UserForm->create(false)?>
							<fieldset class="form-group col-sm-4">
								<label><?=__('Current Purchase Balance')?></label>
								<input type="text" class="form-control" placeholder="<?=h($this->Currency->format($user['User']['purchase_balance']))?>" readonly>
							</fieldset>
							<fieldset class="form-group col-sm-4">
								<label id="minimumAmountLabel"><?=__('Minimum Transfer Amount Is')?></label>
								<input type="text" id="minimumAmount" class="form-control" placeholder="<?=h($this->Currency->format($gData['AccountBalance']['minimum_deposit_amount']))?>" readonly>
							</fieldset>
							<fieldset class="form-group col-sm-4">
								<label id="maximumAmountLabel"><?=__('Maximum Transfer Amount Is')?></label>
								<input type="text" id="maximumAmount" class="form-control" placeholder="<?=h(bccomp('0', $gData['AccountBalance']['maximum_deposit_amount']) == 0 ? __('Unlimited') : $this->Currency->format($gData['AccountBalance']['maximum_deposit_amount']))?>" readonly>
							</fieldset>
							<fieldset class="form-group col-sm-4 ">
								<label id="totalAmountLabel"><?=__('Total Payment')?></label>
								<input type="text" id="totalAmount" class="form-control" readonly>
							</fieldset>
							<fieldset class="form-group col-sm-4">
								<label><?=__('Fee')?></label>
								<input type="text" id="feeAmount" class="form-control" placeholder="<?=h($this->Currency->format(0))?>" readonly>
							</fieldset>
							<fieldset class="form-group col-sm-4">
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
