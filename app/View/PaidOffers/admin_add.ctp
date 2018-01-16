<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Add new Paid Offer')?></h2>
	</div>
	<?=$this->AdminForm->create('PaidOffer', array('class' => 'form-horizontal'))?>
		<div class="form-group">
			<label for="advertiser" class="col-sm-3 control-label"><?=__d('admin', 'Advertiser')?></label>
			<div class="col-sm-9">
				<?=
					$this->AdminForm->input('Advertiser.username', array(
						'data-toggle' => 'tooltip',
						'data-placement' => 'top',
						'title' => __d('admin', 'Leave blank for self sponsored advertisement (admin\'s ad)'),
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label for="title" class="col-sm-3 control-label"><?=__d('admin', 'Title')?></label>
			<div class="col-sm-9">
				<div class="input-group">
					<?=
						$this->AdminForm->input('title', array(
							'data-limit' => $titleMax,
							'data-counter' => 'titleCounter'
						))
					?>
					<div id="titleCounter" class="input-group-addon"><?=isset($this->request->data['PaidOffer']['title']) ? strlen($this->request->data['PaidOffer']['title']) : 0?> / <?=$titleMax?></div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="description" class=" col-sm-3 control-label"><?=__d('admin', 'Description')?></label>
			<div class="col-sm-9">
				<div class="input-group">
					<?=
						$this->AdminForm->input('description', array(
							'type' => 'textarea',
							'data-limit' => $descMax,
							'data-counter' => 'descCounter'
						))
					?>
					<div id="descCounter" class="input-group-addon"><?=isset($this->request->data['PaidOffer']['description']) ? strlen($this->request->data['PaidOffer']['description']) : 0?> / <?=$descMax?></div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="url" class=" col-sm-3 control-label"><?=__d('admin', 'URL')?></label>
			<div class="col-sm-9">
				<?=$this->AdminForm->input('url')?>
			</div>
		</div>
		<div class="form-group">
			<label for="url" class=" col-sm-3 control-label"><?=__d('admin', 'Slots')?></label>
			<div class="col-sm-9">
				<?=$this->AdminForm->input('total_slots')?>
			</div>
		</div>
		<div class="form-group">
			<label for="expiry" class=" col-sm-3 control-label"><?=__d('admin', 'Category')?></label>
			<div class="col-sm-9">
				<?=$this->AdminForm->input('category_id')?>
			</div>
		</div>
		<div class="form-group">
			<label for="expiry" class=" col-sm-3 control-label"><?=__d('admin', 'Value')?></label>
			<div class="col-sm-9">
				<?=$this->AdminForm->input('value', array('type' => 'select', 'options' => $values))?>
			</div>
		</div>
		<div class="form-group">
			<label for="expiry" class=" col-sm-3 control-label"><?=__d('admin', 'Membership Targeting')?></label>
			<div class="col-sm-6">
				<?=
					$this->AdminForm->input('TargettedMemberships', array(
						'type' => 'select',
						'class' => 'fancy form-control',
						'multiple' => 'multiple',
						'options' => $memberships,
					))
				?>
			</div>
			<div class="col-sm-2">
				<input class="btn btn-default" type="button" value="<?=__d('admin', 'Select All')?>" style="vertical-align: top;" onclick="selectAll('TargettedMembershipsTargettedMemberships')">
			</div>
		</div>
		<?php if(Module::active('AccurateLocationDatabase')): ?>
			<?=$this->Locations->selector($countries)?>
		<?php else: ?>
			<div class="form-group">
				<label for="expiry" class=" col-sm-3 control-label"><?=__d('admin', 'Location Targeting')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input('TargettedLocations', array(
							'type' => 'select',
							'class' => 'fancy form-control',
							'multiple' => 'multiple',
							'options' => $countries,
							'selected' => $selectedCountries,
						))
					?>
				</div>
				<div class="col-sm-2">
					<input class="btn btn-default" type="button" value="<?=__d('admin', 'Select All')?>" style="vertical-align: top;" onclick="selectAll('PaidOfferTargettedLocations')">
				</div>
			</div>
		<?php endif; ?>
		<div class="text-center">
			<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
