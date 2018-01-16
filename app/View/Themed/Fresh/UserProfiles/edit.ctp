<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Your Profile')?></h2>
			<?=$this->UserForm->create('UserProfile', array ('class' => 'uk-form-horizontal'))?>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Gender:')?></label>
				<div class="uk-form-controls">
					<?=$this->UserForm->input('gender', array(
						'type' => 'select', 
						'options' => array(null => __('Please select...'), 'Male' => __('Male'), 'Female' => __('Female')),
						'class' => 'uk-select',
						));?>
				</div>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Date Of Birth:')?></label>
				<div class="uk-form-controls">
					<?=$this->UserForm->input('birth_day', array(
						'dateformat' => 'DMY',
						'timepicker' => false,
						'minYear' => date('Y') - 120,
						'maxYear' => date('Y') - 18,
						'class' => 'uk-input'
						))?>
				</div>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('E-mail:')?></label>
				<div class="uk-form-controls">
					<?=$this->UserForm->input('UserMetadata.next_email', array(
						'class' => 'uk-input'
						))?>
					<?php if($nextEmail): ?>
					<div class="uk-text-left uk-text-small">
						<?=__('%s is waiting for verification. To resend verification e-mail please click %s.', h($nextEmail), $this->Html->link(__('here'), array('action' => 'resend', $this->request->data['UserProfile']['user_id'])))?>
					</div>
					<?php endif;?>
				</div>
			</div>
			<?php foreach($gateways as $gateway => $name): ?>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Account Id For %s:', h($name))?></label>
				<div class="uk-form-controls">
					<?=$this->UserForm->input($gateway, array(
						'class' => 'uk-input'
						))?>
				</div>
			</div>
			<?php endforeach; ?>
			<?php if(Configure::read('Forum.active')): ?>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Forum Avatar:')?></label>
				<div class="uk-form-controls">
					<?=$this->UserForm->input('User.avatar', array(
						'class' => 'uk-input',
						))?>
				</div>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Forum Signature:')?></label>
				<div class="uk-form-controls">
					<?=$this->UserForm->input('User.signature', array(
						'class' => 'uk-textarea',
						'type' => 'textarea',
						))?>
				</div>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Forum Statistics:')?></label>
				<div class="uk-form-controls">
					<?=$this->UserForm->input('User.forum_statistics', array('class' => 'uk-checkbox'))?>
				</div>
			</div>
			<?php endif; ?>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Allow Notification E-mails:')?></label>
				<div class="uk-form-controls">
					<?=$this->UserForm->input('User.allow_emails', array('class' => 'uk-checkbox'))?>
				</div>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('New Password:')?></label>
				<div class="uk-form-controls">
					<?=$this->UserForm->input('User.password', array(
						'class' => 'uk-input',
						))?>
				</div>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Confirm New Password:')?></label>
				<div class="uk-form-controls">
					<?=$this->UserForm->input('User.confirm_password', array(
						'type' => 'password',
						'class' => 'uk-input',
						))?>
				</div>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Current Password:')?></label>
				<div class="uk-form-controls">
					<?=$this->UserForm->input('password_check', array(
						'type' => 'password',
						'class' => 'uk-input',
						'required' => true,
						))?>
				</div>
			</div>
			<?php if($googleAuthenticator): ?>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Google Authenticator Code:')?></label>
					<div class="uk-form-controls">
						<?=$this->UserForm->input('UserSecret.ga_code', array(
							'type' => 'password',
							'class' => 'uk-input',
							'required' => true,
						))?>
					</div>
				</div>
			<?php endif; ?>
			<div class="uk-margin uk-text-right">
				<button class="uk-button uk-button-primary"><?=__('Save')?></button>
			</div>
			<?=$this->UserForm->input('user_id');?>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
