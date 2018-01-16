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
					<?=$this->UserForm->create('SupportTicket', array('class' => 'form-horizontal'))?>
						<?php if(isset($user)): ?>
							<?=$this->UserForm->input('user_id', array('type' => 'hidden', 'value' => $user['User']['id']))?>
						<?php endif; ?>
						<fieldset class="form-group">
							<label class="col-sm-2 control-label"><?=__('Full Name')?></label>
							<div class="col-sm-10">
								<?=
									$this->UserForm->input('full_name', array(
										'class' => 'form-control',
										'default' => isset($user) ? h($user['User']['first_name']).' '.h($user['User']['last_name']) : null,
									))
								?>
							</div>
						</fieldset>
						<fieldset class="form-group">
							<label class="col-sm-2 control-label"><?=__('E-mail Address')?></label>
							<div class="col-sm-10">
								<?=
									$this->UserForm->input('email', array(
										'class' => 'form-control',
										'default' => isset($user) ? h($user['User']['email']) : null,
									))
								?>
							</div>
						</fieldset>
						<fieldset class="form-group">
							<label class="col-sm-2 control-label"><?=__('Department')?></label>
							<div class="col-sm-10">
								<?=
									$this->UserForm->input('department_id', array(
										'class' => 'form-control',
										'empty' => __('Please select...'),
									))
								?>
							</div>
						</fieldset>
						<fieldset class="form-group">
							<label class="col-sm-2 control-label"><?=__('Subject')?></label>
							<div class="col-sm-10">
								<?=
									$this->UserForm->input('subject', array(
										'class' => 'form-control',
									))
								?>
							</div>
						</fieldset>
						<fieldset class="form-group">
							<label class="col-sm-2 control-label"><?=__('Message')?></label>
							<div class="col-sm-10">
								<?=
									$this->UserForm->input('message', array(
										'class' => 'form-control',
										'rows' => 10,
									))
								?>
							</div>
						</fieldset>
						<div class="col-md-12 text-xs-center">
							<button class="btn btn-primary"><?=__('Send Support Ticket')?></button>
						</div>
					<?=$this->UserForm->end()?>
				</div>
			</div>
		</div>
	</div>
</div>
