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
			<h2 class="uk-margin-top"><?=__('Edit advertisement')?></h2>
			<?=$this->UserForm->create('FeaturedAd', array('class' => 'uk-form-horizontal'))?>
			<div class="uk-margin">
				<label class="uk-form-label">
					<?=__('Title')?>
					<div id="titleCounter" class="uk-badge"><?=isset($this->request->data['FeaturedAd']['title']) ? strlen($this->request->data['FeaturedAd']['title']) : 0?> / <?=$titleMax?></div>
				</label>
				<?=
					$this->UserForm->input('title', array(
					'palaceholder' => __('Enter your advertisement title'),
					'class' => 'uk-input',
					'data-limit' => $titleMax,
					'data-counter' => 'titleCounter'
					))
					?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label">
					<?=__('Description')?>
					<div id="descCounter" class="uk-badge"><?=isset($this->request->data['FeaturedAd']['description']) ? strlen($this->request->data['FeaturedAd']['description']) : 0?> / <?=$descMax?></div>
				</label>
				<?=
					$this->UserForm->input('description', array(
					'type' => 'textarea',
					'class' => 'uk-textarea',
					'placeholder' => __('Enter your advertisement description'),
					'style' => 'resize:none;',
					'data-limit' => $descMax,
					'data-counter' => 'descCounter',
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
			<div class="uk-margin uk-text-right">
				<button class="uk-button uk-button-primary"><?=__('Save')?></button>
			</div>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
