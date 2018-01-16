<?php
	echo $this->Html->docType();
	echo $this->OpenGraph->html(); ?>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="robots" content="index,follow" />
	<?php echo $this->Html->charset(); ?>
	<title><?php echo $this->Breadcrumb->pageTitle($settings['name'], array('separator' => $settings['titleSeparator'])); ?></title>
	<!--Fav and touch icons-->
	<link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="/img/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/img/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/img/manifest.json">
	<link rel="mask-icon" href="/img/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="theme-color" content="#ffffff">
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.6/css/uikit.min.css" />
	<link rel="stylesheet" href="//cdn.materialdesignicons.com/1.7.22/css/materialdesignicons.min.css">
	<?php
		echo $this->Html->script('uikit.min');
		echo $this->Html->script('tether.min');
		echo $this->Html->css('Forum.style');
		echo $this->Html->script('Forum.forum');
		
		if ($this->params['controller'] === 'forum') {
		echo $this->Html->meta(__d('forum', 'RSS Feed - Latest Topics'), array('action' => 'index', 'ext' => 'rss'), array('type' => 'rss'));
		} else if (isset($rss)) {
		echo $this->Html->meta(__d('forum', 'RSS Feed - Content Review'), array($rss, 'ext' => 'rss'), array('type' => 'rss'));
		}
		
		$this->OpenGraph->name($settings['name']);
		
		echo $this->OpenGraph->fetch();
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script'); ?>
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
</head>
<body class="controller-<?php echo $this->request->controller; ?>">
	<?php echo $this->element('navigation'); ?>
	<div class="action-<?php echo $this->action; ?>">
		<?php
			$this->Breadcrumb->prepend(__d('forum', 'Forum'), array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'index'));
			$this->Breadcrumb->prepend($settings['name'], '/');
			echo $this->element('breadcrumbs');?>
		<div class="uk-container content uk-margin-top">
			<div class="uk-grid">
				<div class="uk-width-1-1">
					<?php
						echo $this->Session->flash();
						echo $this->Notice->show();
						echo $this->fetch('content'); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="footer uk-margin-top">
		<div class="uk-container">
			<div class="uk-grid">
				<div class="uk-width-1-3@m copy">
					<!-- Do not remove this copyright unless you bought Copyright Removal from our shop. Otherwise your licence will be suspended! -->
					<p>&copy; 2014 - <?=date('Y')?></p>
					<a href="https://futurumclix.com">Powered by FuturumClix.com</a>
				</div>
				<div class="uk-width-1-3@m social uk-text-center">
					<a class="uk-visible@l" href="#"><i class="mdi mdi-facebook mdi-18px"></i></a>
					<a class="uk-visible@l" href="#"><i class="mdi mdi-twitter mdi-18px"></i></a>
					<a class="uk-visible@l" href="#"><i class="mdi mdi-google-plus mdi-18px"></i></a>
				</div>
				<div class="uk-width-1-3@m links uk-text-right">
					<?=$this->Html->link(__('Payment Proofs'), array('plugin' => null, 'controller' => 'cashouts', 'action' => 'payment_proofs'))?>
					<?php if(Configure::read('sitePrivacyPolicyActive')):?>
					<?=$this->Html->link(__('Privacy Policy'), array('plugin' => null, 'controller' => 'pages', 'action' => 'content', 'privacy'))?>
					<?php endif; ?>
					<?php if(Configure::read('siteFAQActive')):?>
					<?=$this->Html->link(__('FAQ'), array('plugin' => null, 'controller' => 'pages', 'action' => 'content', 'faq'))?>
					<?php endif; ?>
					<?php if(Configure::read('siteToSActive')):?>
					<?=$this->Html->link(__('ToS'), array('plugin' => null, 'controller' => 'pages', 'action' => 'content', 'tos'))?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
