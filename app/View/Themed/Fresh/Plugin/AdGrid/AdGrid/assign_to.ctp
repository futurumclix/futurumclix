<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__d('ad_grid', 'AdGrid Ads Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('action' => 'add'),
				'buy' => array('action' => 'buy'),
				'assign' => array('action' => 'assign'),
				))
				?>
			<?php if(empty($packages)): ?>
			<h5><?=__d('ad_grid', 'Sorry, you do not have any AdGrid Ads packages.')?></h5>
			<?php else: ?>
			<h2 class="uk-margin-top"><?=__d('ad_grid', 'Assign advertisement click packages')?></h2>
			<?=$this->UserForm->create(false, array('class' => 'uk-form-horizontal'))?>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__d('ad_grid', 'Choose package')?></label>
				<?=$this->UserForm->input('package_id', array('class' => 'uk-select'))?>
			</div>
			<div class="uk-margin uk-text-right">
				<button class="uk-button uk-button-primary"><?=__d('ad_grid', 'Assign')?></button>
			</div>
			<?=$this->UserForm->end()?>
			<?php endif; ?>
		</div>
	</div>
</div>
