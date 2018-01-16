	<div class="container">
		<div class="row aboutpanels">
			<div class="col-sm-6 panelone">
				<h6>Something about us</h6>
				<h1>You can earn extra money online!</h1>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.</p>
			</div>
			<div class="col-sm-6 paneltwo">
				<h6>Why to advertise with us?</h6>
				<h1>Internet is large advertisement center.</h1>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.</p>
			</div>
			<div class="col-sm-6 panelthree">
				<h6>How to earn money</h6>
				<h1>It's easy and safe!</h1>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.</p>
			</div>
			<div class="col-sm-6 panelfour">
				<h6>And finally...</h6>
				<h1>Just register, with one click!</h1>
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud.</p>
			</div>
		</div>
	</div>
	
	<div class="container">
		<div class="row morepanel">
			<div class="col-sm-12 text-xs-center">
				<?=$this->Html->link(__('Learn more about our platform'), array('plugin' => null, 'controller' => 'pages', 'action' => 'content', 'faq'), array('class' => 'btn btn-primary'))?>
				<br />
				<span><?=__('or')?></span><br />
				<?=$this->Html->link(__('Sign up, now!'), array('plugin' => null, 'controller' => 'users', 'action' => 'signup'))?>
				<?php
					if(Module::active('FacebookLogin')) {echo $this->FacebookLogin->link('Signup with <i class="fa fa-facebook" aria-hidden="true"></i>', array('escape' => false));}
				?>
			</div>
		</div>
	</div>
	
	<div class="logos">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<?=$this->Html->image('logotype.png')?>
					<?=$this->Html->image('logotype1.png')?>
					<?=$this->Html->image('logotype2.png')?>
					<?=$this->Html->image('logotype3.png')?>
					<?=$this->Html->image('logotype4.png')?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="stats">
		<div class="container">
			<div class="row">
				<div class="col-sm-3 text-xs-center">
					<h6><?=__('Registered users')?></h6>
					<h1><?=$stats['users']?></h6>
				</div>
				<div class="col-sm-2 text-xs-center">
					<h6><?=__('Users yesterday')?></h6>
					<h1><?=$stats['yesterday_users']?></h6>
				</div>
				<div class="col-sm-2 text-xs-center">
					<?php if(isset($stats['users_online'])): ?>
						<h6><?=__('Users online')?></h6>
						<h1><?=$stats['users_online']?></h6>
					<?php endif ;?>
				</div>
				<div class="col-sm-2 text-xs-center">
					<h6><?=__('Paid so far')?></h6>
					<h1><?=$this->Currency->format($stats['total_cashouts'])?></h6>
				</div>
				<div class="col-sm-3 text-xs-center">
					<h6><?=__('Ads watched')?></h6>
					<h1><?=$stats['clicks']?></h6>
				</div>
			</div>
		</div>
	</div>
	<div class="news">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h1><?=__('Latest news')?></h1>
				</div>
				<?=$this->News->box(3)?>
			</div>
		</div>
	</div>
