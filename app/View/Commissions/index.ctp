<?=$this->element('userSidebar')?>
<div class="page">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 padding30-sides">
				<?=$this->element('userBreadcrumbs')?>
				<?=$this->Notice->show()?>
				<div class="panel">
					<div class="padding30-col">
						<div class="col-md-12">
							<h5><?=__('Referral\'s commissions')?></h5>
						</div>
						<div class="col-md-12 margin30-top table-responsive">
							<table class="table">
								<tbody>
									<tr>
										<th><?=$this->Paginator->sort('Deposit.date', __('Purchase Date'))?></th>
										<th><?=$this->Paginator->sort('credit_date')?></th>
										<th><?=$this->Paginator->sort('Referral.username', __('Referral'))?></th>
										<th><?=$this->Paginator->sort('amount', __('Amount'))?></th>
										<th><?=$this->Paginator->sort('Deposit.title', __('Item'))?></th>
										<th><?=$this->Paginator->sort('status')?></th>
									</tr>
									<?php foreach($commissions as $commission): ?>
									<tr>
										<td><?=h($commission['Deposit']['date'])?></td>
										<td><?=h($commission['Commission']['credit_date'])?></td>
										<td><?=h($commission['Referral']['username'])?></td>
										<td><?=h($this->Currency->format($commission['Commission']['amount']))?></td>
										<td><?=h($commission['Deposit']['title'])?></td>
										<td><?=h($commission['Commission']['status'])?></td>
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
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
