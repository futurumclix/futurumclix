<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('Cashout History')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<div class="uk-overflow-auto">
				<table class="uk-table uk-table-striped uk-table-small">
					<thead>
						<tr>
							<th><?=$this->Paginator->sort('User.username', __('Username'))?></th>
							<th><?=$this->Paginator->sort('Cashout.created', __('Date'))?></th>
							<th><?=$this->Paginator->sort('Cashout.amount', __('Amount'))?></th>
							<th><?=$this->Paginator->sort('Deposit.gateway', __('Method'))?></th>
							<th><?=$this->Paginator->sort('Deposit.status', __('Status'))?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($cashouts as $cashout): ?>
						<tr>
							<td><?=h($cashout['User']['username'])?></td>
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
