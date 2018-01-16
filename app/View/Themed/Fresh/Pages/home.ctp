<div class="mainslider">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-2@m">
				<h1>Welcome</h1>
				<h2>Now you can multiply your earning just by viewing advertisements.</h2>
				<h3>At FuturumClix you get paid just for browsing our advertisers websites.</h3>
				<div class="uk-inline">
					<?=$this->Html->link(__('Sign up, now!'), array('plugin' => null, 'controller' => 'users', 'action' => 'signup'), array('class' => 'registernow'))?>
					<?php
						if(Module::active('FacebookLogin')) {echo $this->FacebookLogin->link('Signup with <i class="mdi mdi-facebook" aria-hidden="true"></i>', array('escape' => false, 'class' => 'registernow'));}
						?>
					<?=$this->Html->link(__('Learn more about our platform'), array('plugin' => null, 'controller' => 'pages', 'action' => 'content', 'faq'), array('class' => 'moreinfo'))?>
				</div>
			</div>
			<div class="uk-width-1-2@m uk-text-center sliderimage">
				<?=$this->Html->image('ipad.png', array('class' => ''))?>
			</div>
		</div>
	</div>
</div>
<div class="adverts">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-2@m uk-text-center">
				<?=$this->BannerAds->show()?>
			</div>
			<div class="uk-width-1-2@m uk-text-center">
				<?=$this->BannerAds->show()?>
			</div>
		</div>
	</div>
</div>
<div class="panels">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-3@m">
				<div class="panelone">
					<h5><?=__('Registered users')?></h5>
					<h4><?=$stats['users']?></h4>
					<p>As a member you can earn simply by viewing all the advertisements we display.</p>
					<ul>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Lorem ipsum dolor sit amet</li>
					</ul>
				</div>
			</div>
			<div class="uk-width-1-3@m">
				<div class="paneltwo">
					<h5><?=__('Ads watched')?></h5>
					<h4><?=$stats['clicks']?></h4>
					<p>You can advertise with us and reach huge audience of users.</p>
					<ul>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Lorem ipsum dolor sit amet</li>
					</ul>
				</div>
			</div>
			<div class="uk-width-1-3@m">
				<div class="panelthree">
					<h5><?=__('Paid so far')?></h5>
					<h4><?=$this->Currency->format($stats['total_cashouts'])?></h4>
					<p>Our company is registered and legacy, we are established business.</p>
					<ul>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Lorem ipsum dolor sit amet</li>
						<li>Lorem ipsum dolor sit amet</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="bottompanels">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-3@m news">
				<h2><?=__('Latest news')?></h2>
				<?=$this->News->box(1)?>
			</div>
			<div class="uk-width-2-3@m">
				<h2>Advertisements</h2>
				<?=$this->FeaturedAds->box('2')?>
			</div>
		</div>
	</div>
</div>
<div class="logos">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1 uk-text-center">
				<?=$this->Html->image('paypal.png')?>
				<?=$this->Html->image('payza.png')?>
				<?=$this->Html->image('neteller.png')?>
				<?=$this->Html->image('trustwave.png')?>
				<?=$this->Html->image('sitelock.png')?>
			</div>
		</div>
	</div>
</div>
