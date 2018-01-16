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
	<?=$this->fetch('meta')?>
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
		<?='var CurrencyHelperData = '.json_encode($this->Currency, JSON_NUMERIC_CHECK)?>
	</script>
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
<body class="<?=h($title_for_layout)?>">
	<?php if($this->params['controller'] == 'AdsCategories' || $this->params['action'] == 'grid' || $this->params['controller'] == 'memberships' || $this->params['controller'] == 'news' || $this->params['controller'] == 'support' || $this->params['action'] == 'payment_proofs' || $this->params['controller'] == 'advertise'): // serve different logo on pages with no sidebar ?>
	<nav class="navbar">
	<?php 
		echo $this->Html->link(
			$this->Html->image('logo_dashboard_bw.png', array('alt' => 'Logo', 'class' => 'navbar-logo')),
			array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home'),
			array('escape' => false, 'class' => 'navbar-brand white')
		);
	?>
	<?php else: ?>
	<nav class="navbar">
	<?php 
		echo $this->Html->link(
			$this->Html->image('logo_dashboard.png', array('alt' => 'Logo', 'class' => 'navbar-logo')),
			array('controller' => 'pages', 'action' => 'display', 'home'),
			array('escape' => false, 'class' => 'navbar-brand')
		);
	?>
	<?php endif; ?>
		<div class="navbar-toggle">
			<button class="navbar-toggler hidden-md-up" type="button" data-toggle="collapse" data-target="#mainnavbar">&#9776;</button>
			<button class="navbar-toggler hidden-md-up navbar-toggle-yellow" type="button" data-toggle="collapse" data-target="#sidebar">&#9776;</button>
		</div>
	</a>
		<div class="collapse navbar-toggleable-sm" id="mainnavbar">
		<ul class="nav navbar-nav pull-xs-right">
			<li class="nav-item"><?=$this->Html->link(__('Dashboard'), array('plugin' => null, 'controller' => 'users', 'action' => 'dashboard'), array('class' => 'nav-link'))?></li>
			<li class="nav-item"><?=$this->Html->link(__('View Ads'), array('plugin' => null, 'controller' => 'AdsCategories'), array('class' => 'nav-link'))?></li>
			<?php if(Configure::read('paidOffersActive')): ?>
				<li class="nav-item"><?=$this->Html->link(__('Paid Offers'), array('plugin' => null, 'controller' => 'paid_offers', 'action' => 'view'), array('class' => 'nav-link'))?></li>
			<?php endif; ?>
			<?php if(Module::active('Offerwalls')): ?>
				<li class="nav-item"><?=$this->Html->link(__('Offerwalls'), array('plugin' => 'offerwalls', 'controller' => 'offerwalls', 'action' => 'index'), array('class' => 'nav-link'))?></li>
			<?php endif; ?>
			<?php if(Module::active('RevenueShare')): ?>
				<li class="nav-item"><?=$this->Html->link(__('Revenue shares'), array('plugin' => 'revenue_share', 'controller' => 'revenue_share', 'action' => 'index'), array('class' => 'nav-link'))?></li>
			<?php endif; ?>
			<?php if(Module::active('AdGrid')): ?>
				<li class="nav-item"><?=$this->Html->link(__('AdGrid'), array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'grid'), array('class' => 'nav-link'))?></li>
			<?php endif; ?>
			<li class="nav-item"><?=$this->Html->link(__('Advertise'), array('controller' => 'advertise', 'action' => 'index'), array('class' => 'nav-link'))?></li>
			<?php if(Configure::read('Forum.active')): ?>
				<li class="nav-item"><?=$this->Html->link(__('Forum'), array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'index'), array('class' => 'nav-link'))?></li>
			<?php endif; ?>
			<li class="nav-item"><?=$this->Html->link(__('Logout'), array('plugin' => null, 'controller' => 'users', 'action' => 'logout'), array('class' => 'nav-link'))?></li>
			<li class="nav-item">
				<?=$this->element('languageSelector', array('id' => 'dashboardlanguage'))?>
			</li>
		</ul>
		</div>
	</nav>
