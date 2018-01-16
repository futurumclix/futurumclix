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
							<h5><?=__('Your Profile')?></h5>
						</div>
						<div class="col-md-12 margin30-top">
							<?=$this->UserForm->create('UserProfile')?>
							<fieldset class="form-group">
								<label class="col-sm-3 control-label"><?=__('Gender:')?></label>
								<div class="col-sm-9">
									<?=$this->UserForm->input('gender', array(
										'type' => 'select', 
										'options' => array(null => __('Please select...'), 'Male' => __('Male'), 'Female' => __('Female')),
										'class' => 'form-control',
									));?>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<label class="col-sm-3 control-label"><?=__('Date Of Birth:')?></label>
								<div class="col-sm-9">
									<?=$this->UserForm->input('birth_day', array(
										'dateformat' => 'DMY',
										'timepicker' => false,
										'minYear' => date('Y') - 120,
										'maxYear' => date('Y') - 18,
										'class' => 'form-control'
									))?>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<label class="col-sm-3 control-label"><?=__('E-mail:')?></label>
								<div class="col-sm-9">
									<?=$this->UserForm->input('UserMetadata.next_email', array(
										'class' => 'form-control'
									))?>
									<?php if($nextEmail): ?>
									<div class="text-xs-left">
										<?=__('%s is waiting for verification. To resend verification e-mail please click %s.', h($nextEmail), $this->Html->link(__('here'), array('action' => 'resend', $this->request->data['UserProfile']['user_id'])))?>
									</div>
									<?php endif;?>
								</div>
							</fieldset>
							<?php foreach($gateways as $gateway => $name): ?>
							<fieldset class="form-group">
								<label class="col-sm-3 control-label"><?=__('Account Id For %s:', h($name))?></label>
								<div class="col-sm-9">
									<?=$this->UserForm->input($gateway, array(
										'class' => 'form-control'
									))?>
								</div>
							</fieldset>
							<?php endforeach; ?>
							<?php if(Configure::read('Forum.active')): ?>
								<fieldset class="form-group">
									<label class="col-sm-3 control-label"><?=__('Forum Avatar:')?></label>
									<div class="col-sm-9">
										<?=$this->UserForm->input('User.avatar', array(
											'class' => 'form-control',
										))?>
									</div>
								</fieldset>
								<fieldset class="form-group">
									<label class="col-sm-3 control-label"><?=__('Forum Signature:')?></label>
									<div class="col-sm-9">
										<?=$this->UserForm->input('User.signature', array(
											'class' => 'form-control',
											'type' => 'textarea',
										))?>
									</div>
								</fieldset>
								<fieldset class="form-group">
									<label class="col-sm-3 control-label"><?=__('Forum Statistics:')?></label>
									<div class="col-sm-9">
										<?=$this->UserForm->input('User.forum_statistics')?>
									</div>
								</fieldset>
							<?php endif; ?>
							<fieldset class="form-group">
								<label class="col-sm-3 control-label"><?=__('Allow Sending Notification E-mails:')?></label>
								<div class="col-sm-9">
									<?=$this->UserForm->input('User.allow_emails')?>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<label class="col-sm-3 control-label"><?=__('New Password:')?></label>
								<div class="col-sm-9">
									<?=$this->UserForm->input('User.password', array(
										'class' => 'form-control',
									))?>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<label class="col-sm-3 control-label"><?=__('Confirm New Password:')?></label>
								<div class="col-sm-9">
									<?=$this->UserForm->input('User.confirm_password', array(
										'type' => 'password',
										'class' => 'form-control',
									))?>
								</div>
							</fieldset>
							<fieldset class="form-group">
								<label class="col-sm-3 control-label"><?=__('Current Password:')?></label>
								<div class="col-sm-9">
									<?=$this->UserForm->input('password_check', array(
										'type' => 'password',
										'class' => 'form-control',
										'required' => true,
									))?>
								</div>
							</fieldset>
							<?php if($googleAuthenticator): ?>
								<fieldset class="form-group">
									<label class="col-sm-3 control-label"><?=__('Google Authenticator Code:')?></label>
									<div class="col-sm-9">
										<?=$this->UserForm->input('UserSecret.ga_code', array(
											'type' => 'password',
											'class' => 'form-control',
											'required' => true,
										))?>
									</div>
								</fieldset>
							<?php endif; ?>
							<div class="col-md-12 text-xs-right">
								<button class="btn btn-primary"><?=__('Save')?></button>
							</div>
							<?=$this->UserForm->input('user_id');?>
							<?=$this->UserForm->end()?>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
