<h5 class="text-xs-center"><?=__d('installer', 'Awesome sauce, we have connected to the database.')?></h5>
<p><?=__d('installer', 'We are connected to your database so all you need to do now is just approve further installation by clicking button below.')?></p>
<?=$this->AdminForm->create(false)?>
	<div class="text-xs-right">
		<button class="btn btn-primary"><?=__d('installer', 'Begin installation')?></button>
	</div>
<?=$this->AdminForm->end()?>
