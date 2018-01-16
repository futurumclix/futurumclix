<div class="container">
	<div class="row">
		<div class="col-md-12 front_text">
			<h2 class="text-xs-center"><?=__d('facebook_login', 'Register at %s', Configure::read('siteName'))?></h2>
			<?=$this->Notice->show()?>
			<p><?=__d('facebook_login', 'To finish your registration via Facebook you just need to provide us your username and password so you will be able to change your personal settings in the future and recover your password in case you will loose it.')?></p>
			<?=$this->UserForm->create('User', array('class' => 'form-horizontal padding'))?>
			<div class="form-group col-sm-4">
				<label class="sr-only" for="UserUsername"><?=__d('facebook_login', 'Username')?></label>
				<?=
					$this->UserForm->input('username', array(
						'type' => 'text',
						'class' => 'form-control',
						'placeholder' => __('Enter your username'),
						'data-toggle' => 'popover',
						'data-trigger' => 'focus',
						'title' => __('Enter your username'),
						'data-content' => __('Please put your username (nickname) which you gonna use for logging in and in forum section. Make sure to use only alphanumerical symbols.'),
					))
					?>
			</div>
			<div class="form-group col-sm-4">
				<label class="sr-only" for="UserPassword"><?=__d('facebook_login', 'Password')?></label>
				<?=
					$this->UserForm->input('password', array(
						'type' => 'password',
						'class' => 'form-control',
						'placeholder' => __('Enter your password'),
						'data-toggle' => 'popover',
						'data-trigger' => 'focus',
						'title' => __('Enter your password'),
						'data-content' => __('Please put your password. Make sure it differs from the other sites you use and make it as much complicated as it possible. Make sure no one else will know your password for security purposes.'),
					))
					?>
			</div>
			<div class="form-group col-sm-4">
				<label class="sr-only" for="UserConfirm_Password"><?=__d('facebook_login', 'Confirm password')?></label>
				<?=
					$this->UserForm->input('confirm_password', array(
						'type' => 'password',
						'class' => 'form-control',
						'placeholder' => __('Confirm password'),
						'data-toggle' => 'popover',
						'data-trigger' => 'focus',
						'title' => __('Confirm password'),
						'data-content' => __('Please confirm your password.'),
					))
					?>
			</div>
			<?php if(isset($uplineName)): ?>
			<div class="form-group col-sm-4 col-md-offset-4">
				<label class="sr-only"><?=__d('facebook_login', 'Referrer')?></label>
				<?=
					$this->UserForm->input('Upline', array(
						'readonly' => 'readonly', 
						'default' => $uplineName,
						'class' => 'form-control',
						'data-toggle' => 'popover',
						'data-trigger' => 'focus',
						'title' => __('Person which referred you'),
						'data-content' => __('This is the nickname of person which referred you to FuturumClix. If you do not have any referrer just leave this field blank.'),
					))
					?>
			</div>
			<?php endif; ?>
			<div class="clearfix"></div>
			<div class="checkbox text-xs-center">
				<label>
				<?=$this->UserForm->input('acceptTos', array('type' => 'checkbox', 'label' => __d('facebook_login', 'I understand and agree with %s', $this->Html->link(__d('facebook_login', 'Terms of Service'), array('controller' => 'pages', 'action' => 'content', 'tos')))));?>
				</label>
			</div>
			<div class="form-group text-xs-center">
				<button href="#" type="submit" class="btn btn-primary"><?=__d('facebook_login', 'Register')?></button>
			</div>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
