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
								'add' => array('controller' => 'paid_offers', 'action' => 'add'),
								'buy' => array('controller' => 'paid_offers', 'action' => 'buy'),
								'assign' => array('controller' => 'paid_offers', 'action' => 'assign'),
							))
							?>
						<div class="padding30-col">
							<h5><?=__('Applications for Paid Offer "%s"', $offer['PaidOffer']['title'])?></h5>
							<table class="table">
								<thead class="thead-default">
									<tr>
										<th><?=__('Applicant')?></th>
										<th><?=__('Status')?></th>
										<th><?=__('Appliction date')?></th>
										<th><?=__('Confirmation')?></th>
										<th><?=__('Reject reason')?></th>
										<th><?=__('Actions')?></th>
									</tr>
								</thead>
								<tbody>
							<?php foreach($applications as $app): ?>
									<tr>
										<td>
											<?php if($app['Applicant']['username']): ?>
											<?=h($app['Applicant']['username'])?>
											<?php else: ?>
											<?=__('Deleted')?>
											<?php endif; ?>
										</td>
										<td><?=$this->Utility->enum('PaidOffersApplication', 'status', $app['status'])?></td>
										<td><?=$this->Time->nice($app['created'])?></td>
										<td><?=h($app['description'])?></td>
										<?php if($app['reject_reason']): ?>
										<td><?=h($app['reject_reason'])?></td>
										<?php else: ?>
										<td><i><?=__('None')?></i></td>
										<?php endif; ?>
										<?php if($app['status'] == PaidOffersApplication::PENDING): ?>
										<td>
											<button class="btn btn-default btn-sm" data-toggle="modal" data-target="#ajax-modal-container" data-placement="top" data-original-title="<?=__('Reject application')?>" data-ajaxsource="<?=$this->Html->url(array('action' => 'applicationReject', $app['id']))?>"><?=__('Reject')?>&nbsp;<i class="fa fa-close"></i></button>
											<?=
												$this->UserForm->postLink('<button class="btn btn-default btn-sm">'.__('Approve').'&nbsp;<i class="fa fa-thumbs-up"></i></button>',
													array('action' => 'applicationAccept', $app['id']),
													array('escape' => false),
													__('Are you sure you want to accept this application?')
												)
												?>
										</td>
										<?php endif; ?>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
