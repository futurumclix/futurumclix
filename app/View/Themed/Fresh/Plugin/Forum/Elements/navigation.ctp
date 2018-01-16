<div class="topbar">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1 uk-text-right toplinks">
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
							<?php
								if ($user) {
								?>
							<li><?=$this->Html->link(__('Home Page'), array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home'))?></li>
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
							<li><?=$this->Html->link($user[$userFields['username']], $this->Forum->profileUrl($user))?></li>
							<li><?=$this->Html->link(__d('forum', 'View Recent Topics'), array('controller' => 'search', 'action' => 'index', 'new_posts', 'admin' => false))?></li>
							<?php
								} else {
								?>
							<li><?=$this->Html->link(__d('forum', 'Login'), $userRoutes['login'])?></li>
							<?php
								if (!empty($userRoutes['signup'])) {
								?>
							<li><?=$this->Html->link(__d('forum', 'Sign Up'), $userRoutes['signup'])?></li>
							<?php
								}
								if (!empty($userRoutes['forgotPass'])) {
								?>
							<li><?=$this->Html->link(__d('forum', 'Forgot Password'), $userRoutes['forgotPass'])?></li>
							<?php
								}
								} ?>
							<li><?php echo $this->Html->link(__d('forum', 'Forums'), array('controller' => 'forum', 'action' => 'index')); ?></li>
							<li><?php echo $this->Html->link(__d('forum', 'Search'), array('controller' => 'search', 'action' => 'index')); ?></li>
							<?php if($config['Forum']['ToSActive']) { ?>
							<li><?php echo $this->Html->link(__d('forum', 'Rules'), array('controller' => 'forum', 'action' => 'rules')); } ?></li>
							<?php if($config['Forum']['helpActive']) { ?>
							<li><?php echo $this->Html->link(__d('forum', 'Help'), array('controller' => 'forum', 'action' => 'help')); } ?></li>
						</ul>
					</div>
				</nav>
			</div>
		</div>
	</div>
</div>
