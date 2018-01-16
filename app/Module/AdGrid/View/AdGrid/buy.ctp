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
							<h5><?=__d('ad_grid', 'AdGrid Ads Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('action' => 'add'),
								'buy' => array('action' => 'buy'),
								'assign' => array('action' => 'assign'),
							))
							?>
						<div class="padding30-col">
							<h5><?=__d('ad_grid', 'Purchase advertisement exposures')?></h5>
							<div class="col-md-8 col-md-offset-2 margin30-top">
								<?=$this->UserForm->create(false)?>
								<fieldset class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=__d('ad_grid', 'Pack')?></div>
										<?=$this->UserForm->input('package_id', array('class' => 'form-control', 'id' => 'packSelect'))?>
									</div>
								</fieldset>
								<fieldset class="form-group">
									<div class="input-group">
										<div id="pricePerLabel" class="input-group-addon"><?=__d('ad_grid', 'Price per click')?></div>
										<input type="text" readonly="" placeholder="$0.001" class="form-control" id="pricePer">
									</div>
								</fieldset>
								<fieldset class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=__d('ad_grid', 'Total price')?></div>
										<input type="text" readonly="" placeholder="$10" class="form-control" id="price">
									</div>
								</fieldset>
								<div class="gatewaybuttons text-xs-center">
									<?=$this->UserForm->getGatewaysButtons($activeGateways, array('prices' => $packagesData[key($packagesData)]['AdGridAdsPackage']['prices']))?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?=$this->UserForm->end()?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	$clicksLabel = __d('ad_grid', 'Price per click');
	$daysLabel = __d('ad_grid', 'Price per day');
	$packagesData = json_encode($packagesData);

	$this->Js->buffer("
		var Data = $packagesData;

		function calcPrice() {
			var pack_id = $('#packSelect').val();

			switch(Data[pack_id]['AdGridAdsPackage']['type']) {
				case 'Clicks':
					$('#pricePerLabel').html('$clicksLabel');
				break;

				case 'Days':
					$('#pricePerLabel').html('$daysLabel');
				break;
			}
			$('#price').attr('placeholder', formatCurrency(Data[pack_id]['AdGridAdsPackage']['price']));
			$('#pricePer').attr('placeholder', formatCurrency(Data[pack_id]['AdGridAdsPackage']['price_per']));

			$.each(Data[pack_id]['AdGridAdsPackage']['prices'], function(key, value) {
				if(value == 'disabled') {
					$('.' + key + 'Price').parent().hide();
				} else {
					$('.' + key + 'Price').html(formatCurrency(value));
					$('.' + key + 'Price').parent().show();
				}
			});
		}

		$('#packSelect').change(calcPrice);
		calcPrice();
	");
	?>