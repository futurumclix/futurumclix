<?php if(!isset($user)): ?>
<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=isset($data['label']) ? h($data['label']) : __('Payment Message')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top uk-text-center"><?=isset($data['label']) ? h($data['label']) : __('Payment Message')?></h2>
			<h6 class="uk-text-center">
				<?=__('Please pay %s BTC to %s', h($data['amount']), '<a href="'.$url.'">'.h($data['addr']).'</a>')?>
				<div class=uk-text-center">
					<img id="QRCode" src="https://chart.googleapis.com/chart?chs=295x295&cht=qr&chl=<?=h($url)?>&choe=UTF-8"/>
				</div>
			</h6>
		</div>
	</div>
</div>
<?php elseif(isset($user)): ?>
<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=isset($data['label']) ? h($data['label']) : __('Payment Message')?></h2>
			<h6 class="uk-text-center">
				<?=__('Please pay %s BTC to %s', h($data['amount']), '<a href="'.$url.'">'.h($data['addr']).'</a>')?>
				<div class=uk-text-center">
					<img id="QRCode" src="https://chart.googleapis.com/chart?chs=295x295&cht=qr&chl=<?=h($url)?>&choe=UTF-8"/>
				</div>
			</h6>
		</div>
	</div>
</div>
<?php endif; ?>
