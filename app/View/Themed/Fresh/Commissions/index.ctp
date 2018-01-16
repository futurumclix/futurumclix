<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Referral\'s commissions')?></h2>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped uk-table-small">
					<thead>
						<tr>
							<th><?=$this->Paginator->sort('Deposit.date', __('Purchase Date'))?></th>
							<th><?=$this->Paginator->sort('credit_date')?></th>
							<th><?=$this->Paginator->sort('Referral.username', __('Referral'))?></th>
							<th><?=$this->Paginator->sort('amount', __('Amount'))?></th>
							<th><?=$this->Paginator->sort('Deposit.title', __('Item'))?></th>
							<th><?=$this->Paginator->sort('status')?></th>
						</tr>
					</thead>
					<tbody>
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
