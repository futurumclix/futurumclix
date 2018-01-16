<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Manually Add User')?></h2>
	</div>
	<?=$this->AdminForm->create('User', array('class' => 'form-horizontal'))?>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'First Name')?></label>
			<?=$this->AdminForm->input('first_name')?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'Last Name')?></label>
			<?=$this->AdminForm->input('last_name')?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'Username')?></label>
			<?=$this->AdminForm->input('username')?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'E-mail Address')?></label>
			<?=$this->AdminForm->input('email')?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'Password')?></label>
			<?=$this->AdminForm->input('password')?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'Confirm Password')?></label>
			<?=$this->AdminForm->input('confirm_password')?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'Location')?></label>
			<?=$this->AdminForm->input('location')?>
		</div>
		<div class="text-center col-md-12 paddingten">
			<button class="btn btn-primary btn-sm"><?=_('Add User')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
