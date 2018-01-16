<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Add User Items')?></h2>
	</div>
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('admin', 'Username')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('username')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('admin', 'Choose item')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('package')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('admin', 'Quantity')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('quantity')?>
			</div>
		</div>
		<div class="text-center col-sm-12 paddingten">
			<button class="btn btn-primary"><?=__d('admin', 'Add item')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
