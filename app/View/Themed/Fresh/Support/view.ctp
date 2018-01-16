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
	<div class="uk-flex-center" uk-grid>
		<div class="uk-width-1-1">
			<?=$this->Notice->show()?>
			<h2 class="uk-text-center"><?=__('Support Tickets')?></h2>
		</div>
		<div class="uk-width-5-6">
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Ticket ID')?></label>
				<input type="text" value="<?=h($ticket['SupportTicket']['id'])?>" class="uk-width-1-1 uk-input" disabled />
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Creation Time')?></label>
				<input type="text" value="<?=h($this->Time->nice($ticket['SupportTicket']['created']))?>" class="uk-width-1-1 uk-input" disabled />
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Department')?></label>
				<input type="text" value="<?=h($ticket['Department']['name'])?>" class="uk-width-1-1 uk-input" disabled />
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Subject')?></label>
				<input type="text" value="<?=h($ticket['SupportTicket']['subject'])?>" class="uk-width-1-1 uk-input" disabled />
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Status')?></label>
				<input type="text" value="<?=h($ticket['SupportTicket']['status_enum'])?>" class="uk-width-1-1 uk-input" disabled />
			</div>
			<div class="uk-margin">
				<label class="uk-badge"><?=__('Owner at %s', $this->Time->nice($ticket['SupportTicket']['created']))?></label>
				<div class="uk-card uk-card-body uk-card-default uk-card-hover">
					<?=nl2br(h($ticket['SupportTicket']['message']))?>
				</div>
			</div>
			<?php foreach($ticket['Answers'] as $answer): ?>
			<div class="uk-margin">
				<label class="label <?php if($answer['sender_flag'] == SupportTicketAnswer::OWNER): ?>uk-badge<?php else: ?>uk-badge admin<?php endif; ?>"><?=__('%s at %s', $senderFlag[$answer['sender_flag']], $this->Time->nice($answer['created']))?></label>
				<div class="uk-card uk-card-body uk-card-default uk-card-hover">
					<?=nl2br(h($answer['message']))?>
				</div>
			</div>
			<?php endforeach; ?>
			<?php if($ticket['SupportTicket']['status'] != SupportTicket::CLOSED): ?>
			<?=$this->UserForm->create('SupportTicketAnswer', array('class' => ''))?>
			<div class="uk-margin">
				<label class="uk-badge"><?=__('You')?></label>
				<?=$this->UserForm->input('message', array('class' => 'uk-textarea'))?>
			</div>
			<div class="uk-margin uk-text-center">
				<button class="uk-button uk-button-primary"><?=__('Answer')?></button>
			</div>
			<?=$this->UserForm->end()?>
			<?php endif; ?>
		</div>
	</div>
</div>
