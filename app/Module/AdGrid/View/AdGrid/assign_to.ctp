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
						<div class="padding30-col row">
							<div class="col-md-12 margin30-top">
								<?php if(empty($packages)): ?>
								<h5><?=__d('ad_grid', 'Sorry, you do not have any AdGrid Ads packages.')?></h5>
								<?php else: ?>
								<h5><?=__d('ad_grid', 'Assign advertisement click packages')?></h5>
								<?=$this->UserForm->create(false)?>
								<div class="col-md-8 col-md-offset-2 margin30-top">
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__d('ad_grid', 'Choose package')?></div>
											<?=$this->UserForm->input('package_id', array('class' => 'form-control'))?>
										</div>
									</fieldset>
									<div class="row">
										<div class="col-sm-12 text-xs-right">
											<button class="btn btn-primary"><?=__d('ad_grid', 'Assign')?></button>
										</div>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<?=$this->UserForm->end()?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>