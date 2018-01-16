<script>
	<?='var CurrencyHelperData = '.json_encode($this->Currency, JSON_NUMERIC_CHECK)?>
</script>
<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('You are buying AdGrid Advertisement')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<div class="uk-card uk-card-body uk-card-default uk-margin-top">
				<h5><?=__('AdGrid Ads')?></h5>
				<p><?=__('Please fill in the form below and choose your payment method. After you will process your payment, we will review your advertisement and once it\'s reviewed, it will go live.')?></p>
				<div class="col-md-8 col-md-offset-2 margin30-top">
					<?=$this->Notice->show()?>
					<?=$this->UserForm->create(false, array('class' => 'uk-form-stacked'))?>
					<div class="uk-margin">
						<label class="uk-form-label"><?=__('URL')?></label>
						<?=
							$this->UserForm->input('AdGridAd.url', array(
								'placeholder' => 'http://',
								'class' => 'uk-input',
							))
							?>
					</div>
					<div class="uk-margin">
						<label class="uk-form-label"><?=__('Click package')?></label>
						<?=
							$this->UserForm->input('AdGridPackageId', array(
								'class' => 'uk-select',
								'options' => $packages,
							))
							?>
					</div>
					<div class="uk-margin">
						<label class="uk-form-label"><?=__('Payment method')?></label>
						<?=
							$this->UserForm->input('gateway', array(
								'class' => 'uk-select',
							))
							?>
					</div>
					<div class="uk-margin">
						<label class="uk-form-label"><?=__('E-mail')?></label>
						<?=
							$this->UserForm->input('email', array(
								'class' => 'uk-input',
							))
							?>
					</div>
					</fieldset>
					<div class="uk-margin uk-text-right">
						<button class="uk-button uk-button-primary" id="buyButton"><?=__('Buy')?></button>
					</div>
					<?=$this->UserForm->end()?>
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
	
		function showPrice() {
			price = Prices[$('#AdGridPackageId').val()][$('#gateway').val()]['withFee'];
			$('#buyButton').html('$buy ' + formatCurrency(price));
		}
	
		$('#gateway').on('change', showPrice);
		$('#AdGridPackageId').on('change', showPrice);
		showPrice();
	"); 
	?>
