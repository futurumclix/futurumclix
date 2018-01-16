<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__d('revenue_share', 'Buy revenue shares')?></h2>
			<h5 class="uk-text-center">
				<?php if($limit == -1): ?>
				<?=__d('revenue_share', 'As a %s member you have no limit on revenue shares.', $user['ActiveMembership']['Membership']['name'])?>
				<?php else: ?>
				<?=__d('revenue_share', 'As a %s member your revenue share packs limit is: %d.', $user['ActiveMembership']['Membership']['name'], $limit)?>
				<?php endif; ?>
			</h5>
			<h6 class="uk-text-center"><?=__d('revenue_share', 'Choose revenue share pack')?>:</h6>
			<div class="uk-margin uk-child-width-1-2@s uk-child-width-1-3@m" id="packsList" uk-grid>
				<?php foreach($optionsData as $pack): ?>
				<?php
					$income = bcmul(bcdiv($pack['RevenueShareOption']['overall_return'], '100'), $pack['RevenueShareOption']['price']);
					$perDay = bcdiv($income, $pack['RevenueShareOption']['running_days']);
					$perStep = bcdiv($perDay, 24 * 60 / $pack['RevenueShareOption']['step']);
					?>
				<div>
					<div class="uk-card uk-card-body uk-card-default uk-text-center" uk-tooltip title="<?=__d('revenue_share', 'Advertisements included in this pack: %s', h(implode($pack['RevenueShareOption']['items'], ', ')))?>">
						<h5><?=__d('revenue_share', '%s', $pack['RevenueShareOption']['title'])?></h5>
						<table class="uk-table uk-table-small">
							<tr>
								<td><?=__d('revenue_share', 'Running days:')?></td>
								<td><?=$pack['RevenueShareOption']['running_days'];?></td>
							</tr>
							<tr>
								<td><?=__d('revenue_share', 'Max running days:')?></td>
								<td><?=$pack['RevenueShareOption']['running_days_max'];?></td>
							</tr>
							<tr>
								<td><?=__d('revenue_share', 'Overall return:')?></td>
								<td><?=$pack['RevenueShareOption']['overall_return'];?></td>
							</tr>
							<tr>
								<td><?=__d('revenue_share', 'Income credit every:')?></td>
								<td><?=$pack['RevenueShareOption']['step'];?></td>
							</tr>
							<tr>
								<td><?=__d('revenue_share', 'Expected Income Per Step:')?></td>
								<td><?=$this->Currency->format($perStep)?></td>
							</tr>
							<tr>
								<td><?=__d('revenue_share', 'Expected Income Per Day:')?></td>
								<td><?=$this->Currency->format($perDay)?></td>
							</tr>
							<tr>
								<td><?=__d('revenue_share', 'Expected Overall Income:')?></td>
								<td><?=$this->Currency->format($income)?></td>
							</tr>
							<tr>
								<td colspan="2">
									<h5><?=__d('revenue_share', 'Pack price:')?><br /><?=$this->Currency->format($pack['RevenueShareOption']['prices']['PurchaseBalance']);?></h5>
								</td>
							</tr>
						</table>
						<button class="uk-button uk-button-primary" data-packid="<?=$pack['RevenueShareOption']['id']?>"><?=__d('revenue_share', 'Buy')?></button>
					</div>
				</div>
				<?php endforeach; reset($optionsData);?>
			</div>
			<?=$this->UserForm->create(false, array('class' => 'uk-form-stacked', 'style' => 'display:none'))?>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__d('revenue_share', 'Choose revenue share pack')?></label>
				<?=$this->UserForm->input('option_id', array('class' => 'uk-select', 'id' => 'packSelect'))?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__d('revenue_share', 'Packs amount')?></label>
				<?=
					$this->UserForm->input('amount', array(
						'type' => 'number',
						'class' => 'uk-input',
						'min' => 1,
						'max' => $can_buy == 'unlimited' ? '' : $can_buy,
						'step '=> 1,
					));
					?>
				<p><?=__d('revenue_share', 'Max allowed: %s', $can_buy)?></p>
				<div class="uk-margin uk-text-center">
					<?=$this->UserForm->getGatewaysButtons($activeGateways, array('prices' => $optionsData[key($optionsData)]['RevenueShareOption']['prices']))?>
				</div>
				<?=$this->UserForm->end()?>
			</div>
		</div>
	</div>
</div>
<?php
	$optionsData = json_encode($optionsData);
	$minimumDeposits = json_encode($minimumDeposits);
	$ignoreMinDeposit = $ignoreMinDeposit ? 'true' : 'false';
	
	if($can_buy == 'unlimited') {
		$can_buy = -1;
	}
	
	$this->Js->buffer("
		var Data = $optionsData;
		var Minimum = $minimumDeposits;
		var IgnoreMin = $ignoreMinDeposit;
		var CanBuy = $can_buy;
	
		function selectPack() {
			$('#packSelect').val($(this).data('packid'));
			$('#buyForm').show();
			$('#packsList').hide();
		}
	
		function calcPrice() {
			var pack_id = $('#packSelect').val();
			var amount = $('#amount').val();
	
			if(!amount) {
				amount = 0;
			}
	
			if(amount > CanBuy && CanBuy != -1) {
				return;
			}
	
			price = new Big(Data[pack_id]['RevenueShareOption']['price']).mul(new Big(amount));
	
			$('#price').attr('placeholder', formatCurrency(price));
	
			$.each(Data[pack_id]['RevenueShareOption']['prices'], function(key, value) {
				price = new Big(value).mul(new Big(amount));
	
				if(IgnoreMin || (price.cmp(Minimum[key]) >= 0 && price.cmp(0) == 1)) {
					$('.' + key + 'Price').parent().show();
					$('.' + key + 'Price').html(formatCurrency(price));
				} else {
					$('.' + key + 'Price').parent().hide();
				}
			});
		}
	
		$('#packSelect').change(calcPrice);
		$('#amount').on('change input', calcPrice);
		$('[data-packid]').on('click', selectPack);
		calcPrice();
	");
	?>
