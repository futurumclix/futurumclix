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
			<h2 class="uk-margin-top">
			<?=__('Edit advertisement')?></h5>
			<?=
				$this->UserForm->create('Ad', array(
				'url' => array('action' => 'preview'),
				'class' => 'uk-form-horizontal'
				))
				?>
			<?=$this->UserForm->input('id')?>
			<div class="uk-margin">
				<label class="uk-form-label">
					<?=__('Title')?>
					<div id="titleCounter" class="uk-badge"><?=isset($this->request->data['Ad']['title']) ? strlen($this->request->data['Ad']['title']) : 0?> / <?=$titleMax?></div>
				</label>
				<?=
					$this->UserForm->input('title', array(
					'placeholder' => __('Enter your advertisement title'),
					'class' => 'uk-input',
					'data-limit' => $titleMax,
					'data-counter' => 'titleCounter',
					))
					?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label">
					<?=__('Description')?>
					<div id="descCounter" class="uk-badge"><?=isset($this->request->data['Ad']['description']) ? strlen($this->request->data['Ad']['description']) : 0?> / <?=$descMax?></div>
				</label>
				<?=
					$this->UserForm->input('description', array(
					'placeholder' => __('Enter your advertisement description'),
					'class' => 'uk-input',
					'data-limit' => $descMax,
					'data-counter' => 'descCounter'
					))
					?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('URL')?></label>
				<div class="uk-form-control">
					<?=
						$this->UserForm->input('url', array(
						'placeholder' => 'http://',
						'class' => 'uk-input',
						))
						?>
				</div>
			</div>
			<div class="uk-margin">
				<label>
				<?=$this->UserForm->input('hide_referer', array('class' => 'uk-checkbox'))?>
				<span title="<?=__('Hide source of the traffic.')?>" uk-tooltip><?=__('White label my traffic')?></span>
				</label>
				<div class="uk-margin uk-text-right">
					<button class="uk-button uk-button-primary"><?=__('Save')?></button>
				</div>
				<?=$this->UserForm->end()?>
			</div>
		</div>
	</div>
</div>
