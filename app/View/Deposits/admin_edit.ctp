<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Edit Deposit')?></h2>
	</div>
	<?=$this->AdminForm->create('Deposit', array('class' => 'form-horizontal'))?>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Username')?></label>
		<div class="col-sm-8">
			<?=$this->AdminForm->input('User.username')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Amount')?></label>
		<div class="col-sm-8">
			<div class="input-group">
				<?=$this->AdminForm->input('amount')?>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'From Account')?></label>
		<div class="col-sm-8">
			<?=$this->AdminForm->input('account')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Status')?></label>
		<div class="col-sm-8">
			<?=$this->AdminForm->input('status')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Method')?></label>
		<div class="col-sm-8">
			<?=$this->AdminForm->input('gateway', array(
					'options' => $gateways,
					'empty' => __d('admin', 'Select one'),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Transaction ID')?></label>
		<div class="col-sm-8">
			<?=$this->AdminForm->input('gatewayid')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Date')?></label>
		<div class="col-sm-8">
			<?=$this->AdminForm->input('date')?>
		</div>
	</div>
	<div class="text-center col-sm-12 paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
