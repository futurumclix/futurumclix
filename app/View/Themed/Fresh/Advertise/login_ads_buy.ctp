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
					<li class="uk-active"><?=__('You are buying Login Advertisement')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<div class="uk-card uk-card-body uk-card-default uk-margin-top">
				<h5><?=__('Login Ads')?></h5>
				<p><?=__('Please fill in the form below and choose your payment method. After you will process your payment, we will review your advertisement and once it\'s reviewed, it will go live.')?></p>
				<?=$this->Notice->show()?>
				<?=$this->UserForm->create(false, array('class' => 'uk-form-stacked'))?>
				<div class="uk-margin">
					<label class="uk-form-label">
						<?=__('Title')?>
						<div id="titleCounter" class="uk-badge"> <?=isset($this->request->data['LoginAd']['title']) ? strlen($this->request->data['LoginAd']['title']) : 0?> / <?=$titleMax?></div>
					</label>
					<?=
						$this->UserForm->input('LoginAd.title', array(
							'placeholder' => __('Enter your advertisement title'),
							'class' => 'uk-input',
							'data-limit' => $titleMax,
							'data-counter' => 'titleCounter',
						))
						?>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('URL')?></label>
					<?=
						$this->UserForm->input('LoginAd.url', array(
							'placeholder' => 'http://',
							'class' => 'uk-input',
							))
							?>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Image URL')?></label>
					<?=
						$this->UserForm->input('LoginAd.image_url', array(
							'placeholder' => 'http://',
							'class' => 'uk-input',
						))
						?>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Click package')?></label>
					<?=
						$this->UserForm->input('LoginAdPackageId', array(
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
				<div class="uk-margin uk-text-right">
					<button class="uk-button uk-button-primary" id="buyButton"><?=__('Buy')?></button>
				</div>
			</div>
			<?=$this->UserForm->end()?>
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
			price = Prices[$('#LoginAdPackageId').val()][$('#gateway').val()]['withFee'];
			$('#buyButton').html('$buy ' + formatCurrency(price));
		}
	
		$('#gateway').on('change', showPrice);
		$('#LoginAdPackageId').on('change', showPrice);
		showPrice();
	"); 
	?>
