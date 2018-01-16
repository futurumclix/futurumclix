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
					<li class="uk-active"><?=__('You are buying Express Advertisement')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<div class="uk-card uk-card-body uk-card-default uk-margin-top">
				<h5><?=__('Express Ads')?></h5>
				<p><?=__('Please fill in the form below and choose your payment method. After you will process your payment, we will review your advertisement and once it\'s reviewed, it will go live.')?></p>
				<?=$this->Notice->show()?>
				<?=$this->UserForm->create(false, array('class' => 'uk-form-stacked'))?>
				<div class="uk-margin">
					<label class="uk-form-label">
						<?=__('Title')?>
						<div id="titleCounter" class="uk-badge"> <?=isset($this->request->data['ExpressAd']['title']) ? strlen($this->request->data['ExpressAd']['title']) : 0?> / <?=$settings['titleLen']?></div>
					</label>
					<?=
						$this->UserForm->input('ExpressAd.title', array(
							'placeholder' => __('Enter your advertisement title'),
							'class' => 'uk-input',
							'data-limit' => $settings['titleLen'],
							'data-counter' => 'titleCounter',
						))
						?>
				</div>
				<?php if($settings['descShow']): ?>
				<div class="uk-margin" id="description">
					<label class="uk-form-label">
						<?=__('Description')?>
						<div id="descCounter" class="uk-badge"><?=isset($this->request->data['ExpressAd']['description']) ? strlen($this->request->data['ExpressAd']['description']) : 0?> / <?=$settings['descLen']?></div>
					</label>
					<?=
						$this->UserForm->input('ExpressAd.description', array(
							'placeholder' => __('Enter your advertisement description'),
							'type' => 'textArea',
							'class' => 'uk-textarea',
							'data-limit' => $settings['descLen'],
							'data-counter' => 'descCounter'
						))
						?>
				</div>
				<?php endif; ?>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('URL')?></label>
					<?=
						$this->UserForm->input('ExpressAd.url', array(
							'placeholder' => 'http://',
							'class' => 'uk-input',
							))
							?>
				</div>
				<div class="uk-margin">
					<label>
					<?=$this->UserForm->input('ExpressAd.hide_referer', array('class' => 'uk-checkbox'))?>
					<span title="<?=__('Hide source of the traffic.')?>" uk-tooltip><?=__('White label my traffic')?></span>
					</label>
				</div>
				<?php if($settings['geo_targetting']): ?>
				<div class="uk-margin" id="geoTargetting">
					<?php if(Module::active('AccurateLocationDatabase')): ?>
					<?=$this->Locations->selector($locations)?>
					<?php else: ?>
					<label class="uk-form-label"><?=__('Choose geo targetting')?></label>
					<?=
						$this->UserForm->input('ExpressAd.TargettedLocations', array(
							'class' => 'uk-textarea',
							'multiple' => 'multiple',
							'options' => $locations,
							'style' => 'height: 300px;',
							'selected' => $selectedLocations,
						))
						?>
					<a class="uk-button uk-button-primary" id="locationsSelectAll"><?=__('Select All')?></a>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<div class="uk-margin" id="membershipTargetting">
					<label class="uk-form-label"><?=__('Choose membership targetting')?></label>
					<?=
						$this->UserForm->input('TargettedMemberships', array(
							'class' => 'uk-textarea',
							'multiple' => 'multiple',
							'options' => $memberships,
							'style' => 'height: 150px;',
							'selected' => $selectedMemberships,
						))
						?>
					<a class="uk-button uk-button-primary" id="membershipsSelectAll"><?=__('Select All')?></a>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Click package')?></label>
					<?=
						$this->UserForm->input('ExpressAd.express_ads_package_id', array(
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
			price = Prices[$('#ExpressAdExpressAdsPackageId').val()][$('#gateway').val()]['withFee'];
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
		$('#ExpressAdExpressAdsPackageId').on('change', showPrice);
		showPrice();
	"); 
	?>
