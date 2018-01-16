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
					<li class="uk-active"><?=__('Manual Payment')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<?=$this->Notice->show()?>
			<h6 class="uk-text-center"><?=h($payment['title'])?></h6>
			<h6 class="uk-text-center">Please transfer <?=h($payment['amount'])?> <?=h($payment['currency'])?> via <?=h($payment['gateway'])?> to <a href="mailto:<?=h($payment['to_account'])?>"><?=h($payment['to_account'])?></a></h6>
			<?=$this->UserForm->create(false)?>
			<div class="uk-text-center">
				<button class="uk-button uk-button-primary"><?=__('I have paid')?></button>
			</div>
			<?=$this->UserForm->end()?>
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
			<h2 class="uk-margin-top uk-text-center"><?=__('Manual Payment')?></h2>
			<h6 class="uk-text-center"><?=h($payment['title'])?></h6>
			<h6 class="uk-text-center">Please transfer <?=h($payment['amount'])?> <?=h($payment['currency'])?> via <?=h($payment['gateway'])?> to <a href="mailto:<?=h($payment['to_account'])?>"><?=h($payment['to_account'])?></a></h6>
			<?=$this->UserForm->create(false)?>
			<div class="uk-text-center">
				<button class="uk-button uk-button-primary"><?=__('I have paid')?></button>
			</div>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
<?php endif; ?>
