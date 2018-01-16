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
							<h5><?=__d('offerwalls', 'Offerwalls Log')?></h5>
						</div>
						<div class="col-md-12 margin30-top table-responsive">
							<table class="table">
								<tbody>
									<tr>
										<th><?=$this->Paginator->sort('offerwall')?></th>
										<th><?=$this->Paginator->sort('transactionid', __d('offerwalls', 'Offer ID'))?></th>
										<th><?=$this->Paginator->sort('complete_date', __d('offerwalls', 'Date completed'))?></th>
										<th><?=$this->Paginator->sort('credit_date', __d('offerwalls', 'Credit date'))?></th>
										<th><?=$this->Paginator->sort('amount')?></th>
										<th><?=$this->Paginator->sort('status')?></th>
									</tr>
									<?php foreach($offers as $offer): ?>
									<tr>
										<td><?=h($offer['OfferwallsOffer']['offerwall'])?></td>
										<td><?=h($offer['OfferwallsOffer']['transactionid'])?></td>
										<td><?=$this->Time->nice($offer['OfferwallsOffer']['complete_date'])?></td>
										<td>
											<?php if($offer['OfferwallsOffer']['credit_date']): ?>
												<?=$this->Time->nice($offer['OfferwallsOffer']['credit_date'])?>
											<?php else:?>
												<?=$this->Time->nice($offer['OfferwallsOffer']['complete_date']." + {$options['OfferwallsMembership']['delay']} days")?>
											<?php endif; ?>
										</td>
										<td><?=$this->Currency->format(bcmul($offer['OfferwallsOffer']['amount'], $options['OfferwallsMembership']['point_ratio']))?></td>
										<td><?=$this->Utility->enum('Offerwalls.OfferwallsOffer', 'status', $offer['OfferwallsOffer']['status'])?></td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
							<div class="col-md-12 text-xs-right pagecounter">
								<?=$this->Paginator->counter(array('format' => __d('offerwalls', 'Page {:page} of {:pages}')))?>
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
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
