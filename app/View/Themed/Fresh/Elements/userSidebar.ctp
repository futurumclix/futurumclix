<div class="uk-width-1-4 uk-visible@m uk-margin-top">
	<div class="sidebar">
		<div class="memberpanel">
			<?=$this->Forum->avatar($user, 64)?>
			<h4><?=h($user['User']['first_name'].' '.$user['User']['last_name'])?></h4>
			<?php if($user['ActiveMembership']['period'] == 'Default'): ?>
			<h6><?=__('Default Membership')?></h6>
			<?php else: ?>
			<h6><?=h($user['ActiveMembership']['Membership']['name'])?></h6>
			<h6><?=__('Valid Until: ')?><?=$this->Time->format($user['ActiveMembership']['ends'], '%Y-%m-%d');?></h6>
			<?php endif; ?>
			<div class="upgradebutton">
				<a href="/memberships"><i title="<?=__('Upgrade Your Membership')?>" data-uk-tooltip class="mdi mdi-arrow-up-drop-circle mdi-18px"></i></a>
			</div>
		</div>
		<ul class="uk-nav uk-nav-default uk-nav-parent-icon">
			<li class="uk-nav-header"><?=__('Personal Settings')?></li>
			<?php if(Configure::read('supportEnabled')): ?>
			<li><?=$this->Html->link(__('Support System'), array('plugin' => null, 'controller' => 'support', 'action' => 'index'))?></li>
			<?php endif; ?>
			<li><?=$this->Html->link(__('Edit Profile'), array('plugin' => null, 'controller' => 'user_profiles', 'action' => 'edit'))?></li>
			<li><?=$this->Html->link(__('Account Security'), array('plugin' => null, 'controller' => 'user_profiles', 'action' => 'security'))?></li>
			<li><?=$this->Html->link(__('Upgrade Your Account'), array('plugin' => null, 'controller' => 'memberships'))?></li>
			<li><?=$this->Html->link(__('Fund Purchase Balance'), array('plugin' => null, 'controller' => 'users', 'action' => 'deposit'))?></li>
			<li><?=$this->Html->link(__('Cashout'), array('plugin' => null, 'controller' => 'users', 'action' => 'cashout'))?></li>
			<li><?=$this->Html->link(__('Purchase History'), array('plugin' => null, 'controller' => 'purchase_history', 'action' => 'index'))?></li>
			<li><?=$this->Html->link(__('Cashout History'), array('plugin' => null, 'controller' => 'cashouts', 'action' => 'index'))?></li>
			<?php if($user['ActiveMembership']['Membership']['points_enabled'] && $user['ActiveMembership']['Membership']['points_conversion']): ?>
					<li><?=$this->Html->link(__('Exchange Points'), array('plugin' => null, 'controller' => 'tools', 'action' => 'points_exchange'))?></li>
				<?php endif; ?>
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
		</ul>
	</div>
</div>
<?php $news = $this->News->getNews(1, true); $news = reset($news); ?>
<div class="uk-modal" id="LoginAds" tabindex="-1" role="dialog" aria-labelledby="LoginAdsLabel">
	<div class="uk-modal-dialog uk-modal-body" role="document">
		<h5 id="LoginAdsLabel" class="uk-modal-title">
			<?php if(empty($news)): ?>
			<?=__('Login Ads')?>
			<?php else: ?>
			<?=$news['News']['title']?>
			<?php endif;?>
		</h5>
		<p>
			<?php if(!empty($news)): ?>
			<?=$news['News']['content']?>
			<?php endif; ?>
		<div class="uk-text-center">
			<?=$this->LoginAds->box()?>
		</div>
		</p>
		<div class="uk-text-center">
			<button type="button" class="uk-button medium black uk-modal-close" data-dismiss="modal"><?=__('Close')?></button>
		</div>
	</div>
</div>
