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
							<h5><?=__('Paid Offers Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('controller' => 'paid_offers', 'action' => 'add'),
								'buy' => array('controller' => 'paid_offers', 'action' => 'buy'),
								'assign' => array('controller' => 'paid_offers', 'action' => 'assign'),
							))
							?>
						<div class="padding30-col">
							<?php if(empty($packages)): ?>
								<h5><?=__('Sorry, but currently there are no available advertisement exposures')?></h5>
							<?php else: ?>
								<h5><?=__('Purchase advertisement exposures')?></h5>
								<div class="col-md-8 col-md-offset-2 margin30-top">
									<?=$this->UserForm->create(false)?>
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__('Pack')?></div>
											<?=$this->UserForm->input('package_id', array('class' => 'form-control', 'id' => 'packSelect'))?>
										</div>
									</fieldset>
									<fieldset class="form-group">
										<div class="input-group">
											<div id="pricePerLabel" class="input-group-addon"><?=__('Price Per Slot')?></div>
											<input type="text" readonly="" placeholder="$0.001" class="form-control" id="pricePer">
										</div>
									</fieldset>
									<div class="gatewaybuttons text-xs-center">
										<?=$this->UserForm->getGatewaysButtons($activeGateways, array('prices' => $packagesData[key($packagesData)]['PaidOffersPackage']['prices']))?>
									</div>
								</div>
								<div class="clearfix"></div>
								<?=$this->UserForm->end()?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	$packagesData = json_encode($packagesData);

	$this->Js->buffer("
		var Data = $packagesData;

		function calcPrice() {
			var pack_id = $('#packSelect').val();

			$('#price').attr('placeholder', formatCurrency(Data[pack_id]['PaidOffersPackage']['price']));
			$('#pricePer').attr('placeholder', formatCurrency(Data[pack_id]['PaidOffersPackage']['price_per']));

			$.each(Data[pack_id]['PaidOffersPackage']['prices'], function(key, value) {
				if(value == 'disabled') {
					$('.' + key + 'Price').parent().hide();
				} else {
					$('.' + key + 'Price').parent().show();
					$('.' + key + 'Price').html(formatCurrency(value));
				}
			});
		}

		$('#packSelect').change(calcPrice);
		calcPrice();
	");
?>
