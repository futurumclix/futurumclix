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
						<div class="col-md-12">
							<h5><?=__('Rented Referrals')?></h5>
						</div>
						<div class="col-sm-2 moneypanel">
							<h6><?=__('Purchase balance')?></h6>
							<h3><?=h($this->Currency->format($user['User']['purchase_balance']))?></h3>
						</div>
						<div class="col-sm-1 moneypanel moneypanelicon">
							<?=$this->Html->link('<i class="fa fa-chevron-up"></i>', array('action' => 'deposit'), array(
								'title' => __('Add funds'),
								'data-toggle' => 'tooltip',
								'data-placement' => 'left',
								'escape' => false,
							))?>
						</div>
						<div class="col-sm-2 moneypanel">
							<h6><?=__('AutoPay')?></h6>
							<h3><?=$user['User']['autopay_enabled'] ? __('Enabled') : __('Disabled')?></h3>
						</div>
						<div class="col-sm-1 moneypanel moneypanelicon">
							<a data-toggle="modal" data-target="#ajax-modal-container" data-ajaxsource="<?=$this->Html->url(array('action' => 'autopayStats'))?>"><i data-toggle="tooltip" data-placement="left" title="<?=__('Statistics')?>" class="fa fa-area-chart"></i></a>
							<?php if($user['User']['autopay_enabled']): ?>
								<?=
									$this->UserForm->postLink('<i data-toggle="tooltip" data-placement="left" title="'.__('Disable AutoPay').'" class="fa fa-plus"></i>',
										array('action' => 'setAutopay', 0),
										array('escape' => false),
										__('Are you sure you want to disable AutoPay?')
									)
								?>
							<?php else: ?>
								<?=
									$this->UserForm->postLink('<i data-toggle="tooltip" data-placement="left" title="'.__('Enable AutoPay').'" class="fa fa-minus"></i>',
										array('action' => 'setAutopay', 1),
										array('escape' => false),
										__('Are you sure you want to enable AutoPay?')
									)
								?>
							<?php endif; ?>
						</div>
						<?php if($user['User']['auto_renew_days'] == 0):?>
						<div class="col-sm-2 moneypanel moneypanelicon">
							<h6><?=__('AutoRenew')?></h6>
							<h3 id="autoRenewDisabledInfo"><?=__('Disabled')?></h3>
							<div id="autoRenewForm" style="display:none">
								<?=$this->UserForm->create('User', array('class' => 'form-inline', 'url' => array('action' => 'autoRenew')))?>
									<div class="form-group">
										<label><?=__('Period:')?></label>
										<?=$this->UserForm->input('auto_renew_extend', array('class' => 'form-control', 'default' => $user['User']['auto_renew_extend']))?>
										<label><?=__('Days:')?></label>
										<?=$this->UserForm->input('auto_renew_days', array('class' => 'form-control', 'default' => $user['User']['auto_renew_days']))?>
										<button class="btn btn-primary btn-xs"><?=__('Enable')?></button>
									</div>
								<?=$this->UserForm->end()?>
							</div>
						</div>
						<div class="col-sm-1 moneypanel moneypanelicon">
							<a data-toggle="modal" data-target="#ajax-modal-container" data-ajaxsource="<?=$this->Html->url(array('action' => 'autorenewStats'))?>"><i data-toggle="tooltip" data-placement="left" title="<?=__('Statistics')?>" class="fa fa-area-chart"></i></a>
							<a><i data-toggle="tooltip" data-placement="left" title="<?=__('Enable AutoRenew')?>" class="fa fa-minus" onclick="$('#autoRenewForm').show();$(this).hide();$('#autoRenewDisabledInfo').hide();"></i></a>
						</div>
						<?php else: ?>
						<div class="col-sm-2 moneypanel">
							<h6><?=__('AutoRenew')?></h6>
							<h3><?=__('%d/%d', $user['User']['auto_renew_extend'], $user['User']['auto_renew_days'])?></h3>
						</div>
						<div class="col-sm-1 moneypanel moneypanelicon">
							<a data-toggle="modal" data-target="#ajax-modal-container" data-ajaxsource="<?=$this->Html->url(array('action' => 'autorenewStats'))?>"><i data-toggle="tooltip" data-placement="left" title="<?=__('Statistics')?>" class="fa fa-area-chart"></i></a>
							<?=
								$this->UserForm->postLink('<i data-toggle="tooltip" data-placement="left" title="'.__('Disable AutoRenew').'" class="fa fa-plus"></i>',
									array('action' => 'autoRenew'),
									array('escape' => false),
									__('Are you sure you want to disable AutoRenew?')
								)
							?>
						</div>
						<?php endif; ?>
						<div class="col-sm-2 moneypanel">
							<h6><?=__('Rented referrals')?></h6>
							<h3><?=h($user['User']['rented_refs_count'])?></h3>
						</div>
						<div class="col-sm-1 moneypanel moneypanelicon">
							<?=$this->Html->link('<i class="fa fa-chevron-up"></i>', array('action' => 'rentReferrals'), array(
								'title' => __('Rent more referrals'),
								'data-toggle' => 'tooltip',
								'data-placement' => 'left',
								'escape' => false,
							))?>
						</div>
						<?php if(empty($refs)): ?>
							<div class="col-md-12 text-xs-center margin30-top">
								<?=__('Currently you don\'t have any rented referrals. To rent some please go %s.', $this->Html->link('here', array('action' => 'rentReferrals')))?>
							</div>
						<div class="clearfix"></div>
						<?php else: ?>
					</div>
					<div class="padding30-col">
						<div class="col-md-12 margin30-top table-responsive">
							<?=$this->UserForm->create(false)?>
							<table class="table">
								<tbody>
									<tr>
										<th><?=$this->Paginator->sort('username', __('Referral'))?></th>
										<th><?=$this->Paginator->sort('rent_starts', __('Referral Since'))?></th>
										<th><?=$this->Paginator->sort('rent_ends', __('Expiry Date'))?></th>
										<th><?=$this->Paginator->sort('UserStatistic.last_click_date', __('Last Click'))?></th>
										<th><?=$this->Paginator->sort('UserStatistic.clicks_as_rref', __('Clicks'))?></th>
										<th><?=$this->Paginator->sort('UserStatistic.earned_as_rref', __('Earned'))?></th>
										<th><?=$this->Paginator->sort('clicks_avg', __('AVG'))?></th>
										<th>&nbsp;</th>
										<th><input id="selectAllRefs" type="checkbox"/></th>
									</tr>
									<?php foreach($refs as $ref): ?>
									<tr>
										<td><?=h($ref['User']['username'])?></td>
										<td><?=h($this->Time->nice($ref['User']['rent_starts']))?></td>
										<td><?=h($this->Time->nice($ref['User']['rent_ends']))?></td>
										<td>
											<?php if($ref['UserStatistic']['last_click_date'] == null): ?>
												<?=__('Never')?>
											<?php else: ?>
												<?php if($this->Time->isToday($ref['UserStatistic']['last_click_date'])): ?>
													<?=__('Today')?>
												<?php elseif($this->Time->wasYesterday($ref['UserStatistic']['last_click_date'])): ?>
													<?=__('Yesterday')?>
												<?php else: ?>
													<?=h($this->Time->format($ref['UserStatistic']['last_click_date'], '%a, %b %eS %Y'))?>
												<?php endif; ?>
											<?php endif; ?>
										</td>
										<td><?=h($ref['UserStatistic']['clicks_as_rref'])?></td>
										<td><?=$this->Currency->format($ref['UserStatistic']['earned_as_rref'])?></td>
										<td><?=h($ref['User']['clicks_avg_as_rref'])?></td>
										<td>
											<a data-toggle="modal" data-target="#ajax-modal-container" data-ajaxsource="<?=$this->Html->url(array('action' => 'rentedReferralStats', $ref['User']['url_id']))?>"><i data-toggle="tooltip" data-placement="top" title="<?=__('Show this referral statistics')?>" class="fa fa-info-circle"></i></a>  
											<?=
												$this->UserForm->postLink('<i data-toggle="tooltip" data-placement="top" title="'.__('Recycle this referral').'" class="fa fa-exchange"></i>',
													array('action' => 'recycleReferral', $ref['User']['url_id']),
													array('escape' => false),
													__('Are you sure you want to recycle %s?', $ref['User']['username'])
												)
											?>
										</td>
										<td>
											<?=
												$this->UserForm->input('rrefs.'.$ref['User']['url_id'], array(
													'type' => 'checkbox',
													'class' => 'selectRef',
													'div' => false,
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
							<div id="refsActionDiv" class="col-md-6 col-md-offset-3 rentedrefsinfo" style="display: none">
								<?=__('What would you like to do with selected referrals?')?>
								<br /><br />
								<?=
									$this->UserForm->input('action', array(
										'id' => 'refsActionSelect',
										'options' => $options,
										'empty' => __('Please choose'),
									))
								?>
								<br /><br />
								<button class="btn btn-primary"><?=__('Continue')?></button>
							</div>
							<?=$this->UserForm->end()?>
						</div>
						<div class="clearfix"></div>
					<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	$costs = json_encode($costs);
	$for = __('for'); // suxx
	$this->Js->buffer("
	var Costs = $costs;

	function showRefsActionDiv(checkedCount) {
		if(Costs === null) {
			return;
		}

		var checkedCount = $('.selectRef:checked').length;
		$('#refsActionSelect > option').each(function(index) {
			var cost = Big(checkedCount);
			if(index == 0) {
				return;
			} else if(index == 1) {
				cost = cost.mul(Costs['recycle']);
			} else if(index > 1) {
				id = $(this).val();
				cost = cost.mul(Costs['buy']).mul(Costs['extend'][id - 100]['modifier']);
				cost = cost.sub(cost.mul(Big(Costs['extend'][id - 100]['discount']).div(100)));
			}
			this.text = this.text.substring(0, this.text.indexOf('--') + 2) + ' $for ' + formatCurrency(cost, true);
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
") ?>

