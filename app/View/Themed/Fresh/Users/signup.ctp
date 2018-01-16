<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('Register at')?> <?=h(Configure::read('siteTitle'))?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<?=$this->Notice->show()?>
			<h2 class="uk-text-center"><?=__('Register at')?> <?=h(Configure::read('siteTitle'))?></h2>
			<?=$this->UserForm->create('User', array('class' => 'uk-text-center'))?>
			<div class="uk-margin">
				<?=
					$this->UserForm->input('username', array(
						'type' => 'text',
						'class' => 'uk-input uk-form-width-large',
						'placeholder' => __('Enter your username'),
						'uk-tooltip' => 'pos:\'top\'',
						'title' => __('Please put your username (nickname) which you gonna use for logging in and in forum section. Make sure to use only alphanumerical symbols.'),
					))
					?>
			</div>
			<div class="uk-margin">
				<?=
					$this->UserForm->input('password', array(
						'type' => 'password',
						'class' => 'uk-input uk-form-width-large',
						'placeholder' => __('Enter your password'),
						'uk-tooltip' => 'pos:\'top\'',
						'title' => __('Please put your password. Make sure it differs from the other sites you use and make it as much complicated as it possible. Make sure no one else will know your password for security purposes.'),
					))
					?>
			</div>
			<div class="uk-margin">
				<?=
					$this->UserForm->input('confirm_password', array(
						'type' => 'password',
						'class' => 'uk-input uk-form-width-large',
						'placeholder' => __('Confirm password'),
						'uk-tooltip' => 'pos:\'top\'',
						'title' => __('Please confirm your password.'),
					))
					?>
			</div>
			<div class="uk-margin">
				<?=
					$this->UserForm->input('email', array(
						'type' => 'text',
						'class' => 'uk-input uk-form-width-large',
						'placeholder' => __('Enter your email address'),
						'uk-tooltip' => 'pos:\'top\'',
						'title' => __('Please put your real email address so we can send you confirmation link needed to finish sign up process. We will not share this email with anyone, we will use it only to keep you updated about site news and to recover your password in case you will forget it.'),
					))
					?>
			</div>
			<div class="uk-margin">
				<?=
					$this->UserForm->input('first_name', array(
						'type' => 'text',
						'class' => 'uk-input uk-form-width-large',
						'placeholder' => __('Enter your first name'),
						'uk-tooltip' => 'pos:\'top\'',
						'title' => __('Please put your real first name. It is needed for payment purposes.'),
					))
					?>
			</div>
			<div class="uk-margin">
				<?=
					$this->UserForm->input('last_name', array(
						'type' => 'text',
						'class' => 'uk-input uk-form-width-large',
						'placeholder' => __('Enter your last name'),
						'uk-tooltip' => 'pos:\'top\'',
						'title' => __('Please put your real last name. It is needed for payment purposes.'),
					))
					?>
			</div>
			<?php if(isset($uplineName)): ?>
			<div class="uk-margin">
				<?=
					$this->UserForm->input('Upline', array(
						'readonly' => 'readonly', 
						'default' => $uplineName,
						'class' => 'uk-input uk-form-width-large',
						'uk-tooltip' => 'pos:\'top\'',
						'title' => __('This is the nickname of person which referred you to FuturumClix. If you do not have any referrer just leave this field blank.'),
					))
					?>
			</div>
			<?php endif; ?>
			<div class="uk-margin">
				<?=$this->UserForm->input('acceptTos', array('type' => 'checkbox', 'label' => __('I Understand And Agree With %s', $this->Html->link(__('Terms of Service'), array('controller' => 'pages', 'action' => 'content', 'tos')))));?>
			</div>
			<div class="uk-margin">
				<button type="submit" class="uk-button uk-button-primary"><?=__('Register')?></button>
				<?php
					if(Module::active('FacebookLogin')) {echo $this->FacebookLogin->link('<i class="mdi mdi-facebook"></i>'.__d('facebook_login', 'Register With Facebook'), array('escape' => false, 'class' => 'uk-button uk-button-primary'));}
					?>	
			</div>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
