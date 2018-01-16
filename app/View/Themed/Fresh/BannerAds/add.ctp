<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Banner Ads Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'banner_ads', 'action' => 'add'),
				'buy' => array('controller' => 'banner_ads', 'action' => 'buy'),
				'assign' => array('controller' => 'banner_ads', 'action' => 'assign'),
				))
				?>
			<h2 class="uk-margin-top"><?=__('Add new advertisement')?></h2>
			<?=$this->UserForm->create('BannerAd', array('class' => 'uk-form-horizontal'))?>
			<div class="uk-margin">
				<label class="uk-form-label">
					<?=__('Title')?>
					<div id="titleCounter" class="uk-badge"><?=isset($this->request->data['BannerAd']['title']) ? strlen($this->request->data['BannerAd']['title']) : 0?> / <?=$titleMax?></div>
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
				<label class="uk-form-label"><?=__('Image URL')?></label>
				<?=
					$this->UserForm->input('image_url', array(
					'class' => 'uk-input',
					'placeholder' => 'http://',
					'style' => 'resize:none;',
					))
					?>
			</div>
			<div class="uk-margin">
				<label class="uk-form-label"><?=__('URL')?></label>
				<?=
					$this->UserForm->input('url', array(
					'class' => 'uk-input',
					'placeholder' => 'http://',
					))
					?>
			</div>
			<div class="uk-margin uk-text-right">
				<button class="uk-button uk-button-primary"><?=__('Add')?></button>
			</div>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
