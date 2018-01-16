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
			<?=$this->UserForm->create('SupportTicket', array('class' => 'uk-form-stacked'))?>
			<?php if(isset($user)): ?>
			<?=$this->UserForm->input('user_id', array('type' => 'hidden', 'value' => $user['User']['id']))?>
			<?php endif; ?>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Full Name')?></label>
				<?=
					$this->UserForm->input('full_name', array(
						'class' => 'uk-width-1-1 uk-input',
						'default' => isset($user) ? h($user['User']['first_name']).' '.h($user['User']['last_name']) : null,
					))
					?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('E-mail Address')?></label>
				<?=
					$this->UserForm->input('email', array(
						'class' => 'uk-width-1-1 uk-input',
						'default' => isset($user) ? h($user['User']['email']) : null,
					))
					?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Department')?></label>
				<?=
					$this->UserForm->input('department_id', array(
						'class' => 'uk-width-1-1 uk-select',
						'empty' => __('Please select...'),
					))
					?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Subject')?></label>
				<?=
					$this->UserForm->input('subject', array(
						'class' => 'uk-width-1-1 uk-input',
					))
					?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Message')?></label>
				<?=
					$this->UserForm->input('message', array(
						'class' => 'uk-width-1-1 uk-textarea',
						'rows' => 10,
					))
					?>
			</div>
			<div class="uk-margin uk-text-center">
				<button class="uk-button uk-button-primary"><?=__('Send Support Ticket')?></button>
			</div>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
