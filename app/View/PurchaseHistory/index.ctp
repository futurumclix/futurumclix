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
							<h5><?=__('Purchase history')?></h5>
						</div>
						<div class="col-md-12 margin30-top table-responsive">
							<table class="table table-condensed rentedrefstab">
								<tbody>
									<tr>
										<th><?=$this->Paginator->sort('date', __('Purchase Date'))?></th>
										<th><?=$this->Paginator->sort('amount', __('Payment'))?></th>
										<th><?=$this->Paginator->sort('title', __('Item'))?></th>
										<th><?=$this->Paginator->sort('method', __('Method'))?></th>
										<th><?=$this->Paginator->sort('status', __('Status'))?></th>
									</tr>
									<?php $i = 1; foreach($deposits as $deposit): ?>
									<tr>
										<td><?=h($deposit[0]['date'])?></td>
										<td><?=$this->Currency->format($deposit[0]['amount'])?></td>
										<td><?=h($deposit[0]['title'])?></td>
										<td><?=h(PaymentsInflector::humanize(PaymentsInflector::underscore($deposit[0]['method'])))?></td>
										<td><?=h($deposit[0]['status'])?></td>
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
