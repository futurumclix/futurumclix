<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="robots" content="index,follow" />
	<!--Fav and touch icons-->
	<link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="/img/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/img/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/img/manifest.json">
	<link rel="mask-icon" href="/img/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="theme-color" content="#ffffff">
	<?=$this->Html->charset()?>
	<title><?=h(Configure::read('siteTitle'))?>:<?=h($title_for_layout)?></title>
	<?=$this->Html->css('bootstrap', array('media' => 'screen'))?>
	<?=$this->Html->css('style', array('media' => 'screen'))?>
	<?=$this->Html->css('responsive', array('media' => 'screen'))?>
	<?=$this->fetch('css')?>
	<?php if(Module::active('AdGrid')): ?>
		<?=$this->Html->css('AdGrid.adgrid', array('media' => 'screen'))?>
	<?php endif; ?>
	<?=$this->Html->script('jquery')?>
	<?=$this->Html->script('jquery-ui-1.10.4.custom')?>
	<?=$this->Html->script('tether.min')?>
	<?=$this->Html->script('bootstrap.min')?>
	<?=$this->Html->script('big.min')?>
	<?=$this->Html->script('futurumclix.js')?>
	<?php
		if($this->Session->read('Auth.User')) {
			$this->Evercookie->create();
		}
	?>
	<?=$this->fetch('script')?>
	<script>
	<?php if(Configure::read('googleAnalEnable')): ?>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		ga('create', '<?=Configure::read('googleAnalID')?>', 'auto');
		ga('send', 'pageview');
	<?php endif; ?>
	</script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body class="main <?=h($title_for_layout)?>">
	<div class="topbg<?php if($this->request->here != '/'): ?>_subpage<?php endif; ?>" id="top">
		<div class="containerbig toppanel">
			<div class="col-md-6 text-rs-left">
				<?=
					$this->Html->image('logo.png', array(
						'alt' => __('logo'),
						'class' => 'logo',
						'url' => array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home'),
					))
				?>
			</div>
			<div class="col-md-6 text-rs-right links">
				<?php if($this->Session->read('Auth.User')): ?>
					<?=$this->Html->link(__('Dashboard'), array('plugin' => null, 'controller' => 'users', 'action' => 'dashboard'))?>
					<span><?=__('or')?></span>
					<?=$this->Html->link(__('Logout'), array('plugin' => null, 'controller' => 'users', 'action' => 'logout'))?>
				<?php else: ?>
					<?=$this->Html->link(__('Sign up'), array('plugin' => null, 'controller' => 'users', 'action' => 'signup'))?>
					<span>or</span>
					<?=$this->Html->link(__('Login'), array('plugin' => null, 'controller' => 'users', 'action' => 'login'))?>
				<?php endif; ?>
				<?=$this->element('languageSelector', array('id' => 'homelanguage'))?>
			</div>
			<?php if($this->request->here == '/'): ?>
				<div class="col-sm-12 text-xs-center mainslogan">
					<h1><?=__('Simple Advertising Platform')?></h1>
					<h6><?=__('Maximize your earnings. Just sign up on our platform and earn!')?></h6>
					<?=$this->Html->link(__('I want to earn!'), array('controller' => 'users', 'action' => 'signup'), array('class' => 'btn btn-primary'))?>
					<?=$this->Html->link(__('I want to advertise'), array('controller' => 'advertise', 'action' => 'index'), array('class' => 'btn btn-secondary'))?>
				</div>
			<?php endif; ?>
		</div>
	</div>
		<div class="container">
		<div class="row mainbanners">
			<div class="col-sm-6 text-xs-center">
				<?=$this->BannerAds->show(array('class' => 'img-fluid'))?>
			</div>
			<div class="col-sm-6 text-xs-center">
				<?=$this->BannerAds->show(array('class' => 'img-fluid'))?>
			</div>
		</div>
	</div>
