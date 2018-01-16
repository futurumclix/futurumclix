<?php if(!isset($user)): ?>
	<div class="container">
		<div class="row">
			<div class="col-md-12 front_text">
				<?=$this->Notice->show()?>
				<h2 class="text-xs-center"><?=__('Manual Payment')?></h2>
				<h6><?=h($payment['title'])?></h6>
				<h6 class="margin30-top">Please transfer <?=h($payment['amount'])?> <?=h($payment['currency'])?> via <?=h($payment['gateway'])?> to <a href="mailto:<?=h($payment['to_account'])?>"><?=h($payment['to_account'])?></a></h6>
				<?=$this->UserForm->create(false)?>
				<button class="btn btn-primary margin30-top"><?=__('I have paid')?></button>
				<?=$this->UserForm->end()?>
			</div>
			<div class="clearfix"></div>
		</div>	
	</div>
</div>
<?php elseif(isset($user)): ?>
	<?=$this->element('userSidebar')?>
	<div class="page">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 padding30-sides">
					<?=$this->element('userBreadcrumbs')?>
					<?=$this->Notice->show()?>
					<div class="panel">
						<div class="padding30-col">
							<div class="col-md-12">
								<h5><?=__('Manual Payment')?></h5>
							</div>
							<div class="col-md-12 margin30-top">
								<h6><?=h($payment['title'])?></h6>
								<h6 class="margin30-top">Please transfer <?=h($payment['amount'])?> <?=h($payment['currency'])?> via <?=h($payment['gateway'])?> to <a href="mailto:<?=h($payment['to_account'])?>"><?=h($payment['to_account'])?></a></h6>
								<?=$this->UserForm->create(false)?>
								<button class="btn btn-primary margin30-top"><?=__('I have paid')?></button>
								<?=$this->UserForm->end()?>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>