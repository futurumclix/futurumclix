<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Cashout History')?></h2>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped uk-table-small">
					<thead>
						<tr>
							<th><?=$this->Paginator->sort('Cashout.created', __('Date'))?></th>
							<th><?=$this->Paginator->sort('Cashout.amount', __('Amount'))?></th>
							<th><?=$this->Paginator->sort('Deposit.gateway', __('Method'))?></th>
							<th><?=$this->Paginator->sort('Deposit.status', __('Status'))?></th>
						</tr>
					</thead>
					<tbody>
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
			</div>
		</div>
	</div>
</div>
