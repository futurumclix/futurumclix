<div class="container advertise">
	<div class="row">
		<div class="col">
			<h2 class="text-xs-center"><?=__('You are buying Banner Advertisement')?></h2>
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
					<?=__('Banner Ads')?></h5>
					<p><?=__('Please fill in the form below and choose your payment method. After you will process your payment, we will review your advertisement and once it\'s reviewed, it will go live.')?></p>
					<div class="col-md-8 col-md-offset-2 margin30-top">
						<?=$this->Notice->show()?>
						<?=$this->UserForm->create(false)?>
							<fieldset class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=__('Title')?></div>
									<?=
										$this->UserForm->input('BannerAd.title', array(
											'placeholder' => __('Enter your advertisement title'),
											'class' => 'form-control',
											'data-limit' => $titleMax,
											'data-counter' => 'titleCounter',
										))
									?>
									<div id="titleCounter" class="input-group-addon"> <?=isset($this->request->data['BannerAd']['title']) ? strlen($this->request->data['BannerAd']['title']) : 0?> / <?=$titleMax?></div>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=__('URL')?></div>
									<?=
										$this->UserForm->input('BannerAd.url', array(
											'placeholder' => 'http://',
											'class' => 'form-control',
										))
									?>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=__('Image URL')?></div>
									<?=
										$this->UserForm->input('BannerAd.image_url', array(
											'placeholder' => 'http://',
											'class' => 'form-control',
										))
									?>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<div class="input-group">
									<div class="input-group-addon"><?=__('Click package')?></div>
									<?=
										$this->UserForm->input('BannerAdPackageId', array(
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
		price = Prices[$('#BannerAdPackageId').val()][$('#gateway').val()]['withFee'];
		$('#buyButton').html('$buy ' + formatCurrency(price));
	}

	$('#gateway').on('change', showPrice);
	$('#BannerAdPackageId').on('change', showPrice);
	showPrice();
"); 
?>
