<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__d('revenue_share', 'Revenue Shares')?></h2>
			<?php if(empty($packets)): ?>
			<h5 class="uk-text-center"><?=__d('revenue_share', 'Currently you do not have any revenue shares.')?></h5>
			<?php else: ?>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-small">
					<thead>
						<tr>
							<th><?=$this->Paginator->sort('title', __d('revenue_share', 'Pack name'))?></th>
							<th><?=$this->Paginator->sort('created', __d('revenue_share', 'Start date'))?></th>
							<th><?=$this->Paginator->sort('running_days', __d('revenue_share', 'Running Days'))?></th>
							<th><?=$this->Paginator->sort('revenued', __d('revenue_share', 'Current income'))?></th>
							<th><?=$this->Paginator->sort('days_left', __d('revenue_share', 'Days left'))?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($packets as $packet): ?>
						<tr>
							<td>
								<?php if(isset($packet['RevenueShareOption']['title'])): ?>
								<?=h($packet['RevenueShareOption']['title'])?>
								<?php else: ?>
								<?=__d('revenue_share', 'Not longer available')?>
								<?php endif; ?>
							</td>
							<td><?=$this->Time->nice($packet['RevenueSharePacket']['created'])?></td>
							<td><?=h($packet['RevenueSharePacket']['running_days'])?></td>
							<td><?=$this->Currency->format($packet['RevenueSharePacket']['revenued'])?></td>
							<td><?=$packet['RevenueSharePacket']['days_left'] > 0 ? h($packet['RevenueSharePacket']['days_left']) : __d('revenue_share', 'Completed')?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<div class="uk-text-right uk-margin-top">
				<?=$this->Paginator->counter(array('format' => __('Page {:page} of {:pages}')))?>
			</div>
			<ul class="uk-pagination uk-flex-center">
				<?php
					echo $this->Paginator->first('&laquo;', array('tag' => 'li', 'class' => 'page-item', 'escape' => false));
					echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'class' => 'page-item', 'currentClass' => 'active', 'currentTag' => 'a'));
					echo $this->Paginator->last('&raquo;', array('tag' => 'li', 'class' => 'page-item', 'escape' => false));
					?>
			</ul>
			<?php endif;?>
			<?php if(!empty($limit) && $limit['RevenueShareLimit']['enabled']): ?>
			<?php if($this->Time->isFuture($next_purchase_date)): ?>
			<h5 class="uk-text-center"><?=__d('revenue_share', 'Currently you cannot buy more shares. You have to wait until %s to get more revenue shares.', $this->Time->nice($next_purchase_date))?></h5>
			<?php elseif($limit['RevenueShareLimit']['max_packs'] <= $packets_count && $limit['RevenueShareLimit']['max_packs'] != -1): ?>
			<h5 class="uk-text-center"><?=__d('revenue_share', 'Currently you cannot buy more shares as you have reached maximum amount of allowed revenue shares.')?></h5>
			<?php else: ?>
			<div class="uk-margin uk-text-center">
				<?=$this->Html->link('Buy more shares', array('action' => 'buy'), array('class' => 'uk-button uk-button-primary'))?>
			</div>
			<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
