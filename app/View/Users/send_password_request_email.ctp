<div class="container">
	<div class="row">
		<div class="col-md-12 front_text">
			<h2 class="text-xs-center"><?=__('Request For A New Password')?></h2>
			<?=$this->Notice->show()?>
			<div class="col-md-6 col-md-offset-3">
				<?=$this->Session->flash('user_action')?>
				<?=$this->UserForm->create('User')?>
				<div class="form-group padding30">
					<?=$this->UserForm->input('email', ['class' => 'form-control', 'placeholder' => __('Enter Your E-mail Address')])?>
				</div>
				<div class="form-group text-xs-center">
					<button class="btn btn-primary">Resend Password</button>
				</div>
				<?=$this->UserForm->end(__(''))?>
			</div>
		</div>
	</div>
</div>
