<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__d('offerwalls', 'Offerwalls Log')?></h2>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped uk-table-small">
					<thead>
						<tr>
							<th><?=$this->Paginator->sort('offerwall')?></th>
							<th><?=$this->Paginator->sort('transactionid', __d('offerwalls', 'Offer ID'))?></th>
							<th><?=$this->Paginator->sort('complete_date', __d('offerwalls', 'Date completed'))?></th>
							<th><?=$this->Paginator->sort('credit_date', __d('offerwalls', 'Credit date'))?></th>
							<th><?=$this->Paginator->sort('amount')?></th>
							<th><?=$this->Paginator->sort('status')?></th>
						</tr>
					</thead>
					<tbody>
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
				<div class="uk-text-right">
					<?=$this->Paginator->counter(array('format' => __('Page {:page} of {:pages}')))?>
				</div>
				<ul class="uk-pagination uk-flex-center">
					<?php
						echo $this->Paginator->first('&laquo;', array('tag' => 'li', 'escape' => false));
						echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'currentClass' => 'uk-active', 'currentTag' => 'a'));
						echo $this->Paginator->last('&raquo;', array('tag' => 'li', 'escape' => false));
						?>
				</ul>
			</div>
		</div>
	</div>
</div>
