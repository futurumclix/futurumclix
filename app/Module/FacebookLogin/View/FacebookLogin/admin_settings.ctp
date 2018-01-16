<div class="col-md-12">
	<div class="title">
		<h2><?=__d('facebook_login_admin', 'Facebook Login settings')?></h2>
	</div>
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('facebook_login_admin', 'App ID')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('FacebookLoginSettings.facebookLogin.appID', array(
					'type' => 'text',
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('facebook_login_admin', 'App Secret')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('FacebookLoginSettings.facebookLogin.appSecret', array(
					'type' => 'text',
				))
			?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('facebook_login_admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
