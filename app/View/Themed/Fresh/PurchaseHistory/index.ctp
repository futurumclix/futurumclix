<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Purchase history')?></h2>
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped uk-table-small">
					<thead>
						<tr>
							<th><?=$this->Paginator->sort('date', __('Purchase Date'))?></th>
							<th><?=$this->Paginator->sort('amount', __('Payment'))?></th>
							<th><?=$this->Paginator->sort('title', __('Item'))?></th>
							<th><?=$this->Paginator->sort('method', __('Method'))?></th>
							<th><?=$this->Paginator->sort('status', __('Status'))?></th>
						</tr>
					</thead>
					<tbody>
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
