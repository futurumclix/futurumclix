<?php if(!isset($user)): ?>
	<div class="container">
		<div class="row">
			<div class="col-md-12 front_text">
				<?=$this->Notice->show()?>
				<div class="padding30-col">
					<div class="col-md-12">
						<h5 class="text-xs-center"><?=__('Payment Successful')?>: <?=$gateway?> - <?=$title?></h5>
					</div>
					<div class="col-md-12 margin30-top">
						<div class="alert alert-success" role="alert">
							<strong><?=__('Payment successful.')?></strong> <?=__('Thanks, your payment is successful and all services are added to your account now.')?>
						</div>
					</div>
				</div>	
			</div>
		</div>
	</div>
<?php elseif(isset($user)): ?>
	<?=$this->element('userSidebar')?>
	<div class="page">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 padding30-sides">
					<?php $this->Chart->emitScripts()?>
					<?=$this->element('userBreadcrumbs')?>
					<?=$this->Notice->show()?>
					<div class="panel">
						<div class="padding30-col">
							<div class="col-md-12">
								<h5><?=__('Payment Successful')?>: <?=$gateway?> - <?=$title?></h5>
							</div>
							<div class="col-md-12 margin30-top">
								<div class="alert alert-success" role="alert">
									<strong><?=__('Payment successful.')?></strong> <?=__('Thanks, your payment is successful and all services are added to your account now.')?>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
