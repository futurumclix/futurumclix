<div class="container">
	<div class="row">
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
				<div class="col-md-6 text-xs-center">
					<p><?=__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla suscipit euismod enim.')?></p>
					<?=$this->Html->link(__('Open Support Ticket'), array('action' => 'add'), array('class' => 'btn btn-primary'))?>
				</div>
				<?=$this->UserForm->create(false, array('class' => 'form-horizontal'))?>
				<div class="col-md-6 text-xs-center">
					<?=$this->UserForm->input('ticket', array('class' => 'form-control', 'placeholder' => 'Enter Your Ticket ID'))?>
					<button class="btn btn-primary"><?=__('Check Ticket Status')?></button>
				</div>
				<?=$this->UserForm->end()?>
			</div>
		</div>
	</div>
</div>
