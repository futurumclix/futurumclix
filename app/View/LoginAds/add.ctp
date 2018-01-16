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
							<h5><?=__('Login Ads Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('controller' => 'login_ads', 'action' => 'add'),
								'buy' => array('controller' => 'login_ads', 'action' => 'buy'),
								'assign' => array('controller' => 'login_ads', 'action' => 'assign'),
							))
							?>
						<div class="padding30-col">
							<h5><?=__('Add new advertisement')?></h5>
							<div class="col-md-8 col-md-offset-2 margin30-top">
								<?=$this->UserForm->create('LoginAd')?>
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
										<div id="titleCounter" class="input-group-addon"><?=isset($this->request->data['LoginAd']['title']) ? strlen($this->request->data['LoginAd']['title']) : 0?> / <?=$titleMax?></div>
									</div>
								</fieldset>
								<fieldset class="form-group">
									<div class="input-group">
										<div class="input-group-addon"><?=__('Image URL')?></div>
										<?=
											$this->UserForm->input('image_url', array(
												'class' => 'form-control',
												'placeholder' => 'http://',
												'style' => 'resize:none;',
											))
											?>
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
									<div class="col-md-12 text-xs-right">
										<button class="btn btn-primary"><?=__('Add')?></button>
									</div>
								</div>
								<?=$this->UserForm->end()?>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>