<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('Login')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<?=$this->Notice->show()?>
			<h2 class="uk-text-center"><?=__('Login')?></h2>
			<?=$this->UserForm->create('User', array ('class' => 'uk-text-center'))?>
			<div class="uk-margin">
				<?=$this->UserForm->input('username', array('class' => 'uk-input uk-form-width-large', 'placeholder' => __('Enter your username')))?>
			</div>
			<div class="uk-margin">
				<?=$this->UserForm->input('password', array('class' => 'uk-input uk-form-width-large', 'placeholder' => __('Enter your password')))?>
			</div>
			<div class="uk-margin">
				<?=$this->Html->link(__('Reset Password'), array('action' => 'sendPasswordRequestEmail'), array('class' => 'uk-button uk-button-secondary'))?>
				<button class="uk-button uk-button-primary"><?=__('Login')?></button>
				<?php
					if(Module::active('FacebookLogin')) {echo $this->FacebookLogin->link('<i class="mdi mdi-facebook"></i> Login With Facebook', array('escape' => false, 'class' => 'uk-button uk-button-primary'));}
					?>	
			</div>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
