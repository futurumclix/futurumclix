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
			<h2 class="uk-margin-top"><?=__d('ad_grid', 'Add new advertisement')?></h2>
			<?=
				$this->UserForm->create('AdGridAd', array(
				'url' => array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'preview'),
				'class' => 'uk-form-horizontal',
				))
				?>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__d('ad_grid', 'URL')?></label>
				<?=
					$this->UserForm->input('url', array(
					'placeholder' => 'http://',
					'class' => 'uk-input',
					))
					?>
			</div>
			<div class="uk-margin uk-text-right">
				<button class="uk-button uk-button-primary"><?=__d('ad_grid', 'Add')?></button>
			</div>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
