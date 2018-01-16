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
		<?=$this->Html->charset()?>
		<?=$this->fetch('meta')?>
		<title><?=h(Configure::read('siteTitle'))?>:<?=h($title_for_layout)?></title>
		<!-- CSS styles -->
		<?=$this->fetch('css')?> 
		<link rel="stylesheet" href="//cdn.materialdesignicons.com/1.7.22/css/materialdesignicons.min.css">
		<?php if(Module::active('AdGrid')): ?>
		<?=$this->Html->css('AdGrid.adgrid', array('media' => 'screen'))?>
		<?php endif; ?>
		<!-- Javascript includes -->
		<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<?=$this->Html->script('big.min')?>
		<?=$this->Html->script('uikit.min.js')?>
		<?=$this->Html->script('futurumclix.js')?>
		<?=$this->Html->script('adclick')?>
		<?=$this->Html->script('referrer-killer.js')?>
		<!-- Uikit CDN -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.6/css/uikit.min.css" />
		<!-- main css style after uikit -->
		<?=$this->Html->css('style', array('media' => 'screen'))?>
		<?php
			if($this->Session->read('Auth.User')) {
				$this->Evercookie->create();
			}
			?>
		<?=$this->fetch('script')?>
	</head>
	<?=$this->fetch('content')?>
	<?=$this->Js->writeBuffer()?>
	<?=$this->fetch('postLink')?>
</html>
