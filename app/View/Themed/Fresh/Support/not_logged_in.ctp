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
		<div class="uk-width-1-2 uk-text-center">
			<p><?=__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla suscipit euismod enim.')?></p>
			<?=$this->Html->link(__('Open Support Ticket'), array('action' => 'add'), array('class' => 'uk-button uk-button-primary'))?>
		</div>
		<?=$this->UserForm->create(false, array('class' => 'uk-width-1-2 uk-text-center'))?>
		<div class="uk-margin">
			<?=$this->UserForm->input('ticket', array('class' => 'uk-input uk-width-1-1', 'placeholder' => 'Enter Your Ticket ID'))?>
		</div>
		<div class="uk-margin">
			<button class="uk-button uk-button-primary"><?=__('Check Ticket Status')?></button>
		</div>
	</div>
	<?=$this->UserForm->end()?>
</div>
