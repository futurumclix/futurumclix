<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Advertisement Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'ads', 'action' => 'add'),
				'buy' => array('controller' => 'ads_category_packages', 'action' => 'buy'),
				'assign' => array('controller' => 'ads', 'action' => 'assign'),
				))
				?>
			<h2 class="uk-margin-top"><?=__('Purchase Advertisement Exposures')?></h2>
			<div class="uk-form-horizontal">
				<?=$this->UserForm->create(false)?>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Advertisement Category')?></label>
					<?=$this->UserForm->input('category_id', array('class' => 'uk-select', 'id' => 'categorySelect'))?>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Pack')?></label>
					<?=$this->UserForm->input('package_id', array('class' => 'uk-select', 'id' => 'packSelect', 'options' => $packages[key($categories)]))?>
				</div>
				<div class="uk-margin">
					<label id="pricePerLabel" class="uk-form-label"><?=__('Price Per Click')?></label>
					<input type="text" readonly="" placeholder="$0.001" class="uk-input" id="pricePer">
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Total Price')?>
				</div>
				<input type="text" readonly="" placeholder="$10" class="uk-input" id="price">
			</div>
			<div class="uk-margin uk-text-center">
				<?=$this->UserForm->getGatewaysButtons($activeGateways, array('prices' => $packagesData[ltrim(key($packages[key($categories)]), 'a')]['AdsCategoryPackage']['prices']))?>
			</div>
			<?=$this->UserForm->end()?>
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
