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
							<h5><?=__('Advertisement Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('controller' => 'ads', 'action' => 'add'),
								'buy' => array('controller' => 'ads_category_packages', 'action' => 'buy'),
								'assign' => array('controller' => 'ads', 'action' => 'assign'),
							))
						?>
						<div class="padding30-col">
							<h5><?=__('Purchase Advertisement Exposures')?></h5>
							<div class="col-md-8 col-md-offset-2 margin30-top">
								<?=$this->UserForm->create(false)?>
								<fieldset class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=__('Advertisement Category')?></div>
										<?=$this->UserForm->input('category_id', array('class' => 'form-control', 'id' => 'categorySelect'))?>
									</div>
								</fieldset>
								<fieldset class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=__('Pack')?></div>
										<?=$this->UserForm->input('package_id', array('class' => 'form-control', 'id' => 'packSelect', 'options' => $packages[key($categories)]))?>
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
									<?=$this->UserForm->getGatewaysButtons($activeGateways, array('prices' => $packagesData[ltrim(key($packages[key($categories)]), 'a')]['AdsCategoryPackage']['prices']))?>
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
	$clicksLabel = __('Price Per Click');
	$daysLabel = __('Price Per Day');
	$packages = json_encode($packages);
	$packagesData = json_encode($packagesData);
	
	$this->Js->buffer("
		var Packages = $packages;
		var Data = $packagesData;
	
		function showPacks() {
			var category_id = $('#categorySelect').val();
	
			$('#packSelect').empty();
			$.each(Packages[category_id], function(key, value) {
				$('#packSelect').append($('<option></option>').attr('value', key).text(value));
			});
			calcPrice();
		}
	
		function calcPrice() {
			var pack_id = $('#packSelect').val();
			pack_id = pack_id.substring(1, pack_id.length);
	
			switch(Data[pack_id]['AdsCategoryPackage']['type']) {
				case 'Clicks':
					$('#pricePerLabel').html('$clicksLabel');
				break;
	
				case 'Days':
					$('#pricePerLabel').html('$daysLabel');
				break;
			}
			$('#price').attr('placeholder', formatCurrency(Data[pack_id]['AdsCategoryPackage']['price']));
			$('#pricePer').attr('placeholder', formatCurrency(Data[pack_id]['AdsCategoryPackage']['price_per']));
	
			$.each(Data[pack_id]['AdsCategoryPackage']['prices'], function(key, value) {
				if(value == 'disabled') {
					$('.' + key + 'Price').parent().hide();
				} else {
					$('.' + key + 'Price').parent().show();
					$('.' + key + 'Price').html(formatCurrency(value));
				}
			});
		}
	
		$('#packSelect').change(calcPrice);
		$('#categorySelect').change(showPacks);
		showPacks();
	");
	?>
