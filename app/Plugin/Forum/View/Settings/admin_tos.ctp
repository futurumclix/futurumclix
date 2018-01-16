<div class="col-md-12">
	<div class="title">
		<h2><?=__d('forum', 'Edit ToS')?></h2>
	</div>
	<div>
		<?=
			$this->AdminForm->create(false, array(
				'class' => 'form-horizontal',
			))
		?>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Enable')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('enable', array(
						'type' => 'checkbox',
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-12">
				<?=
					$this->AdminForm->input('text', array(
						'type' => 'textarea',
					))
				?>
			</div>
		</div>
		<div class="text-center col-sm-12 paddingten">
			<button class="btn btn-primary"><?=__d('forum_admin', 'Save Info')?></button>
		</div>
		<?=$this->AdminForm->end()?>
	</div>
</div>
<?php $this->TinyMCE->editor() ?>
