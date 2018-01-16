<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Please choose method to secure your account')?></h2>
			<?=$this->UserForm->create('UserSecret', array ('class' => 'uk-form-horizontal'))?>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Please choose:')?></label>
				<div class="uk-form-controls">
					<?=$this->UserForm->input('mode', array(
						'class' => 'uk-select',
						'options' => array(
							UserSecret::MODE_NONE => __('None'),
							UserSecret::MODE_GA => __('Google Authenticator'),
						),
						))?>
				</div>
			</div>
			<div id="ga">
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Your GA Code:')?></label>
					<div class="uk-form-controls">
						<?=$this->UserForm->input('ga_secret', array('type' => 'hidden'))?>
						<?php
							echo $this->GoogleAuthenticator->getQRCode($this->request->data['UserSecret']['ga_secret']);
							?>
					</div>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Your Secret Code:')?></label>
					<div class="uk-form-controls">
						<?=$this->UserForm->input('ga_secret', array('type' => 'hidden'))?>
						<?php
							echo h($this->request->data['UserSecret']['ga_secret']);
							?>
					</div>
				</div>
			</div>
			<div class="uk-margin uk-text-right">
				<button class="uk-button uk-button-primary"><?=__('Save')?></button>
			</div>
			<?=$this->UserForm->end()?>
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

