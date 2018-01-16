<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('Advertise on %s', h(Configure::read('siteTitle')))?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid-match" uk-grid>
		<div class="uk-width-1-2@m uk-margin-top">
			<div class="uk-card uk-card-body uk-card-default">
				<h5><?=__('Register on the site!')?></h5>
				<p><?=__('You can easily register on our site and advertise with enhanced advertiser panel. Why to register on our site? Check following reasons:')?>
				<ul>
					<li><?=__('Full control of your advertisement')?></li>
					<li><?=__('You have your own wallet which you can top up any time you want')?></li>
					<li><?=__('Detailed statistics of your advertisement')?></li>
				</ul>
				</p>
				<div class="uk-text-right">
					<?=
						$this->Html->link(__('REGISTER'), array(
							'controller' => 'users',
							'action' => 'signup',
							), array(
							'class' => 'uk-button uk-button-primary',
							))
							?>
				</div>
			</div>
		</div>
		<div class="uk-width-1-2@m uk-margin-top">
			<div class="uk-card uk-card-body uk-card-default">
				<h5><?=__('Buy without registering')?></h5>
				<p><?=__('If you don\'t want to register, use this option. After you order, you will get statistics of your advertisement but you will not be able to control it (editing, pausing, changing targetting etc).')?></p>
				<div class="uk-text-right">
					<?=
						$this->Html->link(__('BUY'), array(
							'action' => 'buy',
							$type,
							), array(
							'class' => 'uk-button uk-button-primary',
							))
							?>
				</div>
			</div>
		</div>
	</div>
</div>
