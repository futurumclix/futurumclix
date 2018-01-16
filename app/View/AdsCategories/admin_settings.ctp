<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'PTC Ads Settings')?></h2>
	</div>
	<div class="tab-content">
		<?=
			$this->AdminForm->create('Settings', array(
				'class' => 'form-horizontal',
			))
		?>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Ad Title Length')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('PTCTitleLength', array(
							'type' => 'number',
							'min' => 0,
							'step' => 1,
							'max' => 128,
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Ad Description Length')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('PTCDescLength', array(
							'type' => 'number',
							'min' => 0,
							'step' => 1,
							'max' => 1024,
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Auto Approve Ads')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('PTCAutoApprove', array(
							'type' => 'checkbox',
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Check Connection Before New Ad Preview')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('PTCCheckConnection', array(
							'type' => 'checkbox',
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Check Connection Timeout')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('PTCCheckConnectionTimeout', array(
							'type' => 'number',
							'min' => 0,
							'step' => 1,
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Preview Time')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('PTCPreviewTime', array(
							'type' => 'number',
							'min' => 0,
							'step' => 1,
						))
					?>
				</div>
			</div>
			<div class="text-center col-sm-12 paddingten">
				<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
			</div>
		<?=$this->AdminForm->end()?>
	</div>
</div>
