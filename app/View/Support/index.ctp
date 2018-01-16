<div class="container">
	<div class="row">
		<div class="col-md-12 panel margin30-top padding30">
			<div class="col-md-12 front_text">
				<div class="col-md-12 title">
					<h2><?=__('Support Tickets')?></h2>
				</div>
				<div class="row">
					<div class="col-md-12">
						<?=$this->Notice->show()?>
					</div>
				</div>
				<?php if(empty($tickets)): ?>
				<div class="col-md-12">
					<p><?=__('Lorem ipsum dolor sit amet, consectetur adipiscing elit.')?></p>
				</div>
				<?php else: ?>
				<div class="col-md-12">
					<p><?=__('Your Support Tickets')?></p>
					<div class="table-responsive">
						<table class="table">
							<tbody>
								<tr>
									<th><?=$this->Paginator->sort('id', __('Ticket ID'))?></th>
									<th><?=$this->Paginator->sort('subject')?></th>
									<th><?=$this->Paginator->sort('status')?></th>
									<th><?=$this->Paginator->sort('created')?></th>
									<th class="text-xs-center"><?=__('Actions')?></th>
								</tr>
								<?php foreach($tickets as $ticket): ?>
								<tr>
									<td><?=h($ticket['SupportTicket']['id'])?></td>
									<td><?=h($ticket['SupportTicket']['subject'])?></td>
									<td><?=h($ticket['SupportTicket']['status_enum'])?></td>
									<td><?=$this->Time->nice($ticket['SupportTicket']['created'])?></td>
									<td class="text-xs-center">
										<?=
											$this->Html->link('<i class="fa fa-comment fa-lg" data-toggle="tooltip" data-placement="top" title="'.__('View ticket').'"></i>', array('action' => 'view',  $ticket['SupportTicket']['id']), array('escape' => false))
											?>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="col-sm-12 text-xs-right pagecounter">
					<?=
						$this->Paginator->counter(array(
							'format' => __('Page {:page} of {:pages}')
						))
						?>
				</div>
				<div class="col-sm-12 text-xs-center">
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
				<div class="col-md-12 text-xs-center padding30">
					<?=$this->Html->link(__('Open Support Ticket'), array('action' => 'add'), array('class' => 'btn btn-primary'))?>
				</div>
			</div>
		</div>
	</div>
</div>
