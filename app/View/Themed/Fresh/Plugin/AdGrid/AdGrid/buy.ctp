<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__d('ad_grid', 'AdGrid Ads Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('action' => 'add'),
				'buy' => array('action' => 'buy'),
				'assign' => array('action' => 'assign'),
				))
				?>
			<h2 class="uk-margin-top"><?=__d('ad_grid', 'Purchase advertisement exposures')?></h2>
			<div class="uk-form-horizontal">
				<?=$this->UserForm->create(false)?>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__d('ad_grid', 'Pack')?></label>
					<?=$this->UserForm->input('package_id', array('class' => 'uk-select', 'id' => 'packSelect'))?>
				</div>
				<div class="uk-margin">
					<label id="pricePerLabel" class="uk-form-label"><?=__d('ad_grid', 'Price per click')?></label>
					<input type="text" readonly="" placeholder="$0.001" class="uk-input" id="pricePer">
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__d('ad_grid', 'Total price')?></label>
					<input type="text" readonly="" placeholder="$10" class="uk-input" id="price">
				</div>
				<div class="uk-margin uk-text-center">
					<?=$this->UserForm->getGatewaysButtons($activeGateways, array('prices' => $packagesData[key($packagesData)]['AdGridAdsPackage']['prices']))?>
				</div>
				<?=$this->UserForm->end()?>
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
