<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Featured Ads Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'featured_ads', 'action' => 'add'),
				'buy' => array('controller' => 'featured_ads', 'action' => 'buy'),
				'assign' => array('controller' => 'featured_ads', 'action' => 'assign'),
				))
				?>
			<?php if(empty($ads)): ?>
			<h5><?=__('Sorry, you do not have any not assigned approved advertisements.')?></h5>
			<?php elseif(empty($packages)): ?>
			<h5><?=__('Sorry, you do not have any Featured Ads packages.')?></h5>
			<?php else: ?>
			<h2 class="uk-margin-top"><?=__('Assign advertisement click packages')?></h2>
			<?=$this->UserForm->create(false, array('class' => 'uk-form-horizontal'))?>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Choose Package')?></label>
				<?=$this->UserForm->input('package_id', array('class' => 'uk-select'))?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Advertisement')?></label>
				<?=$this->UserForm->input('ad_id', array('class' => 'uk-select'))?>
			</div>
			<div class="uk-margin uk-text-right">
				<button class="uk-button uk-button-primary"><?=__('Assign')?></button>
			</div>
			<?=$this->UserForm->end()?>
			<?php endif; ?>
		</div>
	</div>
</div>
