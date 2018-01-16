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
							<h6><?=__('You have a pending deposit of')?> <?=h($this->Currency->format($deposit['Deposit']['amount']))?></h6>
							<h6><?=__('You have submitted your payment at:')?> <?=h($deposit['Deposit']['date'])?></h6>
							<h6><?=__('Please wait 24 hours for deposit to be credited on your account.')?></h6>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
