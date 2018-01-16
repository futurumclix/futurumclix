<div class="container advertise">
	<div class="row">
		<div class="col">
			<h2 class="text-xs-center"><?=__('Advertise on')?> <?=h(Configure::read('siteTitle'))?></h2>
		</div>
	</div>
	<div class="row margin30-top padding30-bottom">
		<div class="col-md-6">
			<div class="panel">
				<div class="padding30-col">
					<h5><?=__('Register on the site!')?></h3>
					<p><?=__('You can easily register on our site and advertise with enhanced advertiser panel. Why to register on our site? Check following reasons:')?>
						<ul>
							<li><?=__('Full control of your advertisement')?></li>
							<li><?=__('You have your own wallet which you can top up any time you want')?></li>
							<li><?=__('Detailed statistics of your advertisement')?></li>
						</ul>
					</p>
					<div class="text-xs-right">
						<?=
							$this->Html->link(__('REGISTER'), array(
								'controller' => 'users',
								'action' => 'signup',
							), array(
								'class' => 'btn btn-primary',
							))
						?>
					</div>
	  			</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel">
				<div class="padding30-col">
					<h5><?=__('Buy without registering')?></h3>
					<p><?=__('If you don\'t want to register, use this option. After you order, you will get statistics of your advertisement but you will not be able to control it (editing, pausing, changing targetting etc).')?></p>
					<div class="text-xs-right">
						<?=
							$this->Html->link(__('BUY'), array(
								'action' => 'buy',
								$type,
							), array(
								'class' => 'btn btn-primary',
							))
						?>
					</div>
	  			</div>
			</div>
		</div>
	</div>
</div>
