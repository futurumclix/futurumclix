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
					<li class="uk-active"><?=__('Payment Canceled')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Payment Canceled')?></h2>
			<h6 class="uk-text-center">
				<?=__('We are sorry but for some reason your payment was canceled.')?>
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
			<h2 class="uk-margin-top uk-text-center"><?=__('Payment Canceled')?></h2>
			<h6 class="uk-text-center">
				<?=__('We are sorry but for some reason your payment was canceled.')?>
			</h6>
		</div>
	</div>
</div>
<?php endif; ?>
