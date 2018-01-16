<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Advertisement Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'ads', 'action' => 'add'),
				'buy' => array('controller' => 'ads_category_packages', 'action' => 'buy'),
				'assign' => array('controller' => 'ads', 'action' => 'assign'),
				))
				?>
			<h2 class="uk-margin-top"><?=$title?></h2>
			<?=$this->UserForm->create('Ad', array('class' => 'uk-form-horizontal'))?>
			<?php if($saveField == 'TargettedLocations' && Module::active('AccurateLocationDatabase')): ?>
			<?=$this->Locations->selector($options)?>
			<?php else: ?>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('Choose')?></label>
				<?=
					$this->UserForm->input($saveField, array(
					'type' => 'select',
					'class' => 'uk-textarea',
					'multiple' => 'multiple',
					'options' => $options,
					'style' => 'height: 300px;',
					'selected' => $selected, 
					))
					?>
			</div>
			<?php endif; ?>
			<div class="uk-margin uk-text-right">
				<button class="uk-button uk-button-primary"><?=__('Save')?></button>
			</div>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
