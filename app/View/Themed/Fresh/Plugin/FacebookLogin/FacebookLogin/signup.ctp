<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__d('facebook_login', 'Register at %s', Configure::read('siteName'))?></li>
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
				<p><?=__d('facebook_login', 'To finish your registration via Facebook you just need to provide us your username and password so you will be able to change your personal settings in the future and recover your password in case you will loose it.')?></p>
			</div>
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
			</div>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
