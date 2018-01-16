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
							<h5><?=__('Paid Offers Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('controller' => 'paid_offers', 'action' => 'add'),
								'buy' => array('controller' => 'paid_offers', 'action' => 'buy'),
								'assign' => array('controller' => 'paid_offers', 'action' => 'assign'),
							))
							?>
						<div class="row padding30-col">
							<div class="col-md-12">
								<h5><?=__('Edit advertisement')?></h5>
								<div class="col-md-8 col-md-offset-2 margin30-top">
									<?=$this->UserForm->create('PaidOffer')?>
									<?=$this->UserForm->input('PaidOffer.id')?>
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__('Title')?></div>
											<?=
												$this->UserForm->input('title', array(
													'palaceholder' => __('Enter your offertitle'),
													'class' => 'form-control',
													'data-limit' => $titleMax,
													'data-counter' => 'titleCounter'
												))
												?>
											<div id="titleCounter" class="input-group-addon"><?=isset($this->request->data['PaidOffer']['title']) ? strlen($this->request->data['PaidOffer']['title']) : 0?> / <?=$titleMax?></div>
										</div>
									</fieldset>
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__('Description')?></div>
											<?=
												$this->UserForm->input('description', array(
													'type' => 'textarea',
													'class' => 'form-control',
													'placeholder' => __('Please describe in details what applicant have to do to get this offer approved and what info has to be provided after submitting this offer.'),
													'style' => 'resize:none;',
													'data-limit' => $descMax,
													'data-counter' => 'descCounter',
												))
												?>
											<div id="descCounter" class="input-group-addon"><?=isset($this->request->data['PaidOffer']['description']) ? strlen($this->request->data['PaidOffer']['description']) : 0?> / <?=$descMax?></div>
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
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__('Category')?></div>
											<?=$this->UserForm->input('category_id', array('class' => 'form-control'))?>
										</div>
									</fieldset>
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__('Targeted Memberships')?></div>
											<?=
												$this->UserForm->input('TargettedMemberships', array(
													'type' => 'select',
													'class' => 'fancy form-control',
													'multiple' => 'multiple',
													'options' => $memberships,
												))
												?>
											<div class="input-group-addon">
												<input class="btn btn-default" type="button" value="<?=__('Select All')?>" style="vertical-align: top;" onclick="selectAll('TargettedMembershipsTargettedMemberships')">
											</div>
										</div>
									</fieldset>
									<?php if(Module::active('AccurateLocationDatabase')): ?>
										<?=$this->Locations->selector($countries)?>
									<?php else: ?>
										<fieldset class="form-group">
											<div class="input-group">
												<div class="input-group-addon"><?=__('Targeted Locations')?></div>
												<?=
													$this->UserForm->input('TargettedLocations', array(
														'type' => 'select',
														'class' => 'fancy form-control',
														'multiple' => 'multiple',
														'options' => $countries,
														'selected' => $selectedCountries,
													))
													?>
												<div class="input-group-addon">
													<input class="btn btn-default" type="button" value="<?=__('Select All')?>" style="vertical-align: top;" onclick="selectAll('PaidOfferTargettedLocations')">
												</div>
											</div>
										</fieldset>
									<?php endif; ?>
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
