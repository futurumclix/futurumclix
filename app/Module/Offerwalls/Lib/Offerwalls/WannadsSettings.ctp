<div id="Wannads" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('url' => array('#' => 'Wannads'), 'class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('Offerwall.Wannads.name', array('value' => 'Wannads'))?>
	<div class="title2">
		<h2><?=__d('offerwalls_admin', '%s settings', 'Wannads')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'API Key')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Offerwall.Wannads.api_settings.api_key', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('offerwalls_admin', '')
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'Secret')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Offerwall.Wannads.api_settings.secret_key', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('offerwalls_admin', '')
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'Width')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Offerwall.Wannads.api_settings.width', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('offerwalls_admin', '')
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'Height')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Offerwall.Wannads.api_settings.height', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('offerwalls_admin', '')
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'Postback Allowed IP')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Offerwall.Wannads.allowed_ips', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('offerwalls_admin', '')
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'Postback')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'plugin' => 'offerwalls', 'controller' => 'offerwalls', 'action' => 'offerCallback', 'Wannads'), true)?>">
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('offerwalls_admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
