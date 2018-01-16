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
							<h5><?=__d('ad_grid', 'AdGrid Ads Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('action' => 'add'),
								'buy' => array('action' => 'buy'),
								'assign' => array('action' => 'assign'),
							))
							?>
						<div class="row padding30-col">
							<div class="col-md-12">
								<h5><?=__d('ad_grid', 'Edit advertisement')?></h5>
								<div class="col-md-8 col-md-offset-2 margin30-top">
									<?=$this->UserForm->create('AdGridAd')?>
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__d('ad_grid', 'URL')?></div>
											<?=
												$this->UserForm->input('url', array(
													'class' => 'form-control',
													'placeholder' => 'http://',
												))
												?>
									</fieldset>
									<div class="row">
									<div class="col-sm-12 text-xs-right">
									<button class="btn btn-primary"><?=__d('ad_grid', 'Save')?></button>
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
</div>
