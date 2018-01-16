<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Search Tickets')?></h2>
	</div>
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Owner')?></label>
			<?=
				$this->AdminForm->input('Filter.Owner.username', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter ticket\'s owner to search'),
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Ticket ID')?></label>
			<?=$this->AdminForm->input('Filter.SupportTicket.id', array('type' => 'text'))?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Department')?></label>
			<?=$this->AdminForm->input('Filter.SupportTicket.department_id', array('empty' => __d('admin', 'All')))?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Status')?></label>
			<?=$this->AdminForm->input('Filter.SupportTicket.status', array('empty' => __d('admin', 'All')))?>
		</div>
		<div class="col-sm-12 text-center">
			<button class="btn btn-primary btn-sm"><?=__d('admin', 'Search')?></button>
		</div>
	<?=$this->AdminForm->end()?>
	<div class="clearfix"></div>
	<div class="title">
		<h2><?=__d('admin', 'Support tickets')?></h2>
	</div>
	<?=
		$this->AdminForm->create(false, array(
			'url' => array('action' => 'massaction'),
		))
	?>
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<tbody>
					<tr>
						<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
						<th><?=$this->Paginator->sort('Owner.username', __d('admin', 'Owner'))?></th>
						<th><?=$this->Paginator->sort('id', __d('admin', 'Ticket ID'))?></th>
						<th><?=$this->Paginator->sort('SupportDepartment.name', __d('admin', 'Department'))?></th>
						<th><?=$this->Paginator->sort('subject', __d('admin', 'Subject'))?></th>
						<th><?=__d('admin', 'Status')?></th>
						<th><?=$this->Paginator->sort('created', __d('admin', 'Created'))?></th>
						<th><?=$this->Paginator->sort('modified', __d('admin', 'Modified'))?></th>
						<th><?=__d('admin', 'Actions')?></th>
					</tr>
					<?php foreach($tickets as $ticket): ?>
					<tr>
						<td>
							<?=
								$this->AdminForm->checkbox('SupportTicket.'.$ticket['SupportTicket']['id'], array(
									'class' => 'ActionCheckbox',
								))
							?>
						</td>
						<td>
							<?php if(!$ticket['SupportTicket']['user_id']): ?>
								<?=h($ticket['SupportTicket']['full_name'])?>
							<?php elseif($ticket['Owner']['username']): ?>
								<?=$this->Html->link($ticket['Owner']['username'], array('controller' => 'users', 'action' => 'edit', $ticket['SupportTicket']['user_id']))?>
							<?php else: ?>
								<?=__d('admin', 'User deleted')?>
							<?php endif; ?>
						</td>
						<td><?=h($ticket['SupportTicket']['id'])?></td>
						<td><?=h($ticket['Department']['name'])?></td>
						<td><?=h($ticket['SupportTicket']['subject'])?></td>
						<td>
							<?php if($ticket['SupportTicket']['status'] == SupportTicket::OPEN): ?>
								<?php if($ticket['LastAnswer']['sender_flag'] == SupportTicketAnswer::ADMIN): ?>
									<?=__d('admin', 'Answered')?>
								<?php else: ?>
									<?=__d('admin', 'Awaitng Answer')?>
								<?php endif; ?>
							<?php else: ?>
								<?=h($ticket['SupportTicket']['status_enum'])?>
							<?php endif; ?>
						</td>
						<td><?=$this->Time->nice($ticket['SupportTicket']['created'])?></td>
						<td><?=$this->Time->nice($ticket['SupportTicket']['modified'])?></td>
						<td>
							<?=
								$this->Html->link('<i class="fa fa-comment fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'View ticket').'"></i>', array('action' => 'view',  $ticket['SupportTicket']['id']), array('escape' => false))
							?>
							<?=
								$this->AdminForm->postLink(
									'<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Close ticket').'"></i>',
									array('action' => 'close', $ticket['SupportTicket']['id']), 
									array('escape' => false), 
									__d('admin', 'Are you sure you want to close "%s"?', $ticket['SupportTicket']['subject'])
								);
							?>
							<?=
								$this->AdminForm->postLink(
									'<i class="fa fa-plus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Open ticket').'"></i>',
									array('action' => 'open', $ticket['SupportTicket']['id']), 
									array('escape' => false), 
									__d('admin', 'Are you sure you want to open "%s"?', $ticket['SupportTicket']['subject'])
								);
							?>
							<?=
								$this->AdminForm->postLink(
									'<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete ticket').'"></i>',
									array('action' => 'delete', $ticket['SupportTicket']['id']), 
									array('escape' => false), 
									__d('admin', 'Are you sure you want to delete "%s"?', $ticket['SupportTicket']['subject'])
								);
							?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="col-sm-5 text-left">
			<div class="input-group">
				<label for="selectMassAction" class="input-group-addon"><?=__d('admin', 'Mass Action')?></label>
				<?=
					$this->AdminForm->input('Action', array(
						'empty' => __d('admin', '--Choose--'),
						'required' => true,
						'id'=> 'selectMassAction',
						'options' => array(
							'close' => __d('admin', 'Mark as closed'),
							'open' => __d('admin', 'Mark as open'),
							'delete' => __d('admin', 'Delete'),
						)
					))
				?>
				<div class="input-group-btn">
					<button class="btn btn-danger"><?=__d('admin', 'Perform Action')?></button>
				</div>
			</div>
		</div>
	<?=$this->AdminForm->end()?>
	<div class="col-sm-7 text-right">
		<?=
			$this->Paginator->counter(array(
				'format' => __d('admin', 'Page {:page} of {:pages}')
			))
		?>
	</div>
	<div class="col-sm-12 text-center paddingten">
		<nav>
			<ul class="pagination pagination-sm">
				<?php
					echo $this->Paginator->first('&laquo;', array('tag' => 'li', 'escape' => false));
					echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'a'));
					echo $this->Paginator->last('&raquo;', array('tag' => 'li', 'escape' => false));
				?>
			</ul>
		</nav>
	</div>
</div>
