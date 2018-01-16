<div class="container advertise">
	<div class="row">
		<div class="col">
			<h2 class="text-xs-center"><?=__('You are buying PTC Advertisement')?></h2>
		</div>
	</div>
	<script>
		<?='var CurrencyHelperData = '.json_encode($this->Currency, JSON_NUMERIC_CHECK)?>
	</script>
	<div class="row margin30-top padding30-bottom">
		<div class="col-md-12">
			<div class="panel">
				<div class="padding30-col">
					<h5>
					<?=__('Paid To Click Ads')?></h5>
					<p><?=__('Please fill in the form below and choose your payment method. After you will process your payment, we will review your advertisement and once it\'s reviewed, it will go live.')?></p>
					<div class="col-md-8 col-md-offset-2 margin30-top">
						<?=$this->Notice->show()?>
						<?=$this->UserForm->create(false)?>
							<fieldset class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=__('Title')?></div>
									<?=
										$this->UserForm->input('Ad.title', array(
											'placeholder' => __('Enter your advertisement title'),
											'class' => 'form-control',
											'data-limit' => $titleMax,
											'data-counter' => 'titleCounter',
										))
									?>
									<div id="titleCounter" class="input-group-addon"> <?=isset($this->request->data['Ad']['title']) ? strlen($this->request->data['Ad']['title']) : 0?> / <?=$titleMax?></div>
								</div>
							</fieldset>
							<fieldset class="form-group" id="description">
								<div class="input-group">
									<div class="input-group-addon"><?=__('Description')?></div>
									<?=
										$this->UserForm->input('Ad.description', array(
											'placeholder' => __('Enter your advertisement description'),
											'type' => 'textArea',
											'class' => 'form-control',
											'data-limit' => $descMax,
											'data-counter' => 'descCounter'
										))
									?>
									<div id="descCounter" class="input-group-addon"><?=isset($this->request->data['Ad']['description']) ? strlen($this->request->data['Ad']['description']) : 0?> / <?=$descMax?></div>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=__('URL')?></div>
									<?=
										$this->UserForm->input('Ad.url', array(
											'placeholder' => 'http://',
											'class' => 'form-control',
										))
									?>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<div class="input-group">
									<span class="input-group-addon">
										<?=$this->UserForm->input('Ad.hide_referer')?>
									</span>
									<input type="text" class="form-control" data-toggle="tooltip" data-placement="top" readonly="" value="<?=__('White label my traffic')?>" title="<?=__('Hide source of the traffic.')?>">
								</div>
							</fieldset>
							<fieldset class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=__('Choose category')?></div>
									<?=
										$this->UserForm->input('Ad.ads_category_id', array(
											'class' => 'form-control',
											'options' => $ads_categories,
										))
									?>
								</div>
							</fieldset>
							<fieldset class="form-group" id="geoTargetting">
								<div class="input-group">
									<?php if(Module::active('AccurateLocationDatabase')): ?>
										<?=$this->Locations->selector($locations)?>
									<?php else: ?>
										<div class="input-group-addon"><?=__('Choose geo targetting')?></div>
										<?=
											$this->UserForm->input('Ad.TargettedLocations', array(
												'class' => 'form-control',
												'multiple' => 'multiple',
												'options' => $locations,
												'style' => 'height: 300px;',
												'selected' => $selectedLocations,
											))
										?>
										<a class="btn btn-primary col-md-12" id="locationsSelectAll"><?=__('Select All')?></a>
									<?php endif; ?>
								</div>
							</fieldset>
							<fieldset class="form-group" id="membershipTargetting">
								<div class="input-group">
									<div class="input-group-addon"><?=__('Choose membership targetting')?></div>
									<?=
										$this->UserForm->input('TargettedMemberships', array(
											'class' => 'form-control',
											'multiple' => 'multiple',
											'options' => $memberships,
											'style' => 'height: 150px;',
											'selected' => $selectedMemberships,
										))
									?>
									<a class="btn btn-primary col-md-12" id="membershipsSelectAll"><?=__('Select All')?></a>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=__('Click package')?></div>
									<?=
										$this->UserForm->input('Ad.ads_category_package_id', array(
											'class' => 'form-control',
											'options' => isset($this->request->data['Ad']['ads_category_id']) ? $ads_category_packages[$this->request->data['Ad']['ads_category_id']] : reset($ads_category_packages),
										))
									?>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=__('Payment method')?></div>
									<?=
										$this->UserForm->input('gateway', array(
											'class' => 'form-control',
										))
									?>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=__('E-mail')?></div>
									<?=
										$this->UserForm->input('email', array(
											'class' => 'form-control',
										))
									?>
								</div>
							</fieldset>
							<div class="row">
								<div class="col-md-12 text-xs-right">
									<button class="btn btn-primary" id="buyButton"><?=__('Buy')?></button>
								</div>
							</div>
						<?=$this->UserForm->end()?>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$packages = json_encode($ads_category_packages);
$options = json_encode($options);
$prices = json_encode($prices);
$buy = __('Buy');
$this->Js->buffer("
	var Packages = $packages;
	var Categories = $options;
	var Prices = $prices;
	$('[data-counter]').on('keyup', charCounter);

	function showPrice() {
		price = Prices[$('#AdAdsCategoryPackageId').val()][$('#gateway').val()]['withFee'];
		$('#buyButton').html('$buy ' + formatCurrency(price));
	}

	function setPackets() {
		cat_id = $('#AdAdsCategoryId').val();

		$('#AdAdsCategoryPackageId option').remove();
		$.each(Packages[cat_id], function(k, v) {
			$('#AdAdsCategoryPackageId').append($('<option></option>').attr('value', k).text(v));
		});
		showPrice();
	}

	$('#gateway').on('change', showPrice);
	$('#AdAdsCategoryPackageId').on('change', showPrice);

	showPrice();

	$('#AdAdsCategoryId').on('change', function() {
		cat_id = $('#AdAdsCategoryId').val();

		$('#description').toggle(Categories[cat_id]['allow_description']);
		$('#geoTargetting').toggle(Categories[cat_id]['geo_targetting']);

		setPackets();
	});

	$('#membershipsSelectAll').click(function() {
		$('#TargettedMemberships option').prop('selected', true);
		return false;
	});

	$('#locationsSelectAll').click(function() {
		$('#TargettedLocations option').prop('selected', true);
		return false;
	});

	$('#description').toggle(Categories[$('#AdAdsCategoryId').val()]['allow_description']);
	$('#geoTargetting').toggle(Categories[$('#AdAdsCategoryId').val()]['geo_targetting']);
"); 
?>
