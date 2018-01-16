<div class="col-md-12">
	<div class="title">
		<h2><?=__d('forum_admin', 'Add New Forum')?></h2>
	</div>
	<?=$this->AdminForm->create()?>
		<div class="form-group text-center">
			<div class="col-sm-12 paddingten"><?=__d('forum_admin', 'Please enter forum name. After you do so, you will be redirected to editing your new forum terms.')?></div>
			<div class="col-sm-12">
				<?=$this->AdminForm->input('title', array(
					'style' => 'width: 50%; display: inline-block;',
				))?>
			</div>
		</div>
		<div class="text-center col-sm-12 paddingten">
			<button class="btn btn-primary"><?=__d('forum_admin', 'Add Forum')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
