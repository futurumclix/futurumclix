<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('Support Tickets')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<?=$this->Notice->show()?>
			<h2 class="uk-text-center"><?=__('Support Tickets')?></h2>
			<?php if(empty($tickets)): ?>
			<p class="uk-text-center"><?=__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.')?></p>
			<?php else: ?>
			<p><?=__('Your Support Tickets')?></p>
			<div class="uk-overflow-auto">
				<table class="uk-table">
					<thead>
						<tr>
							<th><?=$this->Paginator->sort('id', __('Ticket ID'))?></th>
							<th><?=$this->Paginator->sort('subject')?></th>
							<th><?=$this->Paginator->sort('status')?></th>
							<th><?=$this->Paginator->sort('created')?></th>
							<th class="uk-text-center"><?=__('Actions')?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($tickets as $ticket): ?>
						<tr>
							<td><?=h($ticket['SupportTicket']['id'])?></td>
							<td><?=h($ticket['SupportTicket']['subject'])?></td>
							<td><?=h($ticket['SupportTicket']['status_enum'])?></td>
							<td><?=$this->Time->nice($ticket['SupportTicket']['created'])?></td>
							<td class="uk-text-center">
								<?=
									$this->Html->link('<i class="mdi mdi-18px mdi-comment-account" uk-tooltip title="'.__('View ticket').'"></i>', array('action' => 'view',  $ticket['SupportTicket']['id']), array('escape' => false))
									?>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<div class="uk-margin uk-text-right">
				<?=
					$this->Paginator->counter(array(
						'format' => __('Page {:page} of {:pages}')
					))
					?>
			</div>
			<div class="uk-margin uk-text-center">
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
			<?php endif; ?>
			<div class="uk-margin uk-text-center">
				<?=$this->Html->link(__('Open Support Ticket'), array('action' => 'add'), array('class' => 'uk-button uk-button-primary'))?>
			</div>
		</div>
	</div>
</div>
