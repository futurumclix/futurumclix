<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'View User Items')?></h2>
	</div>
		<?=$this->AdminForm->create('User')?>
			<div class="form-group text-center">
				<div class="col-sm-12 paddingten"><?=__d('admin', 'Please enter username to show this user items.')?></div>
				<div class="col-sm-12">
					<?=
						$this->AdminForm->input('username', array(
							'style' => 'width: 50%; display: inline-block;',
						))
					?>
				</div>
			</div>
			<div class="text-center col-sm-12 paddingten">
				<button class="btn btn-primary"><?=__d('admin', 'Show items')?></button>
			</div>
		<?=$this->AdminForm->end()?>
</div>
