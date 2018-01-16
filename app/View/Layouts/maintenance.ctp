<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="robots" content="index,follow" />
		<!--Fav and touch icons-->
		<link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
		<link rel="icon" type="image/png" href="/img/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="/img/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="/img/manifest.json">
		<link rel="mask-icon" href="/img/safari-pinned-tab.svg" color="#5bbad5">
		<meta name="theme-color" content="#ffffff">
		<?=$this->fetch('meta')?>
		<script>
		if(navigator.userAgent.match(/IEMobile\/10\.0/)) {
			var msViewportStyle = document.createElement("style")
			msViewportStyle.appendChild(
				document.createTextNode(
					"@-ms-viewport{width:auto!important}"
				)
			)
			document.getElementsByTagName("head")[0].appendChild(msViewportStyle)
		}
		</script>
		<?=$this->Html->charset()?>
		<title><?=h(Configure::read('siteTitle'))?>:<?=__('Maintenance Mode')?></title>
		<?=$this->Html->css('font-awesome.min', array('media' => 'screen'))?>
		<?=$this->Html->css('bootstrap', array('media' => 'screen'))?>
		<?=$this->Html->css('bootstrap-theme', array('media' => 'screen'))?>
		<?=$this->Html->css('jquery.bxslider', array('media' => 'screen'))?>
		<?=$this->Html->css('style', array('media' => 'screen'))?>
		<?=$this->fetch('css')?>
		<?=$this->Html->script('jquery')?>
		<?=$this->Html->script('jquery-ui-1.10.4.custom')?>
		<?=$this->Html->script('bootstrap')?>
		<?=$this->Html->script('respond')?>
		<?=$this->Html->script('html5shiv')?>
		<?=$this->Html->script('jquery.bxslider')?>
		<?=$this->Html->script('tinynav')?>
		<?=$this->Html->script('big.min')?>
		<?=$this->Html->script('futurumclix.js')?>
		<?=$this->fetch('script')?>
	</head>
	<body>
		<div class="jumbotron">
			<div class="container">
			<h1>Maintenance</h1>
			<p><?=h($info)?></p>
			<!-- Do not remove this copyright unless you bought Copyright Removal from our shop. Otherwise your licence will be suspended! -->
			<p><a href="http://futurumclix.com">Powered by FuturumClix.com</a> &copy; 2014 - <?=date('Y')?></p>
			</div>
		</div>
	</body>
</html>
