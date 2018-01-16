<?=$this->element('userSidebar')?>
<div class="page">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 padding30-sides">
				<?=$this->element('userBreadcrumbs')?>
				<?=$this->Notice->show()?>
				<div class="panel">
					<div class="padding30-col">
						<div class="col-md-12 paidoffers">
							<h5><?=__d('revenue_share', 'Revenue Shares')?></h5>
							<?php if(empty($packets)): ?>
							<div class="row">
								<div class="col-md-12 margin30-top">
									<h6 class="text-xs-center"><?=__d('revenue_share', 'Currently you do not have any revenue shares.')?></h6>
								</div>
							</div>
							<?php else: ?>
							<div class="table-responsive margin30-top">
								<table class="table">
									<tbody>
										<tr>
											<th><?=$this->Paginator->sort('title', __d('revenue_share', 'Pack name'))?></th>
											<th><?=$this->Paginator->sort('created', __d('revenue_share', 'Start date'))?></th>
											<th><?=$this->Paginator->sort('running_days', __d('revenue_share', 'Running Days'))?></th>
											<th><?=$this->Paginator->sort('revenued', __d('revenue_share', 'Current income'))?></th>
											<th><?=$this->Paginator->sort('days_left', __d('revenue_share', 'Days left'))?></th>
										</tr>
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
							<div class="col-md-12 text-xs-right pagecounter">
								<?=$this->Paginator->counter(array('format' => __d('revenue_share', 'Page {:page} of {:pages}')))?>
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
							<?php endif;?>
							<?php if(!empty($limit) && $limit['RevenueShareLimit']['enabled']): ?>
							<?php if($this->Time->isFuture($next_purchase_date)): ?>
							<div class="row">
								<div class="col-md-12">
									<h6 class="text-xs-center"><?=__d('revenue_share', 'Currently you cannot buy more shares. You have to wait until %s to get more revenue shares.', $this->Time->nice($next_purchase_date))?></h6>
								</div>
							</div>
							<?php elseif($limit['RevenueShareLimit']['max_packs'] <= $packets_count && $limit['RevenueShareLimit']['max_packs'] != -1): ?>
							<div class="row">
								<div class="col-md-12">
									<h6 class="text-xs-center"><?=__d('revenue_share', 'Currently you cannot buy more shares as you have reached maximum amount of allowed revenue shares.')?></h6>
								</div>
							</div>
							<?php else: ?>
							<div class="row">
								<div class="col-md-12 text-xs-center margin30-top">
									<?=$this->Html->link('Buy more shares', array('action' => 'buy'), array('class' => 'btn btn-primary'))?>
								</div>
							</div>
							<?php endif; ?>
							<?php endif; ?>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
