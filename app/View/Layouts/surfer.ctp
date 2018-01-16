<!DOCTYPE html>
<html style="height:100%;">
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
	<?=$this->fetch('meta')?>
	<?=$this->Html->charset()?>
	<title><?=h(Configure::read('siteTitle'))?>: <?=h($title_for_layout)?></title>
	<?=$this->Html->css('bootstrap', array('media' => 'screen'))?>
	<?=$this->Html->css('jquery.bxslider', array('media' => 'screen'))?>
	<?=$this->Html->css('style', array('media' => 'screen'))?>
	<?=$this->fetch('css')?>
	<?=$this->Html->script('jquery')?>
	<?=$this->Html->script('jquery-ui-1.10.4.custom')?>
	<?=$this->Html->script('tether.min')?>
	<?=$this->Html->script('bootstrap.min')?>
	<?=$this->Html->script('big.min')?>
	<?=$this->Html->script('futurumclix.js')?>
	<?=$this->Html->script('referrer-killer.js')?>
	<?php
		if($this->Session->read('Auth.User')) {
			$this->Evercookie->create();
		}
	?>
	<?=$this->fetch('script')?>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
</head>
<?=$this->fetch('content')?>
<?=$this->Js->writeBuffer()?>
<?=$this->fetch('postLink')?>
</html>
