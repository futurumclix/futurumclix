<div id="add" class="tab-pane fade in">
	<div class="title">
		<h2><?=__d('referrals_contest_admin', 'Contest details')?></h2>
	</div>
	<?=$this->AdminForm->create('ReferralsContest', array('class' => 'form-horizontal'))?>
		<div class="form-group">
			<label class="col-sm-3 control-label"><?=__d('referrals_contest_admin', 'Contest Name')?></label>
			<div class="col-sm-9">
				<?=$this->AdminForm->input('title')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><?=__d('referrals_contest_admin', 'Contest Description')?></label>
			<div class="col-sm-9">
				<?=
					$this->AdminForm->input('description', array(
						'type' => 'textarea',
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><?=__d('referrals_contest_admin', 'Contest Period')?></label>
			<div class="col-sm-3">
				<div class="input-group">
					<div class="input-group-addon"><?=__d('referrals_contest_admin', 'From')?></div>
					<?=$this->AdminForm->input('starts', array('type' => 'datetime'))?>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="input-group">
					<div class="input-group-addon"><?=__d('referrals_contest_admin', 'To')?></div>
					<?=$this->AdminForm->input('ends', array('type' => 'datetime'))?>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><?=__d('referrals_contest_admin', 'Activity')?></label>
			<div class="col-sm-9">
				<?php
					$input = $this->AdminForm->input('activity', array('style' => 'display: inline-block; width: 70px; margin: 0 5px'));
					echo __d('referrals_contest_admin', 'Count only those users which made at least %s clicks after they signup.', $input);
				?>
			</div>
		</div>
		<div class="title2">
			<h2><?=__d('referrals_contest_admin', 'Prizes')?></h2>
		</div>
		<div class="form-group" id="prizesBody">
			<span id="prizesExampleRow">
				<div class="col-sm-2 col-md-offset-1">
					<div class="input-group">
						<div class="input-group-addon"><?=__d('referrals_contest_admin', 'Place')?></div>
						<input type="text" class="form-control" name="place" id="prizes0place" disabled="disabled" value="1" readonly>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="input-group">
						<div class="input-group-addon"><?=__d('referrals_contest_admin', 'Prize')?></div>
						<?=$this->AdminForm->input('ReferralsContest.prizes.0.prize')?>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="input-group">
						<div class="input-group-addon"><?=__d('referrals_contest_admin', 'Where To Credit Prize')?></div>
						<?=
							$this->AdminForm->input('ReferralsContest.prizes.0.credit', array(
								'type' => 'select',
								'options' => array(
									'account' => __d('referrals_contest_admin', 'Account Balance'),
									'purchase' => __d('referrals_contest_admin', 'Purchase Balance'),
								),
							))
						?>
					</div>
				</div>
			</span>
			<?php for($i = 1, @$max = count($this->request->data['ReferralsContest']['prizes']); $i < $max; $i++): ?>
				<div class="col-sm-2 col-md-offset-1">
					<div class="input-group">
						<div class="input-group-addon"><?=__d('referrals_contest_admin', 'Place')?></div>
						<input type="text" class="form-control" name="place" id="prizes0place" disabled="disabled" value="<?=($i + 1)?>" readonly>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="input-group">
						<div class="input-group-addon"><?=__d('referrals_contest_admin', 'Prize')?></div>
						<?=$this->AdminForm->input("ReferralsContest.prizes.$i.prize")?>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="input-group">
						<div class="input-group-addon"><?=__d('referrals_contest_admin', 'Where To Credit Prize')?></div>
						<?=
							$this->AdminForm->input("ReferralsContest.prizes.$i.credit", array(
								'type' => 'select',
								'options' => array(
									'account' => __d('referrals_contest_admin', 'Account Balance'),
									'purchase' => __d('referrals_contest_admin', 'Purchase Balance'),
								),
							))
						?>
					</div>
				</div>
			<?php endfor; ?>
		</div>
		<div class="col-md-12 text-right">
			<a id="addPrizesTableRowButton">
				<i title="<?=__d('referrals_contest_admin', 'Click to add another place')?>" data-toggle="tooltip" data-placement="left" class="fa fa-plus-circle fa-lg"></i>
			</a>
		</div>
		<div class="col-sm-12 text-center paddingten">
			<button class="btn btn-primary"><?=__d('referrals_contest_admin', 'Save contest')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
<?php
	$this->Js->buffer("
		var rowsNo = 1;
		$('#addPrizesTableRowButton').click(function() {
			var newRow = $('#prizesExampleRow').clone();
			newRow.find('input, select').each(function(idx, obj) {
				obj = $(obj);
				obj.attr('name', obj.attr('name').replace('0', rowsNo));
				obj.attr('id', obj.attr('id').replace('0', rowsNo));
				obj.val('');
				obj.removeClass('error');
			});
			newRow.find('[name=place]').val(rowsNo + 1);
			newRow.find('[data-cleanup=yes]').html('');
			$('#prizesBody').append(newRow);
			rowsNo += 1;
		});
	");
?>
