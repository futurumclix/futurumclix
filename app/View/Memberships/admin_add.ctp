<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Add Memberships - please provide membership name')?></h2>
	</div>
	<?=$this->AdminForm->create()?>
		<div class="form-group text-center">
			<div class="col-sm-12 paddingten"><?=__d('admin', 'Please enter membership name. After you do so, you will be redirected to editing your new membership terms.')?></div>
			<div class="col-sm-12">
				<?=$this->AdminForm->input('name', array(
					'style' => 'width: 50%; display: inline-block;',
				))?>
			</div>
		</div>
		<div class="text-center col-sm-12 paddingten">
			<button class="btn btn-primary"><?=__d('admin', 'Add Membership')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
