<div id="MinuteStaff" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('url' => array('#' => 'MinuteStaff'), 'class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('Offerwall.MinuteStaff.name', array('value' => 'MinuteStaff'))?>
	<div class="title2">
		<h2><?=__d('offerwalls_admin', '%s settings', 'MinuteStaff')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'App ID')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Offerwall.MinuteStaff.api_settings.app_id', array(
					'type' => 'text',
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('offerwalls_admin', '')
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'Site Code')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Offerwall.MinuteStaff.api_settings.site_code', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('offerwalls_admin', '')
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'Site Type')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Offerwall.MinuteStaff.api_settings.site_type', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('offerwalls_admin', ''),
					'options' => array(
						'all' => 'all',
						'instant' => 'instant',
						'20seconds' => '20seconds',
						'click' => 'click',
						'clicktarget' => 'clicktarget',
						'double' => 'double',
						'webmail' => 'webmail',
						'search' => 'search',
						'action' => 'action',
						'survey' => 'survey',
						'live' => 'live',
					),
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'Notify code')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Offerwall.MinuteStaff.api_settings.notify_code', array(
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
				$this->AdminForm->input('Offerwall.MinuteStaff.api_settings.width', array(
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
				$this->AdminForm->input('Offerwall.MinuteStaff.api_settings.height', array(
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
				$this->AdminForm->input('Offerwall.MinuteStaff.allowed_ips', array(
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
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'plugin' => 'offerwalls', 'controller' => 'offerwalls', 'action' => 'offerCallback', 'MinuteStaff'), true)?>">
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('offerwalls_admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
