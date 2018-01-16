<div class="row">
	<div class="col-md-4 col-md-offset-4 login-box text-center">
		<?=$this->Html->image('admin/logo2.png', array('class' => 'center', 'alt' => 'FuturumClix Logo'))?>
		<?=$this->AdminForm->create('Admin')?>
		<?=$this->Notice->show()?>
		<div class="input-group">
			<span class="input-group-addon"><i class="fa fa-user"></i></span>
			<?=$this->AdminForm->input('email', array('class' => 'form-control', 'label' => '', 'placeholder' => __d('admin', 'E-mail Address')))?>
		</div>
		<div class="input-group">
			<span class="input-group-addon lock"><i class="fa fa-lock"></i></span>
			<?=$this->AdminForm->input('password', array('class' => 'form-control', 'label' => '', 'placeholder' => __d('admin', 'Password')))?>
		</div>
		<button class="btn btn-primary"><i class="fa fa-check-square"></i>&nbsp;&nbsp;<?=__d('admin', 'Login')?></button>
		<?=$this->AdminForm->end()?>
	</div>
</div>
</div>
