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
							<h5><?=__('Banner Ads Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('controller' => 'explorer_ads', 'action' => 'add'),
								'buy' => array('controller' => 'explorer_ads', 'action' => 'buy'),
								'assign' => array('controller' => 'explorer_ads', 'action' => 'assign'),
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
										<div id="pricePerLabel" class="input-group-addon"><?=__('Price Per Click')?></div>
										<input type="text" readonly="" placeholder="$0.001" class="form-control" id="pricePer">
									</div>
								</fieldset>
								<fieldset class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=__('Total Price')?></div>
										<input type="text" readonly="" placeholder="$10" class="form-control" id="price">
									</div>
								</fieldset>
								<div class="gatewaybuttons text-xs-center">
									<?=$this->UserForm->getGatewaysButtons($activeGateways, array('prices' => $packagesData[key($packagesData)]['ExplorerAdsPackage']['prices']))?>
								</div>
							</div>
							<?=$this->UserForm->end()?>
						</div>
						<div class="clearfix"></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<?php
	$clicksLabel = __('Price Per Click');
	$daysLabel = __('Price Per Day');
	$packagesData = json_encode($packagesData);

	$this->Js->buffer("
		var Data = $packagesData;

		function calcPrice() {
			var pack_id = $('#packSelect').val();

			switch(Data[pack_id]['ExplorerAdsPackage']['type']) {
				case 'Clicks':
					$('#pricePerLabel').html('$clicksLabel');
				break;

				case 'Days':
					$('#pricePerLabel').html('$daysLabel');
				break;
			}
			$('#price').attr('placeholder', formatCurrency(Data[pack_id]['ExplorerAdsPackage']['price']));
			$('#pricePer').attr('placeholder', formatCurrency(Data[pack_id]['ExplorerAdsPackage']['price_per']));

			$.each(Data[pack_id]['ExplorerAdsPackage']['prices'], function(key, value) {
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
