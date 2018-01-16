<div id="Bitcoin" class="tab-pane fade in">
	<?=$this->AdminForm->create('Settings', array('class' => 'form-horizontal'))?>
	<div class="title2">
		<h2><?=__d('admin', 'API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Count incoming BTC payments (CoinBase and BlockChain) to withdraw limit')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('bitcoinDepositsSumHack', array(
				'type' => 'checkbox',
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', ''),
			))?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
