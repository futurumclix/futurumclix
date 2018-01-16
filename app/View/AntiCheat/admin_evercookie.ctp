<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Evercookie settings')?></h2>
	</div>
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
		<div class="form-group">
			<label class="col-sm-4"><?=__d('admin', 'Enable Evercookie')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('Settings.Evercookie.enable', array(
						'type' => 'checkbox',
						'data-toggle' => 'popover',
						'data-trigger' => 'focus',
						'data-placement' => 'top',
						'data-content' => __d('admin', 'Click To Enable EverCookie Protection'),
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('admin', 'Evercookie Cookie Name')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('Settings.Evercookie.name', array(
						'type' => 'text',
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4"><?=__d('admin', 'Java')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('Settings.Evercookie.options.java', array(
						'type' => 'checkbox',
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4"><?=__d('admin', 'SliverLight')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('Settings.Evercookie.options.silverlight', array(
						'type' => 'checkbox',
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4"><?=__d('admin', 'History')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('Settings.Evercookie.options.history', array(
						'type' => 'checkbox',
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4"><?=__d('admin', 'What To Do With Users Caught Cheating via Evercookie')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('Settings.Evercookie.mode', array(
						'type' => 'select',
						'options' => array(
							'suspend' => __d('admin', 'Suspend'),
							'email' => __d('admin', 'Send email to admin'),
						),
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('admin', 'Evercookie Exceptions (usernames)')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('Settings.Evercookie.exceptions', array(
						'data-toggle' => 'tooltip',
						'data-placement' => 'top',
						'title' => __d('admin', 'You can enter more than one username, separated with comma with no spaces, for example: user1,user2.'),
					))
				?>
			</div>
		</div>
		<div class="col-sm-12 text-center paddingten">
			<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
