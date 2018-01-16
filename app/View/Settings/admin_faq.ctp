<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'FAQ - Edit Page Content')?></h2>
	</div>
	<?=
		$this->AdminForm->create(false, array(
			'class' => 'form-horizontal',
		))
	?>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=__d('admin', 'Enable')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('enable', array(
						'type' => 'checkbox',
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=__d('admin', 'Page Title')?></label>
			<div class="col-sm-10">
				<?=
					$this->AdminForm->input('title', array(
						'type' => 'text',
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12">
				<?=
					$this->AdminForm->input('text', array(
						'type' => 'textarea',
						'rows' => 5,
					))
				?>
			</div>
		</div>
		<div class="text-center">
			<button class="btn btn-primary"><?=__d('admin', 'Save')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
<?php $this->TinyMCE->editor() ?>
