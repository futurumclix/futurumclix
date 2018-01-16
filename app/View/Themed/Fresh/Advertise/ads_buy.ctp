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
					<li class="uk-active"><?=__('You are buying PTC Advertisement')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<div class="uk-card uk-card-body uk-card-default uk-margin-top">
				<h5><?=__('Paid To Click Ads')?></h5>
				<p><?=__('Please fill in the form below and choose your payment method. After you will process your payment, we will review your advertisement and once it\'s reviewed, it will go live.')?></p>
				<?=$this->Notice->show()?>
				<?=$this->UserForm->create(false, array('class' => 'uk-form-stacked'))?>
				<div class="uk-margin">
					<label class="uk-form-label">
						<?=__('Title')?>
						<div id="titleCounter" class="uk-badge"> <?=isset($this->request->data['Ad']['title']) ? strlen($this->request->data['Ad']['title']) : 0?> / <?=$titleMax?></div>
					</label>
					<?=
						$this->UserForm->input('Ad.title', array(
							'placeholder' => __('Enter your advertisement title'),
							'class' => 'uk-input',
							'data-limit' => $titleMax,
							'data-counter' => 'titleCounter',
							))
							?>
				</div>
				<div class="uk-margin" id="description">
					<label class="uk-form-label">
						<?=__('Description')?>
						<div id="descCounter" class="uk-badge"><?=isset($this->request->data['Ad']['description']) ? strlen($this->request->data['Ad']['description']) : 0?> / <?=$descMax?></div>
					</label>
					<?=
						$this->UserForm->input('Ad.description', array(
							'placeholder' => __('Enter your advertisement description'),
							'type' => 'textArea',
							'class' => 'uk-textarea',
							'data-limit' => $descMax,
							'data-counter' => 'descCounter'
							))
							?>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('URL')?></label>
					<?=
						$this->UserForm->input('Ad.url', array(
							'placeholder' => 'http://',
							'class' => 'uk-input',
							))
							?>
				</div>
				<div class="uk-margin">
					<label>
					<?=$this->UserForm->input('Ad.hide_referer', array('class' => 'uk-checkbox'))?>
					<span title="<?=__('Hide source of the traffic.')?>" uk-tooltip><?=__('White label my traffic')?></span>
					</label>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Choose category')?></label>
					<?=
						$this->UserForm->input('Ad.ads_category_id', array(
							'class' => 'uk-select',
							'options' => $ads_categories,
							))
							?>
				</div>
				<div class="uk-margin" id="geoTargetting">
					<?php if(Module::active('AccurateLocationDatabase')): ?>
					<?=$this->Locations->selector($locations)?>
					<?php else: ?>
					<label class="uk-form-label"><?=__('Choose geo targetting')?></label>
					<?=
						$this->UserForm->input('Ad.TargettedLocations', array(
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
						$this->UserForm->input('Ad.ads_category_package_id', array(
							'class' => 'uk-select',
							'options' => isset($this->request->data['Ad']['ads_category_id']) ? $ads_category_packages[$this->request->data['Ad']['ads_category_id']] : reset($ads_category_packages),
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
