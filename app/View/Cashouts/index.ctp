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
							<h5><?=__('Cashout History')?></h5>
						</div>
						<div class="col-md-12 margin30-top table-responsive">
							<table class="table table-condensed rentedrefstab">
								<tbody>
									<tr>
										<th><?=$this->Paginator->sort('Cashout.created', __('Date'))?></th>
										<th><?=$this->Paginator->sort('Cashout.amount', __('Amount'))?></th>
										<th><?=$this->Paginator->sort('Deposit.gateway', __('Method'))?></th>
										<th><?=$this->Paginator->sort('Deposit.status', __('Status'))?></th>
									</tr>
									<?php foreach($cashouts as $cashout): ?>
										<tr>
											<td><?=$this->Time->nice($cashout['Cashout']['created'])?></td>
											<td><?=$this->Currency->format($cashout['Cashout']['amount'])?></td>
											<td><?=h(PaymentsInflector::humanize(PaymentsInflector::underscore($cashout['Cashout']['gateway'])))?></td>
											<td><?=h($cashout['Cashout']['status'])?></td>
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
