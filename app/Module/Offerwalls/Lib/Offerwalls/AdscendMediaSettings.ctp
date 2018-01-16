<div id="AdscendMedia" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('url' => array('#' => 'AdscendMedia'), 'class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('Offerwall.AdscendMedia.name', array('value' => 'AdscendMedia'))?>
	<div class="title2">
		<h2><?=__d('offerwalls_admin', '%s settings', 'AdscendMedia')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'Application ID')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Offerwall.AdscendMedia.api_settings.applicationid', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('offerwalls_admin', '')
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'Offerwall ID')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Offerwall.AdscendMedia.api_settings.oferwallid', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('offerwalls_admin', '')
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'Postback Key')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Offerwall.AdscendMedia.api_settings.secret_key', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('offerwalls_admin', '')
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'Postback URL Template')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" id="postback" readonly value="<?=Router::url(array('admin' => false, 'plugin' => 'offerwalls', 'controller' => 'offerwalls', 'action' => 'offerCallback', 'AdscendMedia'), true).'?hash=[HSH='.$this->request->data['Offerwall']['AdscendMedia']['api_settings']['secret_key'].']userId=[SB1]&credits=[CUR]&transactionId=[TID][/HSH]&userId=[SB1]&credits=[CUR]&transactionId=[TID]'?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('offerwalls_admin', 'Width')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Offerwall.AdscendMedia.api_settings.width', array(
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
				$this->AdminForm->input('Offerwall.AdscendMedia.api_settings.height', array(
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
				$this->AdminForm->input('Offerwall.AdscendMedia.allowed_ips', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('offerwalls_admin', '')
				))
			?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('offerwalls_admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
<?php 
$url = Router::url(array('admin' => false, 'plugin' => 'offerwalls', 'controller' => 'offerwalls', 'action' => 'offerCallback', 'AdscendMedia'), true).'?hash=[HSH=%secret%]userId=[SB1]&credits=[CUR]&transactionId=[TID][/HSH]&userId=[SB1]&credits=[CUR]&transactionId=[TID]';
$this->Js->buffer("
	$('#OfferwallAdscendMediaApiSettingsSecretKey').on('change input', function() {
		$('#postback').val('$url'.replace('%secret%', $(this).val()));
	});
");
