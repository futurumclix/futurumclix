<?=$this->element('userSidebar')?>
<div class="page">
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 padding30-sides">
			<?php $this->Chart->emitScripts()?>
			<?=$this->element('userBreadcrumbs')?>
			<?=$this->Notice->show()?>
			<div class="panel">
				<div class="padding30-col">
					<div class="col-md-6">
						<h5><?=__('Direct Referrals')?></h5>
					</div>
					<div class="col-md-6 text-xs-right">
						<?php if($onlyCredited): ?>
							<?=$this->Html->link(__('Show all clicks'), array('false'), array('class' => 'btn btn-primary btn-sm')) ?>
						<?php else: ?>
							<?=$this->Html->link(__('Show only credited clicks'), array('true'), array('class' => 'btn btn-primary btn-sm')) ?>
						<?php endif; ?>
					</div>
						<div class="col-md-12 margin30-top table-responsive">
							<?=
								$this->UserForm->create(false, array(
									'id' => 'directForm',
								))
							?>
							<table class="table">
								<tbody>
									<tr>
										<th><?=$this->Paginator->sort('username', __('Referral'))?></th>
										<th><?=$this->Paginator->sort('dref_since', __('Referral Since'))?></th>
										<th><?=$this->Paginator->sort('comes_from', __('Comes From'))?></th>
										<th><?=$this->Paginator->sort('UserStatistic.last_click_date', __('Last Click'))?></th>
										<th><?=$this->Paginator->sort($onlyCredited ? 'UserStatistics.clicks_as_dref_credited' : 'UserStatistics.clicks_as_dref', __('Clicks'))?></th>
										<th><?=$this->Paginator->sort('UserStatistic.earned_as_dref', __('Earned'))?></th>
										<th><?=$this->Paginator->sort('clicks_avg', __('AVG'))?></th>
										<th>&nbsp;</th>
										<th><input id="selectAllRefs" type="checkbox"/></th>
									</tr>
									<?php foreach($refs as $ref): ?>
										<tr>
											<td><?=h($ref['User']['username'])?></td>
											<td><?=h($this->Time->nice($ref['User']['dref_since']))?></td>
											<td><?=h($ref['User']['comes_from'])?></td>
											<?php if($ref['UserStatistic']['last_click_date'] === null): ?>
												<td><?=h(__('Never'))?></td>
											<?php else: ?>
												<td><?=h($this->Time->niceShort($ref['UserStatistic']['last_click_date']))?></td>
											<?php endif; ?>
											<td><?=h($ref['UserStatistic'][$onlyCredited ? 'clicks_as_dref_credited' : 'clicks_as_dref'])?></td>
											<td><?=$this->Currency->format($ref['UserStatistic']['earned_as_dref'])?></td>
											<td><?=h($ref['User']['clicks_avg_as_dref'])?></td>
											<td>
												<a data-toggle="modal" data-target="#ajax-modal-container" data-ajaxsource="<?=$this->Html->url(array('action' => 'directReferralStats', $ref['User']['id']))?>"><i data-toggle="tooltip" data-placement="top" title="<?=__('Show this referral statistics')?>" class="fa fa-info-circle"></i></a> 
												<?=
													$this->UserForm->postLink('<i data-toggle="tooltip" data-placement="top" title="'.__('Delete this referral').'" class="fa fa-trash"></i> ',
														array('action' => 'unhookDirectReferral', $ref['User']['id']),
														array('escape' => false),
														__('Do you want to remove %s for $%s?', $ref['User']['username'], $user['ActiveMembership']['Membership']['direct_referrals_delete_cost'])
													)
												?>
											</td>
											<td>
												<?=
													$this->UserForm->input('Referrals.'.$ref['User']['id'], array(
														'class' => 'selectRef',
														'type' => 'checkbox',
													))
												?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
							<div class="col-md-12 text-xs-right pagecounter">
								<?=$this->Paginator->counter(array('format' => __('Page {:page} of {:pages}')))?>
							</div>
							<div class="col-md-12 text-xs-center">
								<nav>
									<ul class="pagination pagination-sm">
										<?php
											echo $this->Paginator->first('&laquo;', array('tag' => 'li', 'class' => 'page-item', 'escape' => false));
											echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'class' => 'page-item', 'currentClass' => 'active', 'currentTag' => 'a'));
											echo $this->Paginator->last('&raquo;', array('tag' => 'li', 'class' => 'page-item', 'escape' => false));
										?>
									</ul>
								</nav>
							</div>
							<div id="refsActionDiv" class="col-md-6 col-md-offset-3 directrefsinfo" style="display: none">
								<?=__('What would you like to do with selected referrals?')?>
								<br /><br />
								<?=
									$this->UserForm->input('action', array(
										'id' => 'refsActionSelect',
										'empty' => __('Please choose'),
									))
								?>
								<br /><br />
								<button class="btn btn-primary"><?=__('Continue')?></button>
							</div>
							<?=$this->UserForm->end()?>
						</div>
						<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$confirm = __('Do you want to remove those referrals for');
$noaction = __('Please select an action.');
$this->Js->buffer("
	function showRefsActionDiv() {
		var checkedCount = $('.selectRef:checked').length;
		$('#refsActionSelect > option').each(function(index, item) {
			if($(item).val() == 'delete') {
				var cost = new Big(checkedCount).mul('{$user['ActiveMembership']['Membership']['direct_referrals_delete_cost']}');
				this.text = this.text.substring(0, this.text.indexOf('--') + 3) + formatCurrency(cost);
			}
		});
		if(checkedCount > 0) {
			$('#refsActionDiv').show();
		}
	}
	showRefsActionDiv();
	$('.selectRef').change(function() {
		if(!this.checked) {
			$('#selectAllRefs').attr('checked', false);
			if($('.selectRef:checked').length == 0) {
				$('#refsActionDiv').hide();
			} else {
				showRefsActionDiv();
			}
		} else {
			if($('.selectRef:not(:checked)').length == 0) {
				$('#selectAllRefs').prop('checked', true);
			}
			showRefsActionDiv();
		}
	});
	$('#selectAllRefs').change(function() {
		setAllCheckboxes('selectRef', this.checked);
		if(this.checked) {
			showRefsActionDiv();
		} else {
			$('#refsActionDiv').hide();
		}
	});
	$('#directForm').submit(function(event) {
		if($('#refsActionSelect').val() == 'delete') {
			var cost = new Big($('.selectRef:checked').length).mul('{$user['ActiveMembership']['Membership']['direct_referrals_delete_cost']}');
			cost = formatCurrency(cost);
			return confirm('$confirm ' + cost + '?');
		} else {
			alert('$noaction');
			return false;
		}
	});
")
?>
