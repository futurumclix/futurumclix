<!DOCTYPE html>
<html>
	<head>
		<title><?=Configure::read('siteTitle')?> -- <?=__d('admin', 'Admin Panel')?> -- <?=$this->fetch('title')?></title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<meta name="description" content="" />
		<!--Fav and touch icons-->
		<link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
		<link rel="icon" type="image/png" href="/img/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="/img/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="/img/manifest.json">
		<link rel="mask-icon" href="/img/safari-pinned-tab.svg" color="#5bbad5">
		<meta name="theme-color" content="#ffffff">
		<?=$this->Html->css('admin/bootstrap.css', array('media' => 'all'))?>
		<?=$this->Html->css('admin/bootstrap-theme.css', array('media' => 'all'))?>
		<?=$this->Html->css('admin/style.css', array('media' => 'all'))?>
		<?=$this->Html->css('admin/font-awesome.min', array('media' => 'screen'));?>
		<?=$this->fetch('css')?>
		<?=$this->Html->script('admin/big.min')?>
		<script><?='var CurrencyHelperData = '.json_encode($this->Currency, JSON_NUMERIC_CHECK)?></script>
		<?=$this->Html->script('admin/jquery')?>
		<?=$this->Html->script('admin/jquery-ui-1.10.4.custom')?>
		<?=$this->Html->script('admin/bootstrap.min.js')?>
		<?=$this->Html->script('admin/futurumclix.js')?>
		<?=$this->fetch('script')?>
		<?php $this->Js->buffer("
			$('[data-toggle=tooltip]').tooltip();
			$('[data-toggle=popover]').popover();
		") ?>
	</head>
	<body>
		<?php if($this->action != 'admin_login' && $this->Session->read('Auth.Admin')):?>
		<div class="header">
			<div class="container">
				<div class="row">
					<div class="col-md-4"><?=$this->Html->image('admin/logo.png')?></div>
					<div class="col-md-8 text-right">
						<ul class="top_nav">
							<li class="welcome"><?=$this->Session->read('Auth.Admin.email')?></li>
							<li><?=
								$this->Html->link('<i class="fa fa-home"></i>'.__d('admin', ' Home'),
									array('plugin' => null, 'controller' => 'admins', 'action' => 'home'),
									array('class' => '', 'escape' => false)
								)
								?></li>
							<li><?=
								$this->Html->link('<i class="fa fa-user"></i>'.__d('admin', ' Manage your account'),
									array('plugin' => null, 'controller' => 'admins', 'action' => 'edit', $this->Session->read('Auth.Admin.id')),
									array('class' => '', 'escape' => false)
								)
								?></li>
							<li><?=
								$this->Html->link('<i class="fa fa-unlock"></i>'.__d('admin', ' Logout'),
									array('plugin' => null, 'controller' => 'admins', 'action' => 'logout'),
									array('class' => '', 'escape' => false)
								)
								?></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<!-- End top header -->
		<div class="container">
		<!-- Horizontal menu -->
		<div class="row">
			<div class="col-md-12">
				<nav class="navbar navbar-top">
					<div class="container-fluid">
						<!-- Brand and toggle get grouped for better mobile display -->
						<div class="navbar-header">
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#collapse-1">
							<span class="sr-only"><?=__d('admin', 'Toggle navigation')?></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							</button>
						</div>
						<!-- Collect the nav links, forms, and other content for toggling -->
						<div class="collapse navbar-collapse" id="collapse-1">
							<ul class="nav navbar-nav">
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?=__d('admin', 'Users')?> <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li><?=
										$this->Html->link(__d('admin', 'View And Edit'),
											array('plugin' => null, 'controller' => 'users', 'action' => 'index')
										)
										?></li>
									<li>
										<?=
										$this->Html->link(__d('admin', 'Add New User'),
											array('plugin' => null, 'controller' => 'users', 'action' => 'add')
										)
										?>
									</li>
									<li class="divider"></li>
									<li><?=
										$this->Html->link(__d('admin', 'Send Message'),
											array('plugin' => null, 'controller' => 'users', 'action' => 'sendMessage')
										)
										?>
									<li><?=
										$this->Html->link(__d('admin', 'User Clean Up'),
											array('plugin' => null, 'controller' => 'users', 'action' => 'cleanup')
										)
										?></li>
									<li class="divider"></li>
									<li><?=
										$this->Html->link(__d('admin', 'Manage Admins'),
											array('plugin' => null, 'controller' => 'admins', 'action' => 'index')
										)
										?></li>
									<li class="divider"></li>
									<li><?=
										$this->Html->link(__d('admin', 'Manage Groups'),
											array('plugin' => null, 'controller' => 'groups', 'action' => 'index')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Add Group'),
											array('plugin' => null, 'controller' => 'groups', 'action' => 'add')
										)
										?></li>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-desktop"></i> <?=__d('admin', 'Advertisements')?> <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li class="dropdown-header"><?=__d('admin', 'PTC Ads')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'PTC Settings'), 
											array('plugin' => null, 'controller' => 'ads_categories', 'action' => 'settings')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'PTC Categories'),
											array('plugin' => null, 'controller' => 'ads_categories', 'action' => 'index')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Categories Order List'),
											array('plugin' => null, 'controller' => 'ads_categories', 'action' => 'order')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Manage Advertisements'),
											array('plugin' => null, 'controller' => 'ads', 'action' => 'index')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Add New Advertisement'),
											array('plugin' => null, 'controller' => 'ads', 'action' => 'add')
										)
										?></li>
									<li class="dropdown-header"><?=__d('admin', 'Featured Ads')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Manage Featured Ads'),
											array('plugin' => null, 'controller' => 'featured_ads', 'action' => 'index')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Featured Ads Settings'),
											array('plugin' => null, 'controller' => 'featured_ads', 'action' => 'settings')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Add New Advertisement'),
											array('plugin' => null, 'controller' => 'featured_ads', 'action' => 'add')
										)
										?></li>
									<li class="dropdown-header"><?=__d('admin', 'Banner Ads')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Manage Banner Ads'),
											array('plugin' => null, 'controller' => 'banner_ads', 'action' => 'index')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Banner Ads Settings'),
											array('plugin' => null, 'controller' => 'banner_ads', 'action' => 'settings')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Add New Advertisement'),
											array('plugin' => null, 'controller' => 'banner_ads', 'action' => 'add')
										)
										?></li>
									<?php if(Module::active('AdGrid')): ?>
										<li class="dropdown-header"><?=__d('admin', 'AdGrid')?></li>
										<li>
											<?=
												$this->Html->link(__d('admin', 'Manage AdGrid'),
													array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'index')
												)
											?>
										</li>
										<li>
											<?=
												$this->Html->link(__d('admin', 'AdGrid Settings'),
													array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'settings')
												)
											?>
										</li>
										<li>
											<?=
												$this->Html->link(__d('admin', 'Add New Advertisement'),
													array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'add')
												)
											?>
										</li>
									<?php endif; ?>
									<li class="dropdown-header"><?=__d('admin', 'Login Ads')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Manage Login Ads'),
											array('plugin' => null, 'controller' => 'login_ads', 'action' => 'index')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Login Ads Settings'),
											array('plugin' => null, 'controller' => 'login_ads', 'action' => 'settings')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Add New Advertisement'),
											array('plugin' => null, 'controller' => 'login_ads', 'action' => 'add')
										)
										?></li>
									<li class="dropdown-header"><?=__d('admin', 'Paid Offers')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Paid Offers Settings'),
											array('plugin' => null, 'controller' => 'paid_offers', 'action' => 'settings')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Manage Paid Offers'),
											array('plugin' => null, 'controller' => 'paid_offers', 'action' => 'index')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Manage Applications'),
											array('plugin' => null, 'controller' => 'paid_offers', 'action' => 'applications')
										)
										?></li>
									<li class="dropdown-header"><?=__d('admin', 'Express Ads')?></li>
									<li>
										<?=
											$this->Html->link(__d('admin', 'Express Ads Settings'),
												array('plugin' => null, 'controller' => 'express_ads', 'action' => 'settings')
											)
										?>
									</li>
									<li>
										<?=
											$this->Html->link(__d('admin', 'Manage Express Ads'),
												array('plugin' => null, 'controller' => 'express_ads', 'action' => 'index')
											)
										?>
									</li>
									<li>
										<?=
											$this->Html->link(__d('admin', 'Add Express Ad'),
												array('plugin' => null, 'controller' => 'express_ads', 'action' => 'add')
											)
										?>
									</li>
									<li class="dropdown-header"><?=__d('admin', 'Explorer Ads')?></li>
									<li>
										<?=
											$this->Html->link(__d('admin', 'Explorer Ads Settings'),
												array('plugin' => null, 'controller' => 'explorer_ads', 'action' => 'settings')
											)
										?>
									</li>
									<li>
										<?=
											$this->Html->link(__d('admin', 'Manage Explorer Ads'),
												array('plugin' => null, 'controller' => 'explorer_ads', 'action' => 'index')
											)
										?>
									</li>
									<li>
										<?=
											$this->Html->link(__d('admin', 'Add Explorer Ad'),
												array('plugin' => null, 'controller' => 'explorer_ads', 'action' => 'add')
											)
										?>
									</li>
									<?php if(Module::active('Offerwalls')): ?>
										<li class="dropdown-header"><?=__d('admin', 'Offerwalls')?></li>
										<li>
											<?=
												$this->Html->link(__d('admin', 'Offerwall Settings'),
													array('plugin' => 'offerwalls', 'controller' => 'offerwalls', 'action' => 'index')
												)
											?>
										</li>
										<li>
											<?=
												$this->Html->link(__d('admin', 'Postback Log'),
													array('plugin' => 'offerwalls', 'controller' => 'offerwalls', 'action' => 'postbackLog')
												)
											?>
										</li>
									<?php endif; ?>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-th"></i> <?=__d('admin', 'Items')?> <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li><?=
										$this->Html->link(__d('admin', 'View User Items'),
											array('plugin' => null, 'controller' => 'users', 'action' => 'bought_items')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Add User Items'),
											array('plugin' => null, 'controller' => 'bought_items', 'action' => 'add')
										)
										?></li>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-shopping-basket"></i> <?=__d('admin', 'Finances')?> <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li><?=
										$this->Html->link(__d('admin', 'Deposits'),
											array('plugin' => null, 'controller' => 'deposits', 'action' => 'deposits')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Add Deposit'),
											array('plugin' => null, 'controller' => 'deposits', 'action' => 'add')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Purchases'),
											array('plugin' => null, 'controller' => 'deposits', 'action' => 'purchases', 'sort' => 'date', 'direction' => 'desc')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Commissions'),
											array('plugin' => null, 'controller' => 'commissions', 'action' => 'index')
										)
										?></li>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-money"></i> <?=__d('admin', 'Payouts')?> <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li><?=
										$this->Html->link(__d('admin', 'Payouts List'),
											array('plugin' => null, 'controller' => 'cashouts', 'action' => 'index', 'Cashout.status' => 'New')
										)
									?></li>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-comments-o"></i> <?=__d('admin', 'Communication')?> <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li class="dropdown-header"><?=__d('admin', 'News')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Manage News'),
											array('plugin' => null, 'controller' => 'news', 'action' => 'index')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Add News'),
											array('plugin' => null, 'controller' => 'news', 'action' => 'add')
										)
									?></li>
									<li class="dropdown-header"><?=__d('admin', 'Support System')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Reports'),
											array('plugin' => null, 'controller' => 'itemReports', 'action' => 'index', 'sort' => 'created', 'direction' => 'desc', 'ItemReport.status:0')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Tickets Settings'),
											array('plugin' => null, 'controller' => 'support', 'action' => 'settings')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Canned Responses'),
											array('plugin' => null, 'controller' => 'support_canned_answers', 'action' => 'index')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Tickets'),
											array('plugin' => null, 'controller' => 'support', 'action' => 'index', 'sort' => 'modified', 'direction' => 'desc')
										)
									?></li>
									<li class="dropdown-header"><?=__d('admin', 'Forum')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'General Settings'),
											array('plugin' => 'forum', 'controller' => 'settings', 'action' => 'general')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Forum Orders'),
											array('plugin' => 'forum', 'controller' =>'settings', 'action' => 'order' )
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Forum ToS'),
											array('plugin' => 'forum', 'controller' => 'settings', 'action' => 'tos')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Forum Help'),
											array('plugin' => 'forum', 'controller' => 'settings', 'action' => 'help')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Manage Forums'),
											array('plugin' => 'forum', 'controller' => 'settings', 'action' => 'index')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Create New Forum'),
											array('plugin' => 'forum', 'controller' => 'settings', 'action' => 'add')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Manage Moderators'),
											array('plugin' => 'forum', 'controller' => 'moderators', 'action' => 'index')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Add New Moderator'),
											array('plugin' => 'forum', 'controller' => 'moderators', 'action' => 'add')
										)
									?></li>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog"></i> <?=__d('admin', 'Settings')?> <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li class="dropdown-header"><?=__d('admin', 'Global Settings')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'General Settings'),
											array('plugin' => null, 'controller' => 'settings', 'action' => 'general')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Activity And Security'),
											array('plugin' => null, 'controller' => 'settings', 'action' => 'activity')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Captcha Settings'),
											array('plugin' => null, 'controller' => 'settings', 'action' => 'captcha')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Cron Settings'),
											array('plugin' => null, 'controller' => 'settings', 'action' => 'cron')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Promotional Banners'),
											array('plugin' => null, 'controller' => 'banners', 'action' => 'settings')
										)
									?></li>
									<li class="dropdown-header"><?=__d('admin', 'Page Content')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Terms of Service'),
											array('plugin' => null, 'controller' => 'settings', 'action' => 'tos')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Privacy Policy'),
											array('plugin' => null, 'controller' => 'settings', 'action' => 'privacy')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'FAQ'),
											array('plugin' => null, 'controller' => 'settings', 'action' => 'faq')
										)
									?></li>
									<li class="dropdown-header"><?=__d('admin', 'Memberships')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'List Memberships'),
											array('plugin' => null, 'controller' => 'memberships', 'action' => 'index')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Add Membership'),
											array('plugin' => null, 'controller' => 'memberships', 'action' => 'add')
										)
										?></li>
									<li class="dropdown-header"><?=__d('admin', 'Referrals')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Renting Referrals'),
											array('plugin' => null, 'controller' => 'settings', 'action' => 'rentingReferrals')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Buying Referrals'),
											array('plugin' => null, 'controller' => 'settings', 'action' => 'buyingReferrals')
										)
										?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Assign Referrals'),
											array('plugin' => null, 'controller' => 'settings', 'action' => 'assignReferrals')
										)
										?></li>
									<?php if(Module::active('BotSystem')): ?>
										<li>
											<?=
												$this->Html->link(__d('admin', 'Bot System'),
													array('plugin' => 'bot_system', 'controller' => 'bot_system', 'action' => 'settings')
												)
											?>
										</li>
									<?php endif; ?>
									<li class="dropdown-header"><?=__d('admin', 'Payments Settings')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Payments'),
											array('plugin' => null, 'controller' => 'settings', 'action' => 'payments')
										)
									?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Promotion Settings'),
											array('plugin' => null, 'controller' => 'settings', 'action' => 'promotions')
										)
									?></li>
									<li class="dropdown-header"><?=__d('admin', 'System E-mails')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Edit system E-mails'),
											array('plugin' => null, 'controller' => 'settings', 'action' => 'emails')
										)
									?></li>
									<li class="dropdown-header"><?=__d('admin', 'Modules')?></li>
									<li><?=
										$this->Html->link(__d('admin', 'Manage Modules'),
											array('plugin' => null, 'controller' => 'modules', 'action' => 'index')
										)
									?></li>
									<?php if(Module::active('ReferralsContest')): ?>
										<li>
											<?=
												$this->Html->link(__d('admin', 'Referral Contest'),
													array('plugin' => 'referrals_contest', 'controller' => 'referrals_contest', 'action' => 'index')
												)
											?>
										</li>
									<?php endif; ?>
									<?php if(Module::active('FacebookLogin')): ?>
										<li>
											<?=
												$this->Html->link(__d('admin', 'Facebook Login'),
													array('plugin' => 'facebook_login', 'controller' => 'facebook_login', 'action' => 'settings')
												)
											?>
										</li>
									<?php endif; ?>
									<?php if(Module::active('RevenueShare')): ?>
										<li>
											<?=
												$this->Html->link(__d('admin', 'Revenue Share'),
													array('plugin' => 'revenue_share', 'controller' => 'revenue_share', 'action' => 'index')
												)
											?>
										</li>
									<?php endif; ?>
									<?php if(Module::active('AccurateLocationDatabase')): ?>
										<li>
											<?=
												$this->Html->link(__d('admin', 'Accurate Location Database'),
													array('plugin' => 'accurate_location_database', 'controller' => 'accurate_location_database_settings', 'action' => 'index')
												)
											?>
										</li>
									<?php endif; ?>
								</ul>
							</li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bomb"></i> <?=__d('admin', 'Anti-Cheat')?> <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<li><?=
										$this->Html->link(__d('admin', 'Blocking Options'),
											array('plugin' => null, 'controller' => 'antiCheat', 'action' => 'blocking')
										)
										?></li>
									</li>
									<li><?=
										$this->Html->link(__d('admin', 'EverCookie'),
											array('plugin' => null, 'controller' => 'antiCheat', 'action' => 'evercookie')
										)
										?></li>
									</li>
									<li><?=
										$this->Html->link(__d('admin', 'Search For Suspicious Users'),
											array('plugin' => null, 'controller' => 'antiCheat', 'action' => 'suspicious')
										)
										?></li>
									</li>
								</ul>
							</li>
						</div>
					</div>
				</nav>
			</div>
		</div>
		<!-- End of horizontal menu -->
		<div class="row">
		<div class="container-fluid">
		<div class="col-md-12 box">
		<!-- Begin of page content -->
		<?php if($this->action != 'admin_home'): ?>
			<?=$this->Notice->show()?>
		<?php endif;?>
		<?php endif;?>
		<?=$this->fetch('content')?>
		<?php if($this->action != 'admin_login'):?>
		</div>
		<div class="modal fade" id="ajax-modal-container" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div id="ajax-modal" class="modal-content">
				</div>
			</div>
		</div>
		<div class="row">
		<div class="col-md-12 text-right footer">
		<?=__d('admin', 'FuturumClix.com - all right reserved')?>
		</div>
		</div>
		<!-- End container -->
		<?php endif;?>
		<script>
			$(document).ready(function(){
				$('[data-counter]').on('keyup', charCounter);
				$('[data-toggle=modal], [data-ajaxsource]').on('click', ajaxModal);
			});
		</script>
	<?=$this->Js->writeBuffer()?>
	<?=$this->fetch('postLink')?>
	</body>
</html>
