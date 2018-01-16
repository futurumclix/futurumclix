<!DOCTYPE html>
<html>
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
		<?=$this->Html->css('materialdesignicons.min', array('media' => 'screen'))?>
		<?php if(Module::active('AdGrid')): ?>
		<?=$this->Html->css('AdGrid.adgrid', array('media' => 'screen'))?>
		<?php endif; ?>
		<!-- Javascript includes -->
		<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
		<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<?=$this->Html->script('big.min')?>
		<?=$this->Html->script('uikit.min.js')?>
		<?=$this->Html->script('futurumclix.js')?>
		<!-- Uikit CSS -->
		<?=$this->Html->css('uikit.min', array('media' => 'screen'))?>
		<!-- main css style after uikit -->
		<?=$this->Html->css('style', array('media' => 'screen'))?>
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
	</head>
	<body>
		<div class="topbar">
			<div class="uk-container">
				<div class="uk-grid">
					<div class="uk-width-1-2">
						<div class="langselector uk-inline">
							<button class="languagebutton <?=Configure::read('Config.language')?>" type="button"><?=$this->Html->image('v.png')?></button>
							<div class="uk-dropdown" uk-dropdown="mode: click" style="top: 30px; left: 0px;">
								<ul class="uk-nav uk-dropdown-nav">
									<?php foreach($this->UserForm->getAvailableTranslations() as $k => $v): ?>
									<li><?=
										$this->UserForm->postLink('',
											array('plugin' => null, 'controller' => 'settings', 'action' => 'locale', $k),
											array('class' => $k)
										)
										?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
						<?php if($this->params['controller'] == 'pages' && $this->params['action'] == 'display' && $this->params['pass'][0] == 'home' && !$this->Session->read('Auth.User')): // serve stats only on home page ?>
						<?=$this->element('topHomeStats')?>
						<?php endif; ?>
						<?php if($this->params['controller'] == 'users' && $this->Session->read('Auth.User')): // serve stats only on dashboard page and subpages ?>
						<?=$this->element('topUserStats')?>
						<?php endif; ?>
					</div>
					<div class="uk-width-1-2 uk-text-right toplinks">
						<a class="uk-visible@l" href="#"><i class="mdi mdi-facebook mdi-18px"></i></a>
						<a class="uk-visible@l" href="#"><i class="mdi mdi-twitter mdi-18px"></i></a>
						<a class="uk-visible@l" href="#"><i class="mdi mdi-google-plus mdi-18px"></i></a>
						<?php if($this->Session->read('Auth.User')): ?>
						<?=$this->Html->link(__('Dashboard'), array('plugin' => null, 'controller' => 'users', 'action' => 'dashboard'), array('class' => 'register'))?>
						<?=$this->Html->link(__('Logout'), array('plugin' => null, 'controller' => 'users', 'action' => 'logout'), array('class' => 'logout'))?>
						<?php else: ?>
						<?=$this->Html->link(__('Login'), array('plugin' => null, 'controller' => 'users', 'action' => 'login'), array('class' => 'login'))?>
						<?=$this->Html->link(__('Sign up'), array('plugin' => null, 'controller' => 'users', 'action' => 'signup'), array('class' => 'register'))?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="mainbar">
			<div class="uk-container">
				<div class="uk-grid">
					<div class="uk-width-1-1">
						<nav class="uk-navbar-container uk-navbar-transparent" uk-navbar>
							<div class="uk-navbar-left">
								<?=
									$this->Html->image('logo.png', array(
										'alt' => __('logo'),
										'class' => 'logo',
										'url' => array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home'),
									))
									?>
							</div>
							<div class="uk-navbar-right">
								<ul class="uk-navbar-nav uk-visible@m">
									<li><?=$this->Html->link(__('Home Page'), array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home'))?></li>
									<li><?=$this->Html->link(__('Advertise'), array('plugin' => null, 'controller' => 'advertise', 'action' => 'index'))?></li>
									<li><?=$this->Html->link(__('View Ads'), array('plugin' => null, 'controller' => 'AdsCategories'))?></li>
									<li class="dropdown">
										<a class="dropicon" href=""><?=__('Earn money')?></a>
										<div class="uk-dropdown uk-dropdown-bottom" aria-hidden="true" tabindex="" style="top: 30px; left: 0px;" uk-dropdown>
											<ul class="uk-nav uk-dropdown-nav">
												<?php if(Configure::read('paidOffersActive')): ?>
												<li><?=$this->Html->link(__('Paid Offers'), array('plugin' => null, 'controller' => 'paid_offers', 'action' => 'view'))?></li>
												<?php endif; ?>
												<?php if(Module::active('Offerwalls')): ?>
												<li><?=$this->Html->link(__('Offerwalls'), array('plugin' => 'offerwalls', 'controller' => 'offerwalls', 'action' => 'index'))?></li>
												<?php endif; ?>
												<?php if(Module::active('RevenueShare')): ?>
												<li><?=$this->Html->link(__('Revenue shares'), array('plugin' => 'revenue_share', 'controller' => 'revenue_share', 'action' => 'index'))?></li>
												<?php endif; ?>
												<?php if(Module::active('AdGrid')): ?>
												<li><?=$this->Html->link(__('AdGrid'), array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'grid'))?></li>
												<?php endif; ?>
											</ul>
										</div>
									</li>
									<?php if(Configure::read('Forum.active')): ?>
									<li><?=$this->Html->link(__('Forum'), array('plugin' => null, 'plugin' => 'forum', 'controller' => 'forum', 'action' => 'index'))?></li>
									<?php endif; ?>
									<?php if(Configure::read('supportEnabled')): ?>
									<li><?=$this->Html->link(__('Support system'), array('plugin' => null, 'controller' => 'support', 'action' => 'index'))?></li>
									<?php endif; ?>
								</ul>
								<a href="#mainnavmobile" class="uk-hidden@m" uk-toggle uk-navbar-toggle-icon></a>
							</div>
						</nav>
						<div id="mainnavmobile" uk-offcanvas="overlay: true">
							<div class="uk-offcanvas-bar">
								<ul class="uk-nav uk-nav-default uk-nav-parent-icon">
									<li class="uk-nav-header"><?=__('Navigation')?></li>
									<li><?=$this->Html->link(__('Home Page'), array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home'))?></li>
									<li><?=$this->Html->link(__('View Ads'), array('plugin' => null, 'controller' => 'AdsCategories'))?></li>
									<li><?=$this->Html->link(__('Advertise'), array('plugin' => null, 'controller' => 'advertise', 'action' => 'index'))?></li>
									<?php if(Configure::read('Forum.active')): ?>
									<li><?=$this->Html->link(__('Forum'), array('plugin' => null, 'plugin' => 'forum', 'controller' => 'forum', 'action' => 'index'))?></li>
									<?php endif; ?>
									<?php if(Configure::read('supportEnabled')): ?>
									<li><?=$this->Html->link(__('Support system'), array('plugin' => null, 'controller' => 'support', 'action' => 'index'))?></li>
									<?php endif; ?>
									<li class="uk-nav-header"><?=__('Earn money')?></li>
									<?php if(Configure::read('paidOffersActive')): ?>
									<li><?=$this->Html->link(__('Paid Offers'), array('plugin' => null, 'controller' => 'paid_offers', 'action' => 'view'))?></li>
									<?php endif; ?>
									<?php if(Module::active('Offerwalls')): ?>
									<li><?=$this->Html->link(__('Offerwalls'), array('plugin' => 'offerwalls', 'controller' => 'offerwalls', 'action' => 'index'))?></li>
									<?php endif; ?>
									<?php if(Module::active('RevenueShare')): ?>
									<li><?=$this->Html->link(__('Revenue shares'), array('plugin' => 'revenue_share', 'controller' => 'revenue_share', 'action' => 'index'))?></li>
									<?php endif; ?>
									<?php if(Module::active('AdGrid')): ?>
									<li><?=$this->Html->link(__('AdGrid'), array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'grid'))?></li>
									<?php endif; ?>
									<?php if($this->Session->read('Auth.User')): ?>
									<li class="uk-nav-header"><?=__('Dashboard')?></li>
									<li><?=$this->Html->link(__('Edit Profile'), array('plugin' => null, 'controller' => 'user_profiles', 'action' => 'edit'))?></li>
									<li><?=$this->Html->link(__('Account Security'), array('plugin' => null, 'controller' => 'user_profiles', 'action' => 'security'))?></li>
									<li><?=$this->Html->link(__('Upgrade Your Account'), array('plugin' => null, 'controller' => 'memberships'))?></li>
									<li><?=$this->Html->link(__('Fund Purchase Balance'), array('plugin' => null, 'controller' => 'users', 'action' => 'deposit'))?></li>
									<li><?=$this->Html->link(__('Cashout'), array('plugin' => null, 'controller' => 'users', 'action' => 'cashout'))?></li>
									<li><?=$this->Html->link(__('Purchase History'), array('plugin' => null, 'controller' => 'purchase_history', 'action' => 'index'))?></li>
									<li><?=$this->Html->link(__('Cashout History'), array('plugin' => null, 'controller' => 'cashouts', 'action' => 'index'))?></li>
									<?php if(Module::active('Offerwalls')): ?>
									<li><?=$this->Html->link(__('Offerwalls Log'), array('plugin' => null, 'controller' => 'offerwalls', 'action' => 'postbackLog'))?></li>
									<?php endif; ?>
									<li class="uk-nav-header"><?=__('Advertisements')?></li>
									<li><?=$this->Html->link(__('PTC Advertisement Panel'), array('plugin' => null, 'controller' => 'ads', 'action' => 'index'))?></li>
									<?php if(Configure::read('expressAdsActive')): ?>
									<li><?=$this->Html->link(__('Express Advertisement Panel'), array('plugin' => null, 'controller' => 'express_ads', 'action' => 'index'))?></li>
									<?php endif; ?>
									<?php if(Configure::read('explorerAdsActive')): ?>
									<li><?=$this->Html->link(__('Explorer Advertisement Panel'), array('plugin' => null, 'controller' => 'explorer_ads', 'action' => 'index'))?></li>
									<?php endif; ?>
									<?php if(Configure::read('featuredAdsActive')): ?>
									<li><?=$this->Html->link(__('Featured Advertisement Panel'), array('plugin' => null, 'controller' => 'featuredAds', 'action' => 'index'))?></li>
									<?php endif; ?>
									<?php if(Configure::read('bannerAdsActive')): ?>
									<li><?=$this->Html->link(__('Banner Advertisement Panel'), array('plugin' => null, 'controller' => 'BannerAds', 'action' => 'index'))?></li>
									<?php endif; ?>
									<?php if(Configure::read('loginAdsActive')): ?>
									<li><?=$this->Html->link(__('Login Ads Advertisement Panel'), array('plugin' => null, 'controller' => 'LoginAds', 'action' => 'index'))?></li>
									<?php endif; ?>
									<?php if(Configure::read('paidOffersActive')): ?>
									<li><?=$this->Html->link(__('Paid Offers Panel'), array('plugin' => null, 'controller' => 'paid_offers', 'action' => 'index'))?></li>
									<?php endif; ?>
									<?php if(Module::active('AdGrid')): ?>
									<li><?=$this->Html->link(__('AdGrid Advertisement Panel'), array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'index'))?></li>
									<?php endif; ?>
									<li class="uk-nav-header"><?=__('Referrals')?></li>
									<li><?=$this->Html->link(__('Promotion Tools'), array('plugin' => null, 'controller' => 'tools', 'action' => 'promotion'))?></li>
									<li><?=$this->Html->link(__('Direct Referrals'), array('plugin' => null, 'controller' => 'users', 'action' => 'directReferrals'))?></li>
									<?php if(Module::active('ReferralsContest')): ?>
									<li><?=$this->Html->link(__('Referral\'s Contest'), array('plugin' => 'referrals_contest', 'controller' => 'referrals_contest', 'action' => 'index'))?></li>
									<?php endif; ?>
									<?php if(Configure::read('enableRentingReferrals')): ?>
									<li><?=$this->Html->link(__('Rented Referrals'), array('plugin' => null, 'controller' => 'users', 'action' => 'rentedReferrals'))?></li>
									<li><?=$this->Html->link(__('Rent Referrals'), array('plugin' => null, 'controller' => 'users', 'action' => 'rentReferrals'))?></li>
									<?php endif; ?>
									<?php if(Configure::read('enableBuyingReferrals')): ?>
									<li><?=$this->Html->link(__('Buy Referrals'), array('plugin' => null, 'controller' => 'users', 'action' => 'buyReferrals'))?></li>
									<?php endif; ?>
									<li><?=$this->Html->link(__('Referral\'s Commissions'), array('plugin' => null, 'controller' => 'commissions'))?></li>
									<?php endif; ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?=$this->fetch('content')?>
		<div class="footer uk-margin-top">
			<div class="uk-container">
				<div class="uk-grid">
					<div class="uk-width-1-3@m copy">
						<!-- Do not remove this copyright unless you bought Copyright Removal from our shop. Otherwise your licence will be suspended! -->
						<p>&copy; 2014 - <?=date('Y')?></p>
						<a href="https://futurumclix.com"><?=__('Powered by %s', 'FuturumClix.com')?></a>
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
		<div id="ajax-modal" uk-modal="center: true">
			<div class="uk-modal-dialog" id="ajax-modal-container"></div>
		</div>
		<script>
			$(document).ready(function() {
				$('[data-counter]').on('keyup', charCounter);
				$('[data-ajaxsource]').on('click', ajaxModal);
			})
		</script>
		<?=$this->Js->writeBuffer()?>
		<?=$this->fetch('postLink')?>
	</body>
</html>
