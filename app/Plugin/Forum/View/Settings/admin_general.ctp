<div class="col-md-12">
	<div class="title">
		<h2><?=__d('forum_admin', 'General Settings')?></h2>
	</div>
	<?=$this->AdminForm->create('Settings', array('class' => 'form-horizontal'))?>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('forum_admin', 'Only for logged in users')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Settings.Forum.onlyLogged', array(
					'type' => 'checkbox',
					'default' => $configuration['Forum.onlyLogged'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('forum_admin', 'Active')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Settings.Forum.active', array(
					'type' => 'checkbox',
					'default' => $configuration['Forum.active'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('forum_admin', 'Show Forum Statistics')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Settings.Forum.indexStatistics', array(
					'type' => 'checkbox',
					'default' => $configuration['Forum.indexStatistics'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('forum_admin', 'Show Newest User')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Settings.Forum.newestUser', array(
					'type' => 'checkbox',
					'default' => $configuration['Forum.newestUser'],
				))
			?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('forum_admin', 'Save Changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
