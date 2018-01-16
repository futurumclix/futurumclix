<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Renting Referrals')?></h2>
	</div>
	<?=$this->AdminForm->create('Settings', array('class' => 'form-horizontal'))?>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Enable Renting Referrals')?></label>
		<div class="col-sm-4">
			<?=
				$this->AdminForm->input('enableRentingReferrals', array(
					'type' => 'checkbox',
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-content' => __d('admin', 'Check to enable / disable referral renting.'),
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Renting Option')?></label>
		<div class="col-sm-4">
			<div class="checkbox">
				<label>
					<?=
						$this->AdminForm->checkbox('rentingOption', array(
							'value' => 'realOnly',
							'hiddenField' => false,
							'class' => 'radioCheckbox',
							'id' => 'SettingsRentingOptionReal',
						))
					?>
					<?=__d('admin', 'Rent Real Users Only')?>
				</label>
			</div>
			<div class="checkbox <?php if(!Module::active('BotSystem')): ?>disabled<?php endif; ?>">
				<label <?php if(!Module::active('BotSystem')): ?>data-toggle="tooltip" data-placement="right" title="<?=__d('admin', 'You need to install and activate BotSystem module to enable this option.')?><?php endif; ?>">
					<?=
						$this->AdminForm->checkbox('rentingOption', array(
							'value' => 'botsOnly',
							'hiddenField' => false,
							'disabled' => Module::active('BotSystem') ? false : true,
							'class' => 'radioCheckbox',
						))
					?>
					<?=__d('admin', 'Rent bots only')?>
				</label>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Rental Period')?></label>
		<div class="col-sm-4">
			<?=
				$this->AdminForm->input('rentPeriod', array(
					'type' => 'number',
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('admin', 'For how long (in days) do you want to rent referrals'),
				));
			?>
		</div>
	</div>
	<div class="form-group" id="RentFilterGroup">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Who Can Be Rented (Filter)')?></label>
		<div class="col-sm-8">
			<?php
				$daysInput = $this->AdminForm->input('rentMinClickDays', array(
					'type' => 'number',
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'style' => 'display: inherit; width: 70px; margin: 0 5px;',
				));
				echo $this->AdminForm->radio('rentFilter', array(
							'clickDays' => '<div class="radio">'.__d('admin', 'Active user which clicked at least last').$daysInput.__d('admin', 'days').'</div>',
							'onlyActive' => '<div class="radio">'.__d('admin', 'All active users').'</div>',
							'all' => '<div class="radio">'.__d('admin', 'All members (active and inactive)').'</div>',
						), array(
							'separator' => '<br/>',
							'legend' => false,
							'label' => false,
						));
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Auto Renew Days')?></label>
		<div class="col-sm-4">
			<?=
				$this->AdminForm->input('autoRenewDays', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('admin', 'Please set days before expiry for AutoRenew, please put them after comma like: 1,2,3,4'),
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Auto Renew Tries')?></label>
		<div class="col-sm-4">
			<?=
				$this->AdminForm->input('autoRenewTries', array(
					'type' => 'number',
					'min' => -1,
					'step' => 1,
					'max' => 128,
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('admin', 'How many times can system try to AutoRenew referral. Pust -1 for unlimited amount of times, 0 to disable AutoRenew. If system will try set amount of days and upline will have no money for AutoRenew, referral will be marked and will not be taken in consideration for AutoRenew.'),
				))
			?>
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'Extension Periods And Discounts')?></h2>
	</div>
	<div id="rentingPeriodsTable">
		<?php $max = count($rentingPeriods); ?>
		<?php if($max == 0): ?>
			<div class="text-center col-sm-12 paddingten" id="addRowButton"><i title="<?=__d('admin', 'Click to add another range')?>" data-toggle="tooltip" data-placement="left" class="fa fa-plus-circle fa-lg"></i></div>
		<?php endif; ?>
		<?php for($i = 0; $i < $max; ++$i): $rp = &$rentingPeriods[$i];?>
		<div class="form-group">
			<?=
				$this->AdminForm->input("RentExtensionPeriod.$i.id", array(
					'type' => 'hidden',
					'value' => $rp['RentExtensionPeriod']['id'],
				));
			?>
			<div class="col-sm-5">
				<div class="input-group">
					<div class="input-group-addon"><?=__d('admin', 'Days')?></div>
					<?=
						$this->AdminForm->input("RentExtensionPeriod.$i.days", array(
							'type' => 'number',
							'data-toggle' => 'popover',
							'data-trigger' => 'focus',
							'data-placement' => 'top',
							'data-content'=> __d('admin', 'Please put number of days to extend referrals'),
							'value' => $rp['RentExtensionPeriod']['days'],
						));
					?>
				</div>
			</div>
			<div class="col-sm-5">
				<div class="input-group">
					<div class="input-group-addon"><?=__d('admin', 'Discount')?></div>
					<?=
						$this->AdminForm->input("RentExtensionPeriod.$i.discount", array(
							'type' => 'number',
							'data-toggle' => 'popover',
							'data-trigger' => 'focus',
							'data-placement' => 'top',
							'data-content'=> __d('admin', 'Please put discount for this period. Put 0 to not to give any discount'),
							'value' => $rp['RentExtensionPeriod']['discount'],
						));
					?>
				</div>
			</div>
			<div class="col-sm-2 text-center">
				<?=
					$this->AdminForm->postLink('<i class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Click to delete this range').'"></i>',
						array('controller' => 'RentExtensionPeriods', 'action' => 'delete', $rp['RentExtensionPeriod']['id']),
						array('escape' => false),
						__d('admin', 'Are you sure you want to delete # %s?', $rp['RentExtensionPeriod']['id'])
					)
				?>
			</div>
			<?php if($i == $max - 1): ?>
				<div id="addRowButton" class="col-sm-12 text-right"><i title="<?=__d('admin', 'Click to add another range')?>" data-toggle="tooltip" data-placement="left" class="fa fa-plus-circle fa-lg"></i></div>
			<?php endif; ?>
		</div>
		<?php endfor; ?>
	</div>
	<div class="text-center col-sm-12 paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
<?php
	$days = __d('admin', 'Days');
	$discount = __d('admin', 'Discount');
	$daysInput = $this->AdminForm->input('RentExtensionPeriod.NUM.days', array(
							'type' => 'number',
							'data-toggle' => 'popover',
							'data-trigger' => 'focus',
							'data-content'=> __d('admin', 'Please put number of days to extend referrals'),
					 ));
	$discountInput = $this->AdminForm->input('RentExtensionPeriod.NUM.discount', array(
							'type' => 'number',
							'data-toggle' => 'popover',
							'data-trigger' => 'focus',
							'data-content'=> __d('admin', 'Please put discount for this period. Put 0 to not to give any discount'),
						  ));
	$remButton = '<a><i class="fa fa-minus-circle fa-lg remButton" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Click to delete this range').'"></i></a>';
	$this->Js->buffer("
		checkboxesAsRadio();
		var no = $max;
		$('#addRowButton').click(function clickFunc() {
			var row = '<div class=\"form-group\"><div class=\"col-sm-5\"><div class=\"input-group\"><div class=\"input-group-addon\">$days</div>$daysInput</div></div><div class=\"col-sm-5\"><div class=\"input-group\"><div class=\"input-group-addon\">$discount</div>$discountInput</div></div><div class=\"col-sm-2 text-center\">$remButton</div></div>';
			row = $(row.replace(/NUM/g, '' + no++));

			var button = $('#addRowButton');
			button.find('i').mouseleave();

			row.find('.remButton').click(function() {
				$('#rentingPeriodsTable').append(button);
				row.remove();
			});

			$('#rentingPeriodsTable').append(row.append(button));
			if($('#rentingPeriodsTable').find(':first').find('input').length == 0) {
				$('#rentingPeriodsTable').find(':first').remove();
			}
		});
		if($('#SettingsRentingOptionReal').prop('checked')) {
			$('#RentFilterGroup').show();
		} else {
			$('#RentFilterGroup').hide();
		}
		$('#SettingsRentingOptionReal').on('change', function(){ $('#RentFilterGroup').toggle();});
		$('#SettingsRentingOption').on('change', function(){ $('#RentFilterGroup').toggle();});
	");
?>
