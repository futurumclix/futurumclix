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
						<div class="padding30-col row">
							<div class="col-md-12 margin30-top">
								<?php if(empty($packages)): ?>
								<h5><?=__('Sorry, you do not have any Paid Offers packages for this offer. You will be automatically redirected to purchase offer exposures after 5 seconds.')?></h5>
								<?php $this->Js->buffer("
									window.setTimeout(function() {
										window.location.href = '".Router::url(array('controller' => 'paid_offers', 'action' => 'buy'))."';
									}, 5000);
									")?>
								<?php else: ?>
								<h5><?=__('Assign paid offers click packages')?></h5>
								<?=$this->UserForm->create(false)?>
								<div class="col-md-8 col-md-offset-2 margin30-top">
									<fieldset class="form-group">
										<div class="input-group">
											<div class="input-group-addon"><?=__('Choose Package')?></div>
											<?=$this->UserForm->input('package_id', array('class' => 'form-control'))?>
										</div>
									</fieldset>
									<div class="row">
										<div class="col-sm-12 text-xs-right">
											<button class="btn btn-primary"><?=__('Assign')?></button>
										</div>
									</div>
									<div class="clearfix"></div>
								</div>
								<?=$this->UserForm->end()?>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
