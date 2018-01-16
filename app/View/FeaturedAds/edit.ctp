<?=$this->element('userSidebar')?>
<div class="page">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 padding30-sides">
				<?=$this->element('userBreadcrumbs')?>
				<?=$this->Notice->show()?>
				<div class="panel">
					<div class="padding30-col">
						<div class="col-md-12">
							<h5><?=__('Featured Ads Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('controller' => 'featured_ads', 'action' => 'add'),
								'buy' => array('controller' => 'featured_ads', 'action' => 'buy'),
								'assign' => array('controller' => 'featured_ads', 'action' => 'assign'),
							))
							?>
						<div class="row padding30-col">
							<div class="col-md-12">
								<h5><?=__('Edit advertisement')?></h5>
								<div class="col-md-8 col-md-offset-2 margin30-top">
									<?=$this->UserForm->create('FeaturedAd')?>
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__('Title')?></div>
											<?=
												$this->UserForm->input('title', array(
													'palaceholder' => __('Enter your advertisement title'),
													'class' => 'form-control',
													'data-limit' => $titleMax,
													'data-counter' => 'titleCounter'
												))
												?>
											<div id="titleCounter" class="input-group-addon"><?=isset($this->request->data['FeaturedAd']['title']) ? strlen($this->request->data['FeaturedAd']['title']) : 0?> / <?=$titleMax?></div>
										</div>
									</fieldset>
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__('Description')?></div>
											<?=
												$this->UserForm->input('description', array(
													'type' => 'textarea',
													'class' => 'form-control',
													'placeholder' => __('Enter your advertisement description'),
													'style' => 'resize:none;',
													'data-limit' => $descMax,
													'data-counter' => 'descCounter',
												))
												?>
											<div id="descCounter" class="input-group-addon"><?=isset($this->request->data['FeaturedAd']['description']) ? strlen($this->request->data['FeaturedAd']['description']) : 0?> / <?=$descMax?></div>
										</div>
									</fieldset>
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__('URL')?></div>
											<?=
												$this->UserForm->input('url', array(
													'class' => 'form-control',
													'placeholder' => 'http://',
												))
												?>
										</div>
									</fieldset>
									<div class="row">
										<div class="col-sm-12 text-xs-right">
											<button class="btn btn-primary"><?=__('Save')?></button>
										</div>
									</div>
								</div>
								<?=$this->UserForm->end()?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
