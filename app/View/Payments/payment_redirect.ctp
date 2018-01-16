<?php if(!isset($user)): ?>
	<div class="container">
		<div class="row">
			<div class="col-md-12 front_text">
				<?=$this->Notice->show()?>
				<div class="padding30-col">
					<div class="col-md-12">
						<h5 class="text-xs-center"><?=__('Payment Redirection')?></h5>
					</div>
					<div class="col-md-12 margin30-top">
						<div class="alert alert-success" role="alert">
							<form method="POST" action="<?=$payment['url']?>" id="paymentForm">
								<?php foreach($payment['data'] as $k => $v): ?>
									<input type="hidden" name="<?=h($k)?>" value="<?=h($v)?>">
								<?php endforeach; ?>
								<?=__('You will be redirected to %s site in seconds, if not, please click %s', $payment['gateway'], '<button class="btn btn-sm btn-primary">'.__('here').'</button>')?>
							</form>
						</div>
					</div>
					<div class="clearfix"></div>
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
				<?=$this->element('userBreadcrumbs')?>
				<?=$this->Notice->show()?>
				<div class="panel">
					<div class="padding30-col">
						<div class="col-md-12">
							<h5><?=__('Payment Redirection')?></h5>
						</div>
						<div class="col-md-12 margin30-top">
							<div class="alert alert-success" role="alert">
								<form method="POST" action="<?=$payment['url']?>" id="paymentForm">
									<?php foreach($payment['data'] as $k => $v): ?>
										<input type="hidden" name="<?=h($k)?>" value="<?=h($v)?>">
									<?php endforeach; ?>
									<?=__('You will be redirected to %s site in seconds, if not, please click %s', $payment['gateway'], '<button class="btn btn-sm btn-primary">'.__('here').'</button>')?>
								</form>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
<?php 
	$this->Js->buffer('
		setTimeout(function(){$("#paymentForm").submit();}, 1);
	') 
?>
