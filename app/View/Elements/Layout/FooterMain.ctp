<div class="prefooter">
			<div class="container">
				<div class="row">
					<div class="col-sm-4 paneldown">
						<h3><?=__('Navigation')?></h3>
							<ul class="nav">
								<li><?=$this->Html->link(__('Home Page'), array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home'))?></li>
								<li><?=$this->Html->link(__('View Ads'), array('plugin' => null, 'controller' => 'AdsCategories'))?></li>
								<?php if(Module::active('AdGrid')): ?>
									<li><?=$this->Html->link(__('AdGrid'), array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'grid'))?></li>
								<?php endif; ?>
								<li><?=$this->Html->link(__('Advertise'), array('plugin' => null, 'controller' => 'advertise'))?></li>
								<?php if(Configure::read('Forum.active')): ?>
									<li><?=$this->Html->link(__('Forum'), array('plugin' => 'forum', 'controller' => 'forum', 'action' => 'index'))?></li>
								<?php endif; ?>
								<?php if(Configure::read('supportEnabled')): ?>
									<li><?=$this->Html->link(__('Support system'), array('plugin' => null, 'controller' => 'support', 'action' => 'index'))?></li>
								<?php endif; ?>
							</ul>
					</div>
					<div class="col-sm-4 paneldown">
						<h3><?=__('Resources')?></h3>
							<ul class="nav">
								<?php if(Configure::read('sitePrivacyPolicyActive')):?>
									<li><?=$this->Html->link(__('Privacy Policy'), array('plugin' => null, 'controller' => 'pages', 'action' => 'content', 'privacy'))?></li>
								<?php endif; ?>
								<?php if(Configure::read('siteFAQActive')):?>
									<li><?=$this->Html->link(__('FAQ'), array('plugin' => null, 'controller' => 'pages', 'action' => 'content', 'faq'))?></li>
								<?php endif; ?>
								<?php if(Configure::read('siteToSActive')):?>
									<li><?=$this->Html->link(__('ToS'), array('plugin' => null, 'controller' => 'pages', 'action' => 'content', 'tos'))?></li>
								<?php endif; ?>
								<li><?=$this->Html->link(__('Payment Proofs'), array('plugin' => null, 'controller' => 'cashouts', 'action' => 'payment_proofs'))?></li>
							</ul>
					</div>
					<div class="col-sm-4 paneldown">
						<?=$this->FeaturedAds->box('1')?>
					</div>
				</div>
			</div>
		</div>
		<div class="footer">
			<div class="container">
				<div class="row">
					<div class="col-sm-4 copyright">
						<!-- Do not remove this copyright unless you bought Copyright Removal from our shop. Otherwise your licence will be suspended! -->
						<p><a href="http://futurumclix.com">Powered by FuturumClix.com</a> &copy; 2014 - <?=date('Y')?></p>
					</div>
					<div class="col-sm-4 social text-xs-center">
						<i class="fa fa-facebook"></i>
						<i class="fa fa-twitter"></i>	
						<i class="fa fa-google-plus"></i>
					</div>
					<div class="col-sm-4 text-xs-right backtop">
						<a href="#top" id ="backToTopBtn" class="well well-sm"><i class="fa fa-arrow-up"></i></a>
					</div>
				</div>
			</div>
		</div>
<script>
$('#backToTopBtn').click(function(){
	$('html,body').animate({scrollTop:0},'slow');return false;
});
$(document).ready(function(){
	$("[data-toggle=tooltip]").tooltip({placement : 'top'});
	$("[data-toggle=popover]").popover({placement : 'top'});
})
</script>
<?=$this->Js->writeBuffer()?>
<?=$this->fetch('postLink')?>
</body>
</html>
