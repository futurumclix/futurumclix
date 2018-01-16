<?=$this->element('userBreadcrumbs')?>
<?php $this->Chart->emitScripts()?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Rented Referrals')?></h2>
			<div class="uk-child-width-1-4@m uk-grid-small uk-grid-match" uk-grid>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('Purchase balance')?></h6>
						<div class="uk-child-width-1-2@m uk-grid-collapse" uk-grid>
							<div>
								<h3><?=h($this->Currency->format($user['User']['purchase_balance']))?></h3>
							</div>
							<div class="uk-text-right">
								<?=
									$this->Html->link('<i class="mdi mdi-18px mdi-chevron-up"></i>', array('controller' => 'users', 'action' => 'deposit'), array(
										'title' => __('Add funds'),
										'uk-tooltip' => '',
										'escape' => false,
									))
									?>
							</div>
						</div>
					</div>
				</div>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('AutoPay')?></h6>
						<div class="uk-child-width-1-2@m uk-grid-collapse" uk-grid>
							<div>
								<h3><?=$user['User']['autopay_enabled'] ? __('Enabled') : __('Disabled')?></h3>
							</div>
							<div class="uk-text-right">
								<a data-ajaxsource="<?=$this->Html->url(array('action' => 'autopayStats'))?>"><i uk-tooltip="" title="<?=__('Statistics')?>" class="mdi mdi-18px mdi-chart-bar"></i></a>
								<?php if($user['User']['autopay_enabled']): ?>
								<?=
									$this->UserForm->postLink('<i uk-tooltip title="'.__('Disable AutoPay').'" class="mdi mdi-18px mdi-plus"></i>',
										array('action' => 'setAutopay', 0),
										array('escape' => false),
										__('Are you sure you want to disable AutoPay?')
									)
									?>
								<?php else: ?>
								<?=
									$this->UserForm->postLink('<i uk-tooltip title="'.__('Enable AutoPay').'" class="mdi mdi-18px mdi-minus"></i>',
										array('action' => 'setAutopay', 1),
										array('escape' => false),
										__('Are you sure you want to enable AutoPay?')
									)
									?>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
				<?php if($user['User']['auto_renew_days'] == 0):?>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('AutoRenew')?></h6>
						<div id="autoRenewForm" style="display:none">
							<?=$this->UserForm->create('User', array('class' => 'uk-form-stacked', 'url' => array('action' => 'autoRenew')))?>
							<div class="uk-margin uk-text-center">
								<label class="uk-form-label"><?=__('Period:')?></label>
								<?=$this->UserForm->input('auto_renew_extend', array('class' => 'uk-select', 'default' => $user['User']['auto_renew_extend']))?>
								<label class="uk-form-label"><?=__('Days:')?></label>
								<?=$this->UserForm->input('auto_renew_days', array('class' => 'uk-select', 'default' => $user['User']['auto_renew_days']))?>
								<button class="uk-button uk-button-small uk-button-primary"><?=__('Enable')?></button>
							</div>
							<?=$this->UserForm->end()?>
						</div>
						<div class="uk-child-width-1-2@m uk-grid-collapse" uk-grid>
							<div>
								<h3 id="autoRenewDisabledInfo"><?=__('Disabled')?></h3>
							</div>
							<div class="uk-text-right">
								<a uk-toggle="target: #ajax-modal" data-ajaxsource="<?=$this->Html->url(array('action' => 'autorenewStats'))?>"><i uk-tooltip title="<?=__('Statistics')?>" class="mdi mdi-18px mdi-chart-bar"></i></a>
								<a><i uk-tooltip title="<?=__('Enable AutoRenew')?>" class="mdi mdi-18px mdi-minus" onclick="$('#autoRenewForm').show();$(this).hide();$('#autoRenewDisabledInfo').hide();"></i></a>
							</div>
						</div>
					</div>
				</div>
				<?php else: ?>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('AutoRenew')?></h6>
						<div class="uk-child-width-1-2@m uk-grid-collapse" uk-grid>
							<div>
								<h3><?=__('%d/%d', $user['User']['auto_renew_extend'], $user['User']['auto_renew_days'])?></h3>
							</div>
							<div class="uk-text-right">
								<a data-toggle="modal" data-target="#ajax-modal-container" data-ajaxsource="<?=$this->Html->url(array('action' => 'autorenewStats'))?>"><i uk-tooltip title="<?=__('Statistics')?>" class="mdi mdi-18px mdi-chart-bar"></i></a>
								<?=
									$this->UserForm->postLink('<i uk-tooltip title="'.__('Disable AutoRenew').'" class="mdi mdi-18px mdi-plus"></i>',
										array('action' => 'autoRenew'),
										array('escape' => false),
										__('Are you sure you want to disable AutoRenew?')
									)
									?>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('Rented referrals')?></h6>
						<div class="uk-child-width-1-2@m uk-grid-collapse" uk-grid>
							<div>
								<h3><?=h($user['User']['rented_refs_count'])?></h3>
							</div>
							<div class="uk-text-right">
								<?=
									$this->Html->link('<i class="mdi mdi-18px mdi-chevron-up"></i>', array('action' => 'rentReferrals'), array(
										'title' => __('Rent more referrals'),
										'data-toggle' => 'tooltip',
										'data-placement' => 'left',
										'escape' => false,
									))
									?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php if(empty($refs)): ?>
			<div class="uk-text-center uk-margin-top">
				<?=__('Currently you don\'t have any rented referrals. To rent some please go %s.', $this->Html->link('here', array('action' => 'rentReferrals')))?>
			</div>
			<?php else: ?>
			<div class="uk-overflow-auto">
				<?=$this->UserForm->create(false)?>
				<table class="uk-table uk-table-striped uk-table-small">
					<thead>
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
					</thead>
					<tbody>
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
								<a uk-toggle="target: #ajax-modal" data-ajaxsource="<?=$this->Html->url(array('action' => 'rentedReferralStats', $ref['User']['url_id']))?>"><i uk-tooltip title="<?=__('Show this referral statistics')?>" class="mdi mdi-information"></i></a>  
								<?=
									$this->UserForm->postLink('<i uk-tooltip title="'.__('Recycle this referral').'" class="mdi mdi-recycle"></i>',
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
				<div class="uk-text-right">
					<?=$this->Paginator->counter(array('format' => __('Page {:page} of {:pages}')))?>
				</div>
				<ul class="uk-pagination uk-flex-center">
					<?php
						echo $this->Paginator->first('&laquo;', array('tag' => 'li', 'class' => 'page-item', 'escape' => false));
						echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'class' => 'page-item', 'currentClass' => 'active', 'currentTag' => 'a'));
						echo $this->Paginator->last('&raquo;', array('tag' => 'li', 'class' => 'page-item', 'escape' => false));
						?>
				</ul>
				<div id="refsActionDiv" class="uk-flex uk-flex-center uk-text-center" style="display: none">
					<div class="uk-card uk-card-body uk-width-1-2@m">
						<p><?=__('What would you like to do with selected referrals?')?></p>
						<?=
							$this->UserForm->input('action', array(
								'id' => 'refsActionSelect',
								'options' => $options,
								'empty' => __('Please choose'),
								'class' => 'uk-select',
							))
							?>
						<button class="uk-button uk-button-primary"><?=__('Continue')?></button>
					</div>
					<?=$this->UserForm->end()?>
				</div>
				<?php endif; ?>
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
			if(Costs == = null) {
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
