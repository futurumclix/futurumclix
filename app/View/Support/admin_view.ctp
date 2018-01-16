<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Ticket %s', $ticket['SupportTicket']['id'])?></h2>
	</div>
	<div class="form-horizontal">
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=__d('admin', 'Name')?></label>
			<div class="col-sm-4">
				<input type="text" class="form-control" value="<?=h($ticket['SupportTicket']['full_name'])?>" disabled />
			</div>
		</div>
		<?php if($ticket['SupportTicket']['user_id']): ?>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=__d('admin', 'Username')?></label>
			<div class="col-sm-4">
				<?php if($ticket['Owner']['username']): ?>
					<?=$this->Html->link($ticket['Owner']['username'], array('plugin' => null, 'controller' => 'users', 'action' => 'edit', $ticket['SupportTicket']['user_id']), array('class' =>'form-control'))?>
				<?php else: ?>
					<?=__d('admin', 'User deleted')?>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=__d('admin', 'Created')?></label>
			<div class="col-sm-4">
				<input type="text" class="form-control" value="<?=h($this->Time->nice($ticket['SupportTicket']['created']))?>" disabled />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=__d('admin', 'Last Action')?></label>
			<div class="col-sm-4">
				<input type="text" class="form-control" value="<?=h($this->Time->nice($ticket['SupportTicket']['last_action']))?>" disabled />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=__d('admin', 'Status')?></label>
			<div class="col-sm-4">
				<input type="text" class="form-control" value="<?=h($ticket['SupportTicket']['status_enum'])?>" disabled />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=__d('admin', 'Subject')?></label>
			<div class="col-sm-4">
				<input type="text" class="form-control" value="<?=h($ticket['SupportTicket']['subject'])?>" disabled />
			</div>
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'Messages')?></h2>
	</div>
	<div class="form-horizontal">
		<div class="form-group">
			<div class="col-md-12 conversation">
				<label class="label label-primary"><?=__d('admin', 'Owner At %s', $this->Time->nice($ticket['SupportTicket']['created']))?></label>
				<div class="panel panel-default">
					<div class="panel-body"><?=nl2br(h($ticket['SupportTicket']['message']))?></div>
				</div>
			</div>
		</div>
		<?php foreach($ticket['Answers'] as $answer): ?>
			<div class="form-group">
				<div class="col-md-12 conversation">
					<label class="label <?php if($answer['sender_flag'] == SupportTicketAnswer::OWNER): ?>label-primary<?php else: ?>label-danger<?php endif; ?>"><?=__d('admin', '%s at %s', $senderFlag[$answer['sender_flag']], $this->Time->nice($answer['created']))?></label>
					<div class="panel panel-default">
						<div class="panel-body"><?=nl2br(h($answer['message']))?></div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'Reply')?></h2>
	</div>
	<div class="form-horizontal">
		<div class="text-center paddingten"><label class="label label-danger"><?=__d('admin', 'Insert Canned Response')?><?=__d('admin', '')?></label></div>
		<div class="form-group">
			<div class="col-md-6 col-md-offset-3">
				<?=$this->AdminForm->input('cannedAnswer', array('class' => 'form-control', 'empty' => __d('admin', 'Please select...')))?>
			</div>
		</div>
		<div class="text-center paddingten"><label class="label label-primary"><?=__d('admin', 'Insert Variables')?></label></div>
		<div class="btn-toolbar paddingten text-center" role="toolbar">
			<div class="btn-group" role="group" style="float: inherit;">
				<?php foreach($variables as $variable => $name): ?>
					<button data-variable="<?=h($variable)?>" data-textarea="SupportTicketAnswerMessage" type="button" class="btn btn-info"><?=h($name)?></button>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?=$this->AdminForm->create('SupportTicketAnswer')?>
		<div class="form-group">
			<div class="col-md-12">
				<?=$this->AdminForm->input('message')?>
			</div>
		</div>
		<div class="text-center">
			<button class="btn btn-primary"><?=__d('admin', 'Send')?></button>
			<?=$this->AdminForm->button(__d('admin', 'Send And Close'), array(
				'class' => 'btn btn-primary',
				'value' => '1',
				'name' => 'close',
			));?>
		</div>
	<?=$this->AdminForm->end()?>
</div>
<?php $this->Js->buffer("
	$('button[data-textarea][data-variable]').click(function() {
		var id = '#' + $(this).data('textarea');
		var cursorPos = $(id).prop('selectionStart');
		var v = $(id).val();
		var textBefore = v.substring(0,  cursorPos);
		var textAfter  = v.substring(cursorPos, v.length);

		$(id).val(textBefore + $(this).data('variable') + textAfter);
		$(id).prop('selectionStart', cursorPos + $(this).data('variable').length);
	});
	var cannedAnswers = $cannedAnswersData;
	$('#cannedAnswer').change(function() {
		$('#SupportTicketAnswerMessage').val(cannedAnswers[$(this).val()]['message']);
	});
")?>
