<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Assign Referrals')?></h2>
	</div>
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Type Of Referral')?></label>
		<div class="col-sm-8">
			<?php if($direct == 0 || $direct === 'disabled'): ?>
			<div class="checkbox disabled">
				<label data-toggle="tooltip" data-placement="right" title="<?php if($direct !== 'disabled'): ?><?=__d('admin', 'You have no referrals available to assign.')?><?php else: ?><?=__d('admin', 'Referral buying is not enabled')?><?php endif; ?>"/>
					<input type="checkbox" disabled="disabled">
			<?php else: ?>
			<div class="checkbox">
				<label>
				<?=
					$this->AdminForm->input('rentingType', array(
						'style' => 'display: none;',
						'value' => 'none',
					))
				?>
				<?=
					$this->AdminForm->checkbox('rentingType', array(
						'value' => 'direct',
						'hiddenField' => false,
						'class' => 'radioCheckbox',
					))
				?>
			<?php endif; ?>
					<?php if($direct === 'disabled'): ?>
					<?=__d('admin', 'Direct')?>
					<?php else: ?>
					<?=__d('admin', 'Direct (available: %u)', h($direct))?>
					<?php endif; ?>
				</label>
			</div>
			<?php if($rented == 0 || $rented === 'disabled'): ?>
			<div class="checkbox disabled">
				<label data-toggle="tooltip" data-placement="right" title="<?php if($rented !== 'disabled'): ?><?=__d('admin', 'You have no referrals available for renting.')?><?php else:?><?=__d('admin', 'Referral renting is not enabled')?><?php endif; ?>"/>
					<input type="checkbox" disabled="disabled">
			<?php else: ?>
			<div class="checkbox">
				<label>
				<?=
					$this->AdminForm->checkbox('rentingType', array(
						'value' => 'rented',
						'hiddenField' => false,
						'class' => 'radioCheckbox',
						'id' => 'assignRentingTypeRented'
					))
				?>
			<?php endif; ?>
					<?php if($rented === 'disabled'): ?>
					<?=__d('admin', 'Rented')?>
					<?php else: ?>
					<?=__d('admin', 'Rented (available: %u)', h($rented))?>
					<?php endif; ?>
				</label>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Number Of Referrals')?></label>
		<div class="col-sm-2">
			<?=
				$this->AdminForm->input('number', array(
										'type' => 'number',
				));
			?>
		</div>
	</div>
	<div class="form-group" id="daysRow">
		<label class="col-sm-4 control-label"><?=__d('admin', 'How Many Days?')?></label>
		<div class="col-sm-2">
			<?=
				$this->AdminForm->input('days', array(
					'type' => 'number',
					'min' => '1',
					'step' => '1',
				))
			?>
		</div>
	</div>
	<div class="form-group" id="daysRow">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Upline Username')?></label>
		<div class="col-sm-2">
			<?=
				$this->AdminForm->input('username', array(
										'type' => 'text',
										'data-toggle' => 'popover',
										'data-trigger' => 'focus',
										'data-placement' => 'top',
										'data-content' => __d('admin', 'To who do you want to assign referrals'),
				))
			?>
		</div>
	</div>
	<div class="text-center col-sm-12 paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Assign Referrals')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
<?php
	$this->Js->buffer("
	checkboxesAsRadio();
	$('#assignRentingTypeRented').change(function() {
		$('#daysRow').toggle($(this).is(':checked'));
	})
	$('#daysRow').toggle($('#assignRentingTypeRented').is(':checked'));
");
?>
