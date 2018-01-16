<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Edit Group:')?> <?=h($this->request->data['RequestObject']['alias'])?></h2>
	</div>
	<div>
		<?=
			$this->AdminForm->create('RequestObject', array(
				'class' => 'form-horizontal',
			))
		?>
		<?=$this->AdminForm->input('id')?>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=__d('admin', 'Alias')?></label>
			<div class="col-sm-10">
				<?=$this->AdminForm->input('alias')?>
			</div>
		</div>
		<div class="text-center col-sm-12 paddingten">
			<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
		</div>
		<?=$this->AdminForm->end()?>
	</div>
</div>
