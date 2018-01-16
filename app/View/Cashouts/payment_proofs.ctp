<div class="container">
	<div class="row">
		<div class="col-md-12 front_text table-responsive margin30-top">
			<h2 class="text-xs-center"><?=__('Cashout History')?></h2>
			<table class="table table-condensed rentedrefstab margin30-top">
				<tbody>
					<tr>
						<th><?=$this->Paginator->sort('User.username', __('Username'))?></th>
						<th><?=$this->Paginator->sort('Cashout.created', __('Date'))?></th>
						<th><?=$this->Paginator->sort('Cashout.amount', __('Amount'))?></th>
						<th><?=$this->Paginator->sort('Deposit.gateway', __('Method'))?></th>
						<th><?=$this->Paginator->sort('Deposit.status', __('Status'))?></th>
					</tr>
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
	</div>
</div>
