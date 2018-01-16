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
				<div class="col-md-12 margin30-top">
					<div class="form-horizontal">
						<fieldset class="form-group">
							<label class="col-sm-2 control-label"><?=__('Ticket ID')?></label>
							<div class="col-sm-10">
								<input type="text" value="<?=h($ticket['SupportTicket']['id'])?>" class="form-control" disabled />
							</div>
						</fieldset>
						<fieldset class="form-group">
							<label class="col-sm-2 control-label"><?=__('Creation Time')?></label>
							<div class="col-sm-10">
								<input type="text" value="<?=h($this->Time->nice($ticket['SupportTicket']['created']))?>" class="form-control" disabled />
							</div>
						</fieldset>
						<fieldset class="form-group">
							<label class="col-sm-2 control-label"><?=__('Department')?></label>
							<div class="col-sm-10">
								<input type="text" value="<?=h($ticket['Department']['name'])?>" class="form-control" disabled />
							</div>
						</fieldset>
						<fieldset class="form-group">
							<label class="col-sm-2 control-label"><?=__('Subject')?></label>
							<div class="col-sm-10">
								<input type="text" value="<?=h($ticket['SupportTicket']['subject'])?>" class="form-control" disabled />
							</div>
						</fieldset>
						<fieldset class="form-group">
							<label class="col-sm-2 control-label"><?=__('Status')?></label>
							<div class="col-sm-10">
								<input type="text" value="<?=h($ticket['SupportTicket']['status_enum'])?>" class="form-control" disabled />
							</div>
						</fieldset>
						<fieldset class="form-group">
							<div class="col-md-12 conversation">
								<label class="label label-primary"><?=__('Owner at %s', $this->Time->nice($ticket['SupportTicket']['created']))?></label>
								<div class="panel panel-default">
									<div class="panel-body"><?=nl2br(h($ticket['SupportTicket']['message']))?></div>
								</div>
							</div>
						</fieldset>
						<?php foreach($ticket['Answers'] as $answer): ?>
							<fieldset class="form-group">
								<div class="col-md-12 conversation">
									<label class="label <?php if($answer['sender_flag'] == SupportTicketAnswer::OWNER): ?>label-primary<?php else: ?>label-danger<?php endif; ?>"><?=__('%s at %s', $senderFlag[$answer['sender_flag']], $this->Time->nice($answer['created']))?></label>
									<div class="panel panel-default">
										<div class="panel-body"><?=nl2br(h($answer['message']))?></div>
									</div>
								</div>
							</fieldset>
						<?php endforeach; ?>
					</div>
					<?php if($ticket['SupportTicket']['status'] != SupportTicket::CLOSED): ?>
						<?=$this->UserForm->create('SupportTicketAnswer', array('class' => 'form-horizontal'))?>
							<div class="form-group">
								<div class="col-md-12 conversation">
									<label class="label label-primary"><?=__('You')?></label>
									<?=$this->UserForm->input('message', array('class' => 'form-control'))?>
								</div>
							</div>
							<div class="col-md-12 text-xs-center padding30">
								<button class="btn btn-primary"><?=__('Answer')?></button>
							</div>
						<?=$this->UserForm->end()?>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</div>
</div>
