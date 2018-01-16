<div class="container">
	<div class="row">
		<div class="col-md-12 front_text">
			<h2 class="text-xs-center"><?=__('Please enter Google Authenticator code')?></h2>
			<?=$this->Notice->show()?>
			<div class="col-md-6 col-md-offset-3">
				<?=$this->UserForm->create(false)?>
				<div class="form-group">
					<?=$this->UserForm->input('ga_code', array('class' => 'form-control', 'placeholder' => __('Enter your Google Authenticator code')))?>
				</div>
				<div class="form-group text-xs-center">
					<?=$this->UserForm->submit('Log in', array('class' => 'btn btn-primary'))?>
					<?=$this->UserForm->end()?>
				</div>
			</div>
		</div>
	</div>
</div>
