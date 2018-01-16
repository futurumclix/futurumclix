<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Captcha Settings')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#settings"><?=__d('admin', 'Captcha Settings')?></a></li>
		<?php foreach($available as $name): $href = strtolower($name); ?>
			<li><a data-toggle="tab" href="#<?=h($href)?>"><?=h($name)?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="tab-content">
		<div id="settings" class="tab-pane fade in active">
			<?=$this->AdminForm->create('Settings', array('class' => 'form-horizontal'))?>
			<div class="title2">
				<h2><?=__d('admin', 'Captcha Settings')?></h2>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Please Choose Which Captcha System Do You Want To Use On Your Site')?></label>
				<div class="col-sm-4">
					<?php $anyAvailable = false; foreach($available as $captcha): ?>
						<div class="checkbox">
						<?php if(empty($this->request->data['Settings'][$captcha])): ?>
							<label data-toggle="tooltip" data-placement="right" title="<?=__d('admin', 'You need to fill in captcha settings to be able to enable it.')?>">
						<?php else: $anyAvailable = true;?>
							<label>
						<?php endif; ?>
								<?=
									$this->AdminForm->checkbox('captchaType', array(
										'value' => $captcha,
										'hiddenField' => false,
										'disabled' => empty($this->request->data['Settings'][$captcha]),
										'class' => 'radioCheckbox',
									))
								?>
								<?=h($captcha)?>
							</label>
						</div>
					<?php endforeach; ?>
					<div class="checkbox">
						<label>
							<?=
								$this->AdminForm->checkbox('captchaType', array(
									'value' => 'disabled',
									'hiddenField' => false,
									'class' => 'radioCheckbox',
								))
							?>
							<?=__d('admin', 'None')?>
						</label>
					</div>
				</div>
			</div>
			<div class="title2">
				<h2><?=__d('admin', 'Page Selection')?></h2>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Please Choose On Which Pages Do You Want To Use Captcha System')?></label>
				<div class="col-sm-4">
					<div class="checkbox">
						<label>
						<?=$this->AdminForm->checkbox('captchaOnLogin', array('disabled' => !$anyAvailable));?>
						<?=__d('admin', 'Login Page')?>
						</label>
					</div>
					<div class="checkbox">
						<label>
						<?=$this->AdminForm->checkbox('captchaOnRegistration', array('disabled' => !$anyAvailable));?>
						<?=__d('admin', 'Registration Page')?>
						</label>
					</div>
					<div class="checkbox">
						<label>
						<?=$this->AdminForm->checkbox('captchaOnSupport', array('disabled' => !$anyAvailable));?>
						<?=__d('admin', 'Support Page')?>
						</label>
					</div>
					<div class="checkbox">
						<label>
						<?=$this->AdminForm->checkbox('captchaOnAdvertise', array('disabled' => !$anyAvailable));?>
						<?=__d('admin', 'Buying advertisements')?>
						</label>
					</div>
				</div>
			</div>
			<div class="title2">
				<h2><?=__d('admin', 'Clicking Captcha')?></h2>
			</div>
			<div class="form-group">
				<label class="col-sm-8 control-label"><?=__d('admin', 'Please Choose Which Captcha You Want To Use For Watching Advertisements')?></label>
				<div class="col-sm-4">
					<?php foreach($available as $captcha): if($captcha == 'SolveMedia') continue;?>
						<div class="checkbox">
						<?php if(empty($this->request->data['Settings'][$captcha])): ?>
							<label data-toggle="tooltip" data-placement="right" title="<?=__d('admin', 'You need to fill in captcha settings to be able to enable it.')?>">
						<?php else: ?>
							<label>
						<?php endif; ?>
								<?=
									$this->AdminForm->checkbox('captchaTypeSurfer', array(
										'value' => $captcha,
										'hiddenField' => false,
										'disabled' => empty($this->request->data['Settings'][$captcha]),
										'class' => 'radioCheckbox',
									))
								?>
								<?=h($captcha)?>
							</label>
						</div>
					<?php endforeach; ?>
						<div class="checkbox">
							<label>
								<?=
									$this->AdminForm->checkbox('captchaTypeSurfer', array(
										'value' => 'disabled',
										'hiddenField' => false,
										'class' => 'radioCheckbox',
									))
								?>
								<?=__d('admin', 'None')?>
							</label>
						</div>
				</div>
			</div>
			<div class="col-md-12 text-center paddingten">
				<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
			</div>
			<?=$this->AdminForm->end()?>
		</div>
		<?php
			foreach($available as $name) {
				include(APP.DS.'Lib'.DS.'Captcha'.DS.$name.'CaptchaSettings.ctp');
			}
		?>
	</div>
</div>
<?php
	$this->Js->buffer("
		checkboxesAsRadio();
		jumpToTabByAnchor();
	");
?>
