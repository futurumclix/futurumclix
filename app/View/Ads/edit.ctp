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
							<h5><?=__('Advertisement Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('controller' => 'ads', 'action' => 'add'),
								'buy' => array('controller' => 'ads_category_packages', 'action' => 'buy'),
								'assign' => array('controller' => 'ads', 'action' => 'assign'),
							))
						?>
						<div class="row padding30-col">
							<div class="col-md-12">
								<h5><?=__('Edit advertisement')?></h5>
								<div class="col-md-8 col-md-offset-2 margin30-top">
									<?=
										$this->UserForm->create('Ad', array(
											'url' => array('action' => 'preview'),
										))
										?>
									<?=$this->UserForm->input('id')?>
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__('Title')?></div>
											<?=
												$this->UserForm->input('title', array(
													'placeholder' => __('Enter your advertisement title'),
													'class' => 'form-control',
													'data-limit' => $titleMax,
													'data-counter' => 'titleCounter',
												))
												?>
											<div id="titleCounter" class="input-group-addon"><?=isset($this->request->data['Ad']['title']) ? strlen($this->request->data['Ad']['title']) : 0?> / <?=$titleMax?></div>
										</div>
									</fieldset>
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__('Description')?></div>
											<?=
												$this->UserForm->input('description', array(
													'placeholder' => __('Enter your advertisement description'),
													'class' => 'form-control',
													'data-limit' => $descMax,
													'data-counter' => 'descCounter'
												))
												?>
											<div id="descCounter" class="input-group-addon"><?=isset($this->request->data['Ad']['description']) ? strlen($this->request->data['Ad']['description']) : 0?> / <?=$descMax?></div>
										</div>
									</fieldset>
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__('URL')?></div>
											<?=
												$this->UserForm->input('url', array(
													'placeholder' => 'http://',
													'class' => 'form-control',
												))
												?>
										</div>
									</fieldset>
									<fieldset class="form-group">
										<div class="input-group">
											<span class="input-group-addon">
												<?=$this->UserForm->input('hide_referer')?>
											</span>
											<input type="text" class="form-control" data-toggle="tooltip" data-placement="top" readonly="" value="<?=__('White label my traffic')?>" title="<?=__('Hide source of the traffic.')?>">
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
