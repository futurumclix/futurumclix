<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Add new explorer ad:')?></h2>
	</div>
	<?=$this->AdminForm->create('ExplorerAd', array(
		'class' => 'form-horizontal'
	))?>
	<div class="form-group">
		<label class="col-sm-3 control-label"><?=__d('admin', 'Advertiser')?></label>
		<div class="col-sm-9">
				<?=
					$this->AdminForm->input('Advertiser.username', array(
						'required' => false,
						'data-toggle' => 'tooltip',
						'data-placement' => 'top',
						'title' => __d('admin', 'Leave blank for self sponsored advertisement (admin\'s ad)'),
					))
				?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"><?=__d('admin', 'Title')?></label>
		<div class="col-sm-9">
			<?=$this->AdminForm->input('title')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"><?=__d('admin', 'Description')?></label>
		<div class="col-sm-9">
			<?=$this->AdminForm->input('description')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"><?=__d('admin', 'URL')?></label>
		<div class="col-sm-9">
			<?=$this->AdminForm->input('url')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"><?=__d('admin', 'White Label Traffic')?></label>
		<div class="col-sm-9">
			<?=$this->AdminForm->input('hide_referer')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"><?=__d('admin', 'SubPages')?></label>
		<div class="col-sm-9">
			<?=
				$this->AdminForm->input('subpages', array(
					'min' => 1,
					'max' => $settings['maxSubpages'],
					'step' => 1,
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"><?=__d('admin', 'Expiry')?></label>
		<div class="col-sm-7" id='expiry'>
			<?=$this->AdminForm->input('expiry')?>
		</div>
		<div class="col-sm-7" id='expiryDate'>
			<?=$this->AdminForm->input('expiry_date')?>
		</div>
		<div class="col-sm-2">
			<?=$this->AdminForm->input('package_type', array('type' => 'select', 'options' => $packageTypes))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"><?=__d('admin', 'Membership Targeting')?></label>
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
			<input type="button" class="btn btn-default" style="vertical-align: top;" value="<?=__d('admin', 'Select All')?>" onclick="selectAll('TargettedMembershipsTargettedMemberships')"/>
		</div>
	</div>
	<?php if(Module::active('AccurateLocationDatabase')): ?>
		<?=$this->Locations->selector($countries)?>
	<?php else: ?>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('admin', 'Geo-targeting')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('TargettedLocations', array(
						'type' => 'select',
						'class' => 'fancy form-control',
						'multiple' => 'multiple',
						'options' => $countries,
						'selected' => @$this->request->data['TargettedLocations'],
					))
					?>
			</div>
			<div class="col-sm-2">
				<input type="button" class="btn btn-default" style="vertical-align: top;" value="<?=__d('admin', 'Select All')?>" onclick="selectAll('ExplorerAdTargettedLocations')"/>
			</div>
		</div>
	<?php endif; ?>
	<div class="text-center">
		<button class="btn btn-primary"><?=__d('admin', 'Add advertisement')?></button></td>
	</div>
</div>
<?=$this->AdminForm->end()?>
<?php $this->Js->buffer("
	function change_expiry() {
		if($('#ExplorerAdPackageType').val() == 'Days') {
			$('#expiry').hide();
			$('#expiryDate').show();
		} else {
			$('#expiry').show();
			$('#expiryDate').hide();
		}
	}
	change_expiry();
	$('#ExplorerAdPackageType').on('change', change_expiry);
");
