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
			<h2 class="uk-margin-top"><?=__('Edit advertisement')?></h2>
			<?=$this->UserForm->create('PaidOffer', array('class' => 'uk-form-horizontal'))?>
			<?=$this->UserForm->input('PaidOffer.id')?>
			<div class="uk-margin">
				<label class="uk-form-label">
					<?=__('Title')?>
					<div id="titleCounter" class="uk-badge"><?=isset($this->request->data['PaidOffer']['title']) ? strlen($this->request->data['PaidOffer']['title']) : 0?> / <?=$titleMax?></div>
				</label>
				<?=
					$this->UserForm->input('title', array(
					'palaceholder' => __('Enter your offertitle'),
					'class' => 'uk-input',
					'data-limit' => $titleMax,
					'data-counter' => 'titleCounter'
					))
					?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label">
					<?=__('Description')?>
					<div id="descCounter" class="uk-badge"><?=isset($this->request->data['PaidOffer']['description']) ? strlen($this->request->data['PaidOffer']['description']) : 0?> / <?=$descMax?></div>
				</label>
				<?=
					$this->UserForm->input('description', array(
					'type' => 'textarea',
					'class' => 'uk-textarea',
					'placeholder' => __('Please describe in details what applicant have to do to get this offer approved and what info has to be provided after submitting this offer.'),
					'style' => 'resize:none;',
					'data-limit' => $descMax,
					'data-counter' => 'descCounter',
					))
					?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('URL')?></label>
				<?=
					$this->UserForm->input('url', array(
					'class' => 'uk-input',
					'placeholder' => 'http://',
					))
					?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Category')?></label>
				<?=$this->UserForm->input('category_id', array('class' => 'uk-select'))?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Targeted Memberships')?></label>
				<?=
					$this->UserForm->input('TargettedMemberships', array(
					'type' => 'select',
					'class' => 'uk-textarea',
					'multiple' => 'multiple',
					'options' => $memberships,
					))
					?>
				<input class="uk-button uk-button-primary" type="button" value="<?=__('Select All')?>" style="vertical-align: top;" onclick="selectAll('TargettedMembershipsTargettedMemberships')">
			</div>
			<?php if(Module::active('AccurateLocationDatabase')): ?>
			<?=$this->Locations->selector($countries)?>
			<?php else: ?>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Targeted Locations')?></label>
				<?=
					$this->UserForm->input('TargettedLocations', array(
					'type' => 'select',
					'class' => 'uk-textarea',
					'multiple' => 'multiple',
					'options' => $countries,
					'selected' => $selectedCountries,
					))
					?>
				<input class="uk-button uk-button-primary" type="button" value="<?=__('Select All')?>" style="vertical-align: top;" onclick="selectAll('PaidOfferTargettedLocations')">
			</div>
			<?php endif; ?>
			<div class="uk-margin uk-text-right">
				<button class="uk-button uk-button-primary"><?=__('Add')?></button>
			</div>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
