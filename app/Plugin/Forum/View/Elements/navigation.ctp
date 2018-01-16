<nav class="navbar">
	<div class="navbar-toggle">
		<button class="navbar-toggler hidden-md-up" type="button" data-toggle="collapse" data-target="#mainnavbar">&#9776;</button>
	</div>
	</a>
	<?php 
		echo $this->Html->link(
			$this->Html->image('logo_dashboard_bw.png', array('alt' => 'Logo', 'class' => 'navbar-logo')),
			array('plugin' => '', 'controller' => 'pages', 'action' => 'display', 'home'),
			array('escape' => false, 'class' => 'navbar-brand white')
		);
		?>
	<div class="collapse navbar-toggleable-sm" id="mainnavbar">
		<ul class="nav navbar-nav pull-xs-right">
			<?php
				if ($user) {
				?>
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
			<li class="nav-item"><?=$this->Html->link($user[$userFields['username']], $this->Forum->profileUrl($user), array('class' => 'button'))?></li>
			<li class="nav-item"><?=$this->Html->link(__d('forum', 'View Recent Topics'), array('controller' => 'search', 'action' => 'index', 'new_posts', 'admin' => false), array('class' => 'button'))?></li>
			<li class="nav-item"><?=$this->Html->link(__d('forum', 'Logout'), $userRoutes['logout'], array('class' => 'button error'))?></li>
			<?php
				} else {
				?>
			<li class="nav-item"><?=$this->Html->link(__d('forum', 'Login'), $userRoutes['login'], array('class' => 'button'))?></li>
			<?php
				if (!empty($userRoutes['signup'])) {
				?>
			<li class="nav-item"><?=$this->Html->link(__d('forum', 'Sign Up'), $userRoutes['signup'], array('class' => 'button'))?></li>
			<?php
				}
				
				if (!empty($userRoutes['forgotPass'])) {
				?>
			<li class="nav-item"><?=$this->Html->link(__d('forum', 'Forgot Password'), $userRoutes['forgotPass'], array('class' => 'button'))?></li>
			<?php
				}
				} ?>
			<li class="nav-item"><?php echo $this->Html->link(__d('forum', 'Forums'), array('controller' => 'forum', 'action' => 'index')); ?></li>
			<li class="nav-item"><?php echo $this->Html->link(__d('forum', 'Search'), array('controller' => 'search', 'action' => 'index')); ?></li>
			<?php if($config['Forum']['ToSActive']) { ?>
			<li class="nav-item"><?php echo $this->Html->link(__d('forum', 'Rules'), array('controller' => 'forum', 'action' => 'rules')); } ?></li>
			<?php if($config['Forum']['helpActive']) { ?>
			<li class="nav-item"><?php echo $this->Html->link(__d('forum', 'Help'), array('controller' => 'forum', 'action' => 'help')); } ?></li>
		</ul>
	</div>
</nav>
