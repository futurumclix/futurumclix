<div class="col-md-12">
	<div class="title">
		<h2><?=__d('referrals_contest_admin', 'Referral Contest')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#settings"><?=__d('referrals_contest_admin', 'Settings')?></a></li>
		<li><a data-toggle="tab" href="#current"><?=__d('referrals_contest_admin', 'Current contests')?></a></li>
		<li><a data-toggle="tab" href="#add"><?=__d('referrals_contest_admin', 'Add Contest')?></a></li>
	</ul>
	<div class="tab-content">
		<div id="settings" class="tab-pane fade in active">
			<div class="title">
				<h2><?=__d('referrals_contest_admin', 'Settings')?></h2>
			</div>
			<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=__d('referrals_contest_admin', 'Show Finished Referral Contests')?></label>
					<div class="col-sm-2">
						<?=
							$this->AdminForm->input('ReferralsContestSettings.referralsContest.showFinished', array(
								'type' => 'checkbox',
								'data-placement' => 'top',
								'data-toggle' => 'popover',
								'data-trigger' => 'hover',
								'data-content' => __d('referrals_contest_admin', 'Check this option to show all finished contests on the user side'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=__d('referrals_contest_admin', 'Show Future Referral Contests')?></label>
					<div class="col-sm-2">
						<?=
							$this->AdminForm->input('ReferralsContestSettings.referralsContest.showFuture', array(
								'type' => 'checkbox',
								'data-placement' => 'top',
								'data-toggle' => 'popover',
								'data-trigger' => 'hover',
								'data-content' => __d('referrals_contest_admin', 'Check this option to show all future contests on the user side'),
							))
						?>
					</div>
				</div>
				<div class="col-sm-12 text-center paddingten">
					<button class="btn btn-primary"><?=__d('referrals_contest_admin', 'Save changes')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<div id="current" class="tab-pane fade in">
			<?=
				$this->AdminForm->create(false, array(
					'url' => array('action' => 'massaction'),
				))
			?>
			<div class="table-responsive">
				<table class="table table-striped table-hover">
					<tbody>
						<tr>
							<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
							<th><?=$this->Paginator->sort('Title')?></th>
							<th><?=$this->Paginator->sort('starts', __d('referrals_contest_admin', 'From'))?></th>
							<th><?=$this->Paginator->sort('ends', __d('referrals_contest_admin', 'To'))?></th>
							<th><?=__d('referrals_contest_admin', 'Places')?></th>
							<th><?=__d('referrals_contest_admin', 'Status')?></th>
							<th><?=__d('referrals_contest_admin', 'Actions')?></th>
						</tr>
						<?php foreach($contests as $contest): ?>
							<tr>
								<td>
									<?=
										$this->AdminForm->checkbox('ReferralsContest.'.$contest['ReferralsContest']['id'], array(
											'class' => 'ActionCheckbox',
										))
									?>
								</td>
								<td><?=$contest['ReferralsContest']['title']?></td>
								<td><?=$contest['ReferralsContest']['starts']?></td>
								<td><?=$contest['ReferralsContest']['ends']?></td>
								<td><?=count($contest['ReferralsContest']['prizes'])?></td>
								<td>
									<?php
										if($this->Time->isPast($contest['ReferralsContest']['ends'])) {
											echo __d('referrals_contest_admin', 'Ended');
										} else if($this->Time->isFuture($contest['ReferralsContest']['starts'])) {
											echo __d('referrals_contest_admin', 'Future');
										} else {
											echo __d('referrals_contest_admin', 'Active');
										}
									?>
								</td>
								<td>
									<?=
										$this->Html->link('<i class="fa fa-eye fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('referrals_contest_admin', 'View').'"></i>',
											array('action' => 'view', $contest['ReferralsContest']['id']),
											array('escape' => false)
										)
									?>
									<?=
										$this->Html->link('<i class="fa fa-pencil fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('referrals_contest_admin', 'Edit').'"></i>',
											array('action' => 'edit', $contest['ReferralsContest']['id']),
											array('escape' => false)
										)
									?>
									<?=
										$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('referrals_contest_admin', 'Delete').'"></i>',
											array('action' => 'delete', $contest['ReferralsContest']['id']),
											array('escape' => false),
											__d('referrals_contest_admin', 'Are you sure you want to delete "%s"?', $contest['ReferralsContest']['title'])
										)
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<div class="col-sm-5 text-left">
				<div class="input-group">
					<label for="selectMassAction" class="input-group-addon"><?=__d('referrals_contest_admin', 'Mass action')?></label>
					<?=
						$this->AdminForm->input('Action', array(
							'empty' => __d('referrals_contest_admin', '--Choose--'),
							'required' => true,
							'id'=> 'actionSelect',
							'options' => array(
								'delete' => __d('referrals_contest_admin', 'Delete'),
							)
						))
					?>
					<div class="input-group-btn">
						<button class="btn btn-danger"><?=__d('referrals_contest_admin', 'Perform action')?></button>
					</div>
				</div>
			</div>
			<?=$this->AdminForm->end()?>
			<div class="col-sm-7 text-right">
				<?=$this->Paginator->counter(array('format' => __d('referrals_contest_admin', 'Page {:page} of {:pages}')))?>
			</div>
			<div class="col-sm-12 text-center paddingten">
				<nav>
					<ul class="pagination pagination-sm">
						<?php
							echo $this->Paginator->first('&laquo;', array('tag' => 'li', 'escape' => false));
							echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'a'));
							echo $this->Paginator->last('&raquo;', array('tag' => 'li', 'escape' => false));
						?>
					</ul>
				</nav>
			</div>
		</div>
		<div id="add" class="tab-pane fade in">
			<div class="title">
				<h2><?=__d('referrals_contest_admin', 'Contest Details')?></h2>
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
	</div>
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
