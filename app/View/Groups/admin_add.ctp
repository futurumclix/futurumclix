<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Add new group')?></h2>
	</div>
	<?=$this->AdminForm->create()?>
		<div class="form-group text-center">
			<div class="col-sm-12 paddingten"><?=__d('admin', 'Please Enter Group Name You Want To Add.')?></div>
			<div class="col-sm-12">
				<?=$this->AdminForm->input('alias', array(
					'style' => 'width: 50%; display: inline-block;',
				))?>
			</div>
		</div>
		<div class="text-center col-sm-12 paddingten">
			<button class="btn btn-primary"><?=__d('admin', 'Add Group')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
