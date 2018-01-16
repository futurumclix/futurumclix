<div class="container advertise">
	<div class="row">
		<div class="col">
			<h2 class="text-xs-center"><?=__('You are buying Explorer Advertisement')?></h2>
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
					<?=__('Explorer Ads')?></h5>
					<p><?=__('Please fill in the form below and choose your payment method. After you will process your payment, we will review your advertisement and once it\'s reviewed, it will go live.')?></p>
					<div class="col-md-8 col-md-offset-2 margin30-top">
						<?=$this->Notice->show()?>
						<?=$this->UserForm->create(false)?>
							<fieldset class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=__('Title')?></div>
									<?=
										$this->UserForm->input('ExplorerAd.title', array(
											'placeholder' => __('Enter your advertisement title'),
											'class' => 'form-control',
											'data-limit' => $settings['titleLen'],
											'data-counter' => 'titleCounter',
										))
									?>
									<div id="titleCounter" class="input-group-addon"> <?=isset($this->request->data['ExplorerAd']['title']) ? strlen($this->request->data['ExplorerAd']['title']) : 0?> / <?=$settings['titleLen']?></div>
								</div>
							</fieldset>
							<?php if($settings['descShow']): ?>
								<fieldset class="form-group" id="description">
									<div class="input-group">
										<div class="input-group-addon"><?=__('Description')?></div>
										<?=
											$this->UserForm->input('ExplorerAd.description', array(
												'placeholder' => __('Enter your advertisement description'),
												'type' => 'textArea',
												'class' => 'form-control',
												'data-limit' => $settings['descLen'],
												'data-counter' => 'descCounter'
											))
										?>
										<div id="descCounter" class="input-group-addon"><?=isset($this->request->data['ExplorerAd']['description']) ? strlen($this->request->data['ExplorerAd']['description']) : 0?> / <?=$settings['descLen']?></div>
									</div>
								</fieldset>
							<?php endif; ?>
							<fieldset class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=__('URL')?></div>
									<?=
										$this->UserForm->input('ExplorerAd.url', array(
											'placeholder' => 'http://',
											'class' => 'form-control',
										))
									?>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<div class="input-group">
									<span class="input-group-addon">
										<?=$this->UserForm->input('ExplorerAd.hide_referer')?>
									</span>
									<input type="text" class="form-control" data-toggle="tooltip" data-placement="top" readonly="" value="<?=__('White label my traffic')?>" title="<?=__('Hide source of the traffic.')?>">
								</div>
							</fieldset>
							<?php if($settings['geo_targetting']): ?>
								<fieldset class="form-group" id="geoTargetting">
									<div class="input-group">
										<?php if(Module::active('AccurateLocationDatabase')): ?>
											<?=$this->Locations->selector($locations)?>
										<?php else: ?>
											<div class="input-group-addon"><?=__('Choose geo targetting')?></div>
											<?=
												$this->UserForm->input('ExplorerAd.TargettedLocations', array(
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
							<?php endif; ?>
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
										$this->UserForm->input('ExplorerAd.explorer_ads_package_id', array(
											'class' => 'form-control',
											'options' => $packages,
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
$prices = json_encode($prices);
$buy = __('Buy');
$this->Js->buffer("
	var Prices = $prices;
	$('[data-counter]').on('keyup', charCounter);

	function showPrice() {
		price = Prices[$('#ExplorerAdExplorerAdsPackageId').val()][$('#gateway').val()]['withFee'];
		$('#buyButton').html('$buy ' + formatCurrency(price));
	}

	$('#membershipsSelectAll').click(function() {
		$('#TargettedMemberships option').prop('selected', true);
		return false;
	});

	$('#locationsSelectAll').click(function() {
		$('#TargettedLocations option').prop('selected', true);
		return false;
	});

	$('#gateway').on('change', showPrice);
	$('#ExplorerAdExplorerAdsPackageId').on('change', showPrice);
	showPrice();
"); 
?>
