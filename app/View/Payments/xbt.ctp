<?php if(!isset($user)): ?>
	<div class="container">
		<div class="row">
			<div class="col-md-12 front_text">
				<?=$this->Notice->show()?>
				<div class="padding30-col">
					<div class="col-md-12">
						<h5 class="text-xs-center"><?=isset($data['label']) ? h($data['label']) : __('Payment Message')?></h5>
					</div>
					<div class="col-md-12 margin30-top">
						<div class="alert alert-success" role="alert">
							<?=__('Please pay %s BTC to %s', h($data['amount']), '<a href="'.$url.'">'.h($data['addr']).'</a>')?>
						</div>
						<div class="text-xs-center">
							<img id="QRCode" src="https://chart.googleapis.com/chart?chs=295x295&cht=qr&chl=<?=h($url)?>&choe=UTF-8"/>
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
				<?=$this->element('userBreadcrumbs')?>
				<?=$this->Notice->show()?>
				<div class="panel">
					<div class="padding30-col">
						<div class="col-md-12">
							<h5><?=isset($data['label']) ? h($data['label']) : __('Payment Message')?></h5>
						</div>
						<div class="col-md-12 margin30-top">
							<div class="alert alert-success" role="alert">
								<?=__('Please pay %s BTC to %s', h($data['amount']), '<a href="'.$url.'">'.h($data['addr']).'</a>')?>
							</div>
							<div>
								<img id="QRCode" src="https://chart.googleapis.com/chart?chs=295x295&cht=qr&chl=<?=h($url)?>&choe=UTF-8"/>
							</div>
						</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
