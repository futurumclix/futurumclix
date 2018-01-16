<?=$this->element('userSidebar')?>
<div class="page">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 padding30-sides">
			<?=$this->element('userBreadcrumbs')?>
			<?=$this->Notice->show()?>
			<div class="panel">
				<div class="padding30-col">
					<div class="col-md-12">
						<h5><?=__('Please choose method to secure your account')?></h5>
					</div>
					<div class="col-md-12 margin30-top">
						<?=$this->UserForm->create('UserSecret')?>
						<fieldset class="form-group">
							<label class="col-sm-3 control-label"><?=__('Please choose:')?></label>
							<div class="col-sm-9">
								<?=$this->UserForm->input('mode', array(
									'class' => 'form-control',
									'options' => array(
										UserSecret::MODE_NONE => __('None'),
										UserSecret::MODE_GA => __('Google Authenticator'),
									),
								))?>
							</div>
						</fieldset>
						<div id="ga">
							<fieldset class="form-group">
								<label class="col-sm-3 control-label"><?=__('Your GA Code:')?></label>
								<div class="col-sm-9">
									<?=$this->UserForm->input('ga_secret', array('type' => 'hidden'))?>
									<?php
										echo $this->GoogleAuthenticator->getQRCode($this->request->data['UserSecret']['ga_secret']);
									?>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<label class="col-sm-3 control-label"><?=__('Your Secret Code:')?></label>
								<div id="ga" class="col-sm-9">
									<?=$this->UserForm->input('ga_secret', array('type' => 'hidden'))?>
									<?php
										echo h($this->request->data['UserSecret']['ga_secret']);
									?>
								</div>
							</fieldset>
						</div>
						<div class="col-md-12 text-xs-right">
							<button class="btn btn-primary"><?=__('Save')?></button>
						</div>
						<?=$this->UserForm->end()?>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->Js->buffer("
	function changeMode() {
		if($('#UserSecretMode').val() == ".UserSecret::MODE_GA.") {
			$('#ga').show();
		} else {
			$('#ga').hide();
		}
	}
	$('#UserSecretMode').change(function() {
		changeMode();
	});
	changeMode();
	")
	?>

