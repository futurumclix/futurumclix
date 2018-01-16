<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Express Advertisements Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'express_ads', 'action' => 'add'),
				'buy' => array('controller' => 'express_ads', 'action' => 'buy'),
				'assign' => array('controller' => 'express_ads', 'action' => 'assign'),
				))
				?>
			<?php if(empty($packages)): ?>
			<h5><?=__('Sorry, you do not have any Express Ads packages for this ad. You will be automatically redirected to purchase advertisement exposures after 5 seconds.')?></h5>
			<?php $this->Js->buffer("
				window.setTimeout(function() {
				window.location.href = '".Router::url(array('controller' => 'express_ads', 'action' => 'buy'))."';
				}, 5000);
				")?>
			<?php else: ?>
			<h2 class="uk-margin-top"><?=__('Assign advertisement click packages')?></h2>
			<?=$this->UserForm->create(false, array('class' => 'uk-form-horizontal'))?>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Choose Package')?></label>
				<?=$this->UserForm->input('package_id', array('class' => 'uk-select'))?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Advertisement')?></label>
				<?=$this->UserForm->input('ad_id', array('class' => 'uk-select', 'disabled' => true))?>
			</div>
			<div class="uk-margin uk-text-right">
				<button class="uk-button uk-button-primary"><?=__('Assign')?></button>
			</div>
			<?=$this->UserForm->end()?>
			<?php endif; ?>
		</div>
	</div>
</div>
