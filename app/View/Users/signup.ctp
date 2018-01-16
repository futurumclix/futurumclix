<div class="container">
	<div class="row">
		<div class="col-md-12 front_text">
			<h2 class="text-xs-center"><?=__('Register at')?> <?=h(Configure::read('siteTitle'))?></h2>
			<?=$this->Notice->show()?>
				<?=$this->UserForm->create('User', array('class' => 'form-horizontal padding'))?>
				<div class="form-group col-sm-4">
					<label class="sr-only" for="UserUsername"><?=__('Username')?></label>
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
					<label class="sr-only" for="UserPassword"><?=__('Password')?></label>
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
					<label class="sr-only" for="UserConfirm_Password"><?=__('Confirm Password')?></label>
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
				<div class="form-group col-sm-4">
					<label class="sr-only" for="UserEmail"><?=__('E-mail Address')?></label>
					<?=
						$this->UserForm->input('email', array(
							'type' => 'text',
							'class' => 'form-control',
							'placeholder' => __('Enter your email address'),
							'data-toggle' => 'popover',
							'data-trigger' => 'focus',
							'title' => __('Enter your email address'),
							'data-content' => __('Please put your real email address so we can send you confirmation link needed to finish sign up process. We will not share this email with anyone, we will use it only to keep you updated about site news and to recover your password in case you will forget it.'),
						))
						?>
				</div>
				<div class="form-group col-sm-4">
					<label class="sr-only" for="UserFirst_Name"><?=__('First Name')?></label>
					<?=
						$this->UserForm->input('first_name', array(
							'type' => 'text',
							'class' => 'form-control',
							'placeholder' => __('Enter your first name'),
							'data-toggle' => 'popover',
							'data-trigger' => 'focus',
							'title' => __('Enter your first name'),
							'data-content' => __('Please put your real first name. It is needed for payment purposes.'),
						))
						?>
				</div>
				<div class="form-group col-sm-4">
					<label class="sr-only" for="UserLast_Name"><?=__('Last Name')?></label>
					<?=
						$this->UserForm->input('last_name', array(
							'type' => 'text',
							'class' => 'form-control',
							'placeholder' => __('Enter your last name'),
							'data-toggle' => 'popover',
							'data-trigger' => 'focus',
							'title' => __('Enter your last name'),
							'data-content' => __('Please put your real last name. It is needed for payment purposes.'),
						))
						?>
				</div>
					<?php if(isset($uplineName)): ?>
						<div class="form-group col-sm-4 col-md-offset-4">
							<label class="sr-only"><?=__('Referrer')?></label>
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
						<?=$this->UserForm->input('acceptTos', array('type' => 'checkbox', 'label' => __('I Understand And Agree With %s', $this->Html->link(__('Terms of Service'), array('controller' => 'pages', 'action' => 'content', 'tos')))));?>
					</label>
				</div>
				<div class="form-group text-xs-center">
					<button type="submit" class="btn btn-primary"><?=__('Register')?></button>
					<?php
						if(Module::active('FacebookLogin')) {echo $this->FacebookLogin->link('<i class="fa fa-facebook" aria-hidden="true"></i>'.__d('facebook_login', 'Register With Facebook'), array('escape' => false, 'class' => 'btn btn-facebook'));}
					?>	
				</div>
				<?=$this->UserForm->end()?>
			</div>
		</div>
	</div>
</div>
