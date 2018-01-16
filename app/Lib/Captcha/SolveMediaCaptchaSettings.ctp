<div id="solvemedia" class="tab-pane fade in">
	<div class="title2">
		<h2><?=__d('admin', 'SolveMedia Settings')?></h2>
	</div>
	<?=
		$this->AdminForm->create('Settings', array(
			'url' => array('#' => 'solvemedia'),
			'class' => 'form-horizontal',
		))
	?>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Verification Key')?></label>
		<div class="col-sm-8">
			<?=$this->AdminForm->input('Settings.SolveMedia.verificationKey')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Authentication Key')?></label>
		<div class="col-sm-8">
			<?=$this->AdminForm->input('Settings.SolveMedia.authenticationKey')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Challenge Key')?></label>
		<div class="col-sm-8">
			<?=$this->AdminForm->input('Settings.SolveMedia.challengeKey')?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button></td>
	</div>
	<?=$this->AdminForm->end()?>
</div>
