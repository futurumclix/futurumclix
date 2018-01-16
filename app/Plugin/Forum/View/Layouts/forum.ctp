<?php
echo $this->Html->docType();
echo $this->OpenGraph->html(); ?>
<head>
    <?php echo $this->Html->charset(); ?>
    <title><?php echo $this->Breadcrumb->pageTitle($settings['name'], array('separator' => $settings['titleSeparator'])); ?></title>
    <!--Fav and touch icons-->
	<link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="/img/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/img/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/img/manifest.json">
	<link rel="mask-icon" href="/img/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="theme-color" content="#ffffff">
    <?php
    //echo $this->Html->css('Forum.titon.min');
	echo $this->Html->css('font-awesome.min.css', array('media' => 'screen'));
	echo $this->Html->css('bootstrap', array('media' => 'screen'));
	echo $this->Html->script('jquery');
	echo $this->Html->script('tether.min');
    echo $this->Html->script('bootstrap.min');
	echo $this->Html->css('Forum.style');
	echo $this->Html->css('Forum.responsive');
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
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<body class="controller-<?php echo $this->request->controller; ?>">
		<?php echo $this->element('navigation'); ?>
        <div class="body action-<?php echo $this->action; ?>">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<?php
						$this->Breadcrumb->prepend(__d('forum', 'Forum'), array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'index'));
						$this->Breadcrumb->prepend($settings['name'], '/');
						echo $this->element('breadcrumbs');
						echo $this->Session->flash();
						echo $this->Notice->show();
						echo $this->fetch('content'); ?>
					</div>
				</div>
			</div>
		</div>
        <div class="footer">
			<div class="container">
				<div class="row">
					<div class="col-sm-6 footerlinks">
						<nav class="nav nav-inline">
							<?php if(Configure::read('sitePrivacyPolicyActive')):?>
								<?=$this->Html->link(__('Privacy Policy'), array('plugin' => null, 'controller' => 'pages', 'action' => 'content', 'privacy'))?>
							<?php endif; ?>
							<?php if(Configure::read('siteFAQActive')):?>
								<?=$this->Html->link(__('FAQ'), array('plugin' => null, 'controller' => 'pages', 'action' => 'content', 'faq'))?>
							<?php endif; ?>
							<?php if(Configure::read('siteToSActive')):?>
								<?=$this->Html->link(__('ToS'), array('plugin' => null, 'controller' => 'pages', 'action' => 'content', 'tos'))?>
							<?php endif; ?>
						</nav>
					</div>
					<div class="col-sm-6 copyright text-xs-right">
						<p><?=h(Configure::read('siteName'))?> &copy; 2014 - <?=date('Y')?></p>
					</div>
				</div>
			</div>
		</div>
    </div>
<script>
$(document).ready(function(){
	$("[data-toggle=tooltip]").tooltip({placement : 'top'});
	$(".js-tooltip").tooltip();
})
</script>
</body>
</html>
