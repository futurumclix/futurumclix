<div class="sidebar collapse navbar-toggleable-sm" id="sidebar">
	<div class="memberpanel">
		<?=$this->Forum->avatar($user, 64)?>
		<h4><?=h($user['User']['first_name'].' '.$user['User']['last_name'])?></h4>
		<?php if($user['ActiveMembership']['period'] == 'Default'): ?>
		<?=__('Default Membership')?><br/>
		<?php else: ?>
		<h6><?=h($user['ActiveMembership']['Membership']['name'])?></h6>
		<h6><?=__('Valid Until: ')?><?=$this->Time->format($user['ActiveMembership']['ends'], '%Y-%m-%d');?></h6>
		<?php endif; ?>
		<div class="upgradebutton">
			<a href="/memberships"><i title="<?=__('Upgrade Your Membership')?>" data-toggle="tooltip" data-placement="top" class="fa fa-chevron-up"></i></a>
		</div>
	</div>
	<ul class="nav">
		<li class="section-title" data-toggle="collapse" href="#personalsettings" aria-expanded="false"><i class="fa fa-gear"></i><span><?=__('Personal Settings')?></span></li>
		<div class="collapse" id="personalsettings">
			<div class="linkspanel">
				<?php if(Configure::read('supportEnabled')): ?>
					<li class="nav-item"><?=$this->Html->link(__('Support System'), array('plugin' => null, 'controller' => 'support', 'action' => 'index'), array('class' => 'nav-link'))?></li>
				<?php endif; ?>
				<li class="nav-item"><?=$this->Html->link(__('Edit Profile'), array('plugin' => null, 'controller' => 'user_profiles', 'action' => 'edit', $user['User']['id']), array('class' => 'nav-link'))?></li>
				<li class="nav-item"><?=$this->Html->link(__('Account Security'), array('plugin' => null, 'controller' => 'user_profiles', 'action' => 'security', $user['User']['id']), array('class' => 'nav-link'))?></li>
				<li class="nav-item"><?=$this->Html->link(__('Upgrade Your Account'), array('plugin' => null, 'controller' => 'memberships'), array('class' => 'nav-link'))?></li>
				<li class="nav-item"><?=$this->Html->link(__('Fund Purchase Balance'), array('plugin' => null, 'controller' => 'users', 'action' => 'deposit'), array('class' => 'nav-link'))?></li>
				<li class="nav-item"><?=$this->Html->link(__('Cashout'), array('plugin' => null, 'controller' => 'users', 'action' => 'cashout'), array('class' => 'nav-link'))?></li>
				<li class="nav-item"><?=$this->Html->link(__('Purchase History'), array('plugin' => null, 'controller' => 'purchase_history', 'action' => 'index'), array('class' => 'nav-link'))?></li>
				<li class="nav-item"><?=$this->Html->link(__('Cashout History'), array('plugin' => null, 'controller' => 'cashouts', 'action' => 'index'), array('class' => 'nav-link'))?></li>
				<?php if($user['ActiveMembership']['Membership']['points_enabled'] && $user['ActiveMembership']['Membership']['points_conversion']): ?>
					<li class="nav-item"><?=$this->Html->link(__('Exchange Points'), array('plugin' => null, 'controller' => 'tools', 'action' => 'points_exchange'), array('class' => 'nav-link'))?></li>
				<?php endif; ?>
				<?php if(Module::active('Offerwalls')): ?>
					<li class="nav-item"><?=$this->Html->link(__('Offerwalls Log'), array('plugin' => null, 'controller' => 'offerwalls', 'action' => 'postbackLog'), array('class' => 'nav-link'))?></li>
				<?php endif; ?>
			</div>
		</div>
		<li class="section-title" data-toggle="collapse" href="#advertisement" aria-expanded="false"><i class="fa fa-bar-chart"></i><span><?=__('Advertisements')?></span></li>
		<div class="collapse" id="advertisement">
			<div class="linkspanel">
				<li class="nav-item"><?=$this->Html->link(__('PTC Advertisement Panel'), array('plugin' => null, 'controller' => 'ads', 'action' => 'index'), array('class' => 'nav-link'))?></li>
				<?php if(Configure::read('expressAdsActive')): ?>
					<li class="nav-item"><?=$this->Html->link(__('Express Advertisement Panel'), array('plugin' => null, 'controller' => 'express_ads', 'action' => 'index'), array('class' => 'nav-link'))?></li>
				<?php endif; ?>
				<?php if(Configure::read('explorerAdsActive')): ?>
					<li class="nav-item"><?=$this->Html->link(__('Explorer Advertisement Panel'), array('plugin' => null, 'controller' => 'explorer_ads', 'action' => 'index'), array('class' => 'nav-link'))?></li>
				<?php endif; ?>
				<?php if(Configure::read('featuredAdsActive')): ?>
					<li class="nav-item"><?=$this->Html->link(__('Featured Advertisement Panel'), array('plugin' => null, 'controller' => 'featuredAds', 'action' => 'index'), array('class' => 'nav-link'))?></li>
				<?php endif; ?>
				<?php if(Configure::read('bannerAdsActive')): ?>
					<li class="nav-item"><?=$this->Html->link(__('Banner Advertisement Panel'), array('plugin' => null, 'controller' => 'BannerAds', 'action' => 'index'), array('class' => 'nav-link'))?></li>
				<?php endif; ?>
				<?php if(Configure::read('loginAdsActive')): ?>
					<li class="nav-item"><?=$this->Html->link(__('Login Ads Advertisement Panel'), array('plugin' => null, 'controller' => 'LoginAds', 'action' => 'index'), array('class' => 'nav-link'))?></li>
				<?php endif; ?>
				<?php if(Configure::read('paidOffersActive')): ?>
					<li class="nav-item"><?=$this->Html->link(__('Paid Offers Panel'), array('plugin' => null, 'controller' => 'paid_offers', 'action' => 'index'), array('class' => 'nav-link'))?></li>
				<?php endif; ?>
				<?php if(Module::active('AdGrid')): ?>
					<li class="nav-item"><?=$this->Html->link(__('AdGrid Advertisement Panel'), array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'index'), array('class' => 'nav-link'))?></li>
				<?php endif; ?>
			</div>
		</div>
		<li class="section-title" data-toggle="collapse" href="#referrals" aria-expanded="false"><i class="fa fa-users"></i><span><?=__('Referrals')?></span></li>
		<div class="collapse" id="referrals">
			<div class="linkspanel">
				<li class="nav-item"><?=$this->Html->link(__('Promotion Tools'), array('plugin' => null, 'controller' => 'tools', 'action' => 'promotion'), array('class' => 'nav-link'))?></li>
				<li class="nav-item"><?=$this->Html->link(__('Direct Referrals'), array('plugin' => null, 'controller' => 'users', 'action' => 'directReferrals'), array('class' => 'nav-link'))?></li>
				<?php if(Module::active('ReferralsContest')): ?>
					<li class="nav-item"><?=$this->Html->link(__('Referral\'s Contest'), array('plugin' => 'referrals_contest', 'controller' => 'referrals_contest', 'action' => 'index'), array('class' => 'nav-link'))?></li>
				<?php endif; ?>
				<?php if(Configure::read('enableRentingReferrals')): ?>
					<li class="nav-item"><?=$this->Html->link(__('Rented Referrals'), array('plugin' => null, 'controller' => 'users', 'action' => 'rentedReferrals'), array('class' => 'nav-link'))?></li>
					<li class="nav-item"><?=$this->Html->link(__('Rent Referrals'), array('plugin' => null, 'controller' => 'users', 'action' => 'rentReferrals'), array('class' => 'nav-link'))?></li>
				<?php endif; ?>
				<?php if(Configure::read('enableBuyingReferrals')): ?>
					<li class="nav-item"><?=$this->Html->link(__('Buy Referrals'), array('plugin' => null, 'controller' => 'users', 'action' => 'buyReferrals'), array('class' => 'nav-link'))?></li>
				<?php endif; ?>
				<li class="nav-item"><?=$this->Html->link(__('Referral\'s Commissions'), array('plugin' => null, 'controller' => 'commissions'), array('class' => 'nav-link'))?></li>
			</div>
		</div>
	</ul>
	<div class="social text-xs-center">
		<i class="fa fa-facebook"></i>
		<i class="fa fa-twitter"></i>	
		<i class="fa fa-google-plus"></i>
	</div>
</div>
<?php $news = $this->News->getNews(1, true); $news = reset($news); ?>
<div class="modal fade" id="LoginAds" tabindex="-1" role="dialog" aria-labelledby="LoginAdsLabel">
	<div class="modal-dialog" role="document">
	 <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="<?=__('Close')?>"><span aria-hidden="true">&times;</span></button>
			<h5 class="modal-title" id="LoginAdsLabel">
				<?php if(empty($news)): ?>
					<?=__('Login Ads')?>
				<?php else: ?>
					<?=$news['News']['title']?>
				<?php endif;?>
			</h5>
		</div>
		<div class="modal-body">
			<?php if(!empty($news)): ?>
				<?=$news['News']['content']?>
			<?php endif; ?>
			<div class="text-xs-center">
				<?=$this->LoginAds->box()?>
			</div>
		</div>
		<div class="modal-footer" style="text-align: center;">
			<button type="button" class="btn btn-primary" data-dismiss="modal"><?=__('Close')?></button>
		</div>
	 </div>
	</div>
</div>
