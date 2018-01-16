<div class="col-md-12">
	<div class="title">
   	 <h2><?=__d('admin', 'Clean Up User Database')?></h2>
   	</div>
	<div class="title2">
		<h2><?=__d('admin', 'Suspend Inactive Users')?></h2>
	</div>
    <div class="paddingten text-center"><?=__d('admin', 'Suspend all users which weren\'t logged in longer than entered amount of days.')?></div>
	<?=$this->AdminForm->create(array('class' => 'form-horizontal'))?>
	<div class="col-sm-6 col-md-offset-3">
	<div class="input-group">
		<label class="input-group-addon"><?=__d('admin', 'Suspend users logged in more than')?></label>
		<?=$this->AdminForm->input('days', array(
            	'type' => 'number',
				'min' => 0,
				'step' => '1',
				'required' => true,
            ))?>
		<div class="input-group-addon"><?=__d('admin', 'days ago.')?></div>
		</div>
	</div>
	<?=$this->AdminForm->input('action', array(
		'type' => 'hidden',
		'value' => 'suspendInactive',
	))?>
	<div class="text-center col-md-12 paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Suspend')?></button>
	</div>
	<div class="clearfix"></div>
	<?=$this->AdminForm->end()?>
    <div class="title2">
		<h2><?=__d('admin', 'Delete Inactive Users')?></h2>
	</div>
    <div class="paddingten text-center"><?=__d('admin', 'Delete all users which weren\'t logged in longer than entered amount of days.')?></div>
	<?=$this->AdminForm->create(array('class' => 'form-horizontal'))?>
    <div class="col-sm-6 col-md-offset-3">
		<div class="input-group">
			<label class="input-group-addon"><?=__d('admin', 'Delete users logged in more than')?></label>
			<?=$this->AdminForm->input('days', array(
            	'type' => 'number',
				'min' => 0,
				'step' => '1',
				'required' => true,
            ))?>
        <div class="input-group-addon"><?=__d('admin', 'days ago.')?></div>
		</div>
	</div>
 			<?=$this->AdminForm->input('action', array(
 				'type' => 'hidden',
 				'value' => 'deleteInactive',
 			))?>
    <div class="text-center col-md-12 paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Delete')?></button>
	</div>
	<div class="clearfix"></div>
	<?=$this->AdminForm->end()?>
	<div class="title2">
	   <h2><?=__d('admin', 'Delete Suspended Users')?></h2>
	</div>
    <div class="paddingten text-center"><?=__d('admin', 'Delete all suspended users which weren\'t logged in longer than entered amount of days.')?></div>
    <?=$this->AdminForm->create(array('class' => 'form-horizontal'))?>
    <div class="col-sm-6 col-md-offset-3">
		<div class="input-group">
			<label class="input-group-addon"><?=__d('admin', 'Delete suspended users logged in more than')?></label>
			<?=$this->AdminForm->input('days', array(
            	'type' => 'number',
				'min' => 0,
				'step' => '1',
				'required' => true,
            ))?>
		<div class="input-group-addon"><?=__d('admin', 'days ago.')?></div>
		</div>
	</div>
	<?=$this->AdminForm->input('action', array(
		'type' => 'hidden',
		'value' => 'deleteSuspended',
	))?>
    <div class="text-center col-md-12 paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Delete')?></button>
	</div>
	<div class="clearfix"></div>
	<?=$this->AdminForm->end()?>
</div>
