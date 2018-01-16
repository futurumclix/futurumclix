<div class="container">
	<div class="row">
		<div class="col-md-12 front_text">
			<h2 class="text-xs-center"><?=__('Login')?></h2>
			<?=$this->Notice->show()?>
			<div class="col-md-6 col-md-offset-3">
				<?=$this->UserForm->create('User')?>
				<div class="form-group">
					<label for="UserUsername"><?=__('Username')?></label>
					<?=$this->UserForm->input('username', array('class' => 'form-control', 'placeholder' => __('Enter your username')))?>
				</div>
				<div class="form-group">
					<label for="UserPassword"><?=__('Password')?></label>
					<?=$this->UserForm->input('password', array('class' => 'form-control', 'placeholder' => __('Enter your password')))?>
				</div>
				<div class="form-group text-xs-center">
					<?=$this->Html->link(__('Reset Password'), array('action' => 'sendPasswordRequestEmail'), array('class' => 'btn btn-secondary'))?>
					<button class="btn btn-primary text-center"><?=__('Login')?></button>
					<?php
						if(Module::active('FacebookLogin')) {echo $this->FacebookLogin->link('<i class="fa fa-facebook" aria-hidden="true"></i> Login With Facebook', array('escape' => false, 'class' => 'btn btn-facebook'));}
					?>	
				</div>
				<?=$this->UserForm->end()?>
			</div>
		</div>
	</div>
</div>
