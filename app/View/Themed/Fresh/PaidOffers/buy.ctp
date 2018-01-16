<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Paid Offers Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'paid_offers', 'action' => 'add'),
				'buy' => array('controller' => 'paid_offers', 'action' => 'buy'),
				'assign' => array('controller' => 'paid_offers', 'action' => 'assign'),
				))
				?>
			<?php if(empty($packages)): ?>
			<h5><?=__('Sorry, but currently there are no available advertisement exposures')?></h5>
			<?php else: ?>
			<h2 class="uk-margin-top"><?=__('Purchase advertisement exposures')?></h2>
			<div class="uk-form-horizontal">
				<?=$this->UserForm->create(false)?>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Pack')?></label>
					<?=$this->UserForm->input('package_id', array('class' => 'uk-select', 'id' => 'packSelect'))?>
				</div>
				<div class="uk-margin">
					<label id="pricePerLabel" class="uk-form-label"><?=__('Price Per Slot')?></label>
					<input type="text" readonly="" placeholder="$0.001" class="uk-input" id="pricePer">
				</div>
				<div class="uk-margin uk-text-center">
					<?=$this->UserForm->getGatewaysButtons($activeGateways, array('prices' => $packagesData[key($packagesData)]['PaidOffersPackage']['prices']))?>
				</div>
			</div>
			<?=$this->UserForm->end()?>
			<?php endif; ?>
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
