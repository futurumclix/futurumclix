<?=$this->element('userSidebar')?>
<div class="page">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 padding30-sides">
				<?=$this->element('userBreadcrumbs')?>
				<?=$this->Notice->show()?>
				<div class="panel">
					<div class="padding30-col">
						<div class="col-md-12 paidoffers">
							<h5><?=__('Paid Offers')?></h5>
							<div id="accordion" role="tablist" class="margin30-top">
								<div class="panel panel-default">
									<div class="panel-heading" role="tab" id="offerstos">
										<h6 class="panel-title">
										<a role="button" data-toggle="collapse" data-parent="#accordion" href="#paidofferstos">
										<?=__('Paid Offers Terms Of Service')?> <i class="fa fa-plus-square"></i>
										</a>
										</h6>
									</div>
									<div id="paidofferstos" class="panel-collapse collapse" role="tabpanel">
										<div class="panel-body">
											Put your ToS for Paid Offers in here.
										</div>
									</div>
								</div>
							</div>
							<div class="margin30-top">
								<ul class="nav nav-tabs">
									<li class="nav-item"><a class="nav-link active" href="#home" aria-controls="home" role="tab" data-toggle="tab"><?=__('Active Offers')?>&nbsp;<span class="label label-primary"><?=count($offers)?></span></a></li>
									<li class="nav-item"><a class="nav-link" href="#ignored" aria-controls="ignored" role="tab" data-toggle="tab"><?=__('Ignored Offers')?>&nbsp;<span class="label label-primary"><?=count($ignoredOffers)?></span></a></li>
									<li class="nav-item"><a class="nav-link" href="#completed" aria-controls="profile" role="tab" data-toggle="tab"><?=__('Completed')?>&nbsp;<span class="label label-primary"><?=$user['User']['accepted_applications']?></span></a></li>
									<li class="nav-item"><a class="nav-link" href="#pending" aria-controls="messages" role="tab" data-toggle="tab"><?=__('Pending')?>&nbsp;<span class="label label-primary"><?=$user['User']['pending_applications']?></span></a></li>
									<li class="nav-item"><a class="nav-link" href="#rejected" aria-controls="settings" role="tab" data-toggle="tab"><?=__('Rejected')?>&nbsp;<span class="label label-primary"><?=$user['User']['rejected_applications']?></span></a></li>
								</ul>
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active" id="home">
										<div class="row">
											<div class="col-md-12">
												<?php foreach($offers as $offer): ?>
												<table class="table table-condensed">
													<thead class="thead-default">
														<tr>
															<th colspan="4" class="text-xs-center"><?=$this->Html->link($offer['PaidOffer']['title'], $offer['PaidOffer']['url'])?></th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td colspan="4"><?=h($offer['PaidOffer']['description'])?></td>
														</tr>
													</tbody>
													<thead class="thead-default">
														<tr>
															<th><?=__('Date Added')?></th>
															<th><?=__('Category')?></th>
															<th><?=__('Offer\'s Value')?></th>
															<th><?=__('Actions')?></th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td><?=$this->Time->nice($offer['PaidOffer']['created'])?></td>
															<td><?=h($offer['Category']['name'])?></td>
															<td><?=$this->Currency->format($offer['PaidOffer']['value'])?></td>
															<td>
																<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ajax-modal-container" data-ajaxsource="<?=$this->Html->url(array('action' => 'applicationAdd', $offer['PaidOffer']['id']))?>"><?=__('Submit')?>&nbsp;<i class="fa fa-thumbs-up"></i></button>
															<?=
																$this->UserForm->postLink('<button class="btn btn-default btn-sm">'.__('Ignore').'&nbsp;<i class="fa fa-thumbs-down"></i></button>',
																	array('action' => 'ignore', $offer['PaidOffer']['id']),
																	array('escape' => false),
																	__('Are you sure you want to add "%s" to ignore list?', $offer['PaidOffer']['title'])
																)
																?>
															<button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#ajax-modal-container" data-ajaxsource="<?=$this->Html->url(array('action' => 'report', $offer['PaidOffer']['id']))?>"><?=__('Report')?>&nbsp;<i class="fa fa-flag"></i></button>
															</td>
														</tr>
													</tbody>
												</table>
												<hr>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="ignored">
										<div class="row">
											<div class="col-md-12">
												<?php foreach($ignoredOffers as $offer): ?>
												<table class="table table-condensed">
													<thead class="thead-default">
														<tr>
															<th colspan="4" class="text-xs-center"><?=$this->Html->link($offer['PaidOffer']['title'], $offer['PaidOffer']['url'])?></th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td colspan="4"><?=h($offer['PaidOffer']['description'])?></td>
														</tr>
													</tbody>
													<thead class="thead-default">
														<tr>
															<th><?=__('Date Added')?></th>
															<th><?=__('Category')?></th>
															<th><?=__('Offer\'s Value')?></th>
															<th><?=__('Actions')?></th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td><?=$this->Time->nice($offer['PaidOffer']['created'])?></td>
															<td><?=h($offer['Category']['name'])?></td>
															<td><?=$this->Currency->format($offer['PaidOffer']['value'])?></td>
															<td>
																<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ajax-modal-container" data-ajaxsource="<?=$this->Html->url(array('action' => 'applicationAdd', $offer['PaidOffer']['id']))?>"><?=__('Submit')?>&nbsp;<i class="fa fa-thumbs-up"></i></button>
															<?=
																$this->UserForm->postLink('<button class="btn btn-danger btn-sm">'.__('Unignore').'&nbsp;<i class="fa fa-thumbs-up"></i></button>',
																	array('action' => 'unignore', $offer['PaidOffer']['id']),
																	array('escape' => false),
																	__('Are you sure you want to remove "%s" from ignore list?', $offer['PaidOffer']['title'])
																)
																?>
																<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#ajax-modal-container" data-ajaxsource="<?=$this->Html->url(array('action' => 'report', $offer['PaidOffer']['id']))?>"><?=__('Report')?>&nbsp;<i class="fa fa-flag"></i></button>
															</td>
														</tr>
													</tbody>
												</table>
												<hr>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="completed">
										<div class="row">
											<div class="col-md-12">
												<?php foreach($user['AcceptedApplication'] as $app): ?>
													<table class="table table-condensed">
														<thead class="thead-default">
															<tr>
																<th colspan="4" class="text-xs-center"><?=h($app['PaidOffer']['title'])?></th>
															</tr>
															<tr>
																<th><?=__('Completed Date')?></th>
																<th><?=__('Approval Date')?></th>
																<th><?=__('Category')?></th>
																<th><?=__('Offer\'s Value')?></th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td><?=$this->Time->nice($app['created'])?></td>
																<td><?=$this->Time->nice($app['modified'])?></td>
																<td><?=h($app['PaidOffer']['Category']['name'])?></td>
																<td><?=$this->Currency->format($app['PaidOffer']['value'])?></td>
															</tr>
														</tbody>
													</table>
													<hr>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="pending">
										<div class="row">
											<div class="col-md-12">
												<?php foreach($user['PendingApplication'] as $app): ?>
													<table class="table table-condensed">
														<thead class="thead-default">
															<tr>
																<th colspan="4" class="text-xs-center"><?=$this->Html->link($offer['PaidOffer']['title'], $offer['PaidOffer']['url'])?></th>
															</tr>
															<tr>
																<th><?=__('Pending Since')?></th>
																<th><?=__('Category')?></th>
																<th><?=__('Offer\'s Value')?></th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td><?=$this->Time->nice($app['created'])?></td>
																<td><?=h($app['PaidOffer']['Category']['name'])?></td>
																<td><?=$this->Currency->format($app['PaidOffer']['value'])?></td>
															</tr>
														</tbody>
													</table>
													<hr>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane" id="rejected">
										<div class="row">
											<div class="col-md-12">
												<?php foreach($user['RejectedApplication'] as $app): ?>
													<table class="table table-condensed">
														<thead class="thead-default">
															<tr>
																<th colspan="4" class="text-xs-center"><?=h($app['PaidOffer']['title'])?></th>
															</tr>
															<tr>
																<th><?=__('Rejection Date')?></th>
																<th><?=__('Category')?></th>
																<th><?=__('Offer\'s Value')?></th>
																<th><?=__('Reject Reason')?></th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td><?=$this->Time->nice($app['created'])?></td>
																<td><?=h($app['PaidOffer']['Category']['name'])?></td>
																<td><?=$this->Currency->format($app['PaidOffer']['value'])?></td>
																<td><?=h($app['reject_reason'])?></td>
															</tr>
														</tbody>
													</table>
													<hr>
												<?php endforeach; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
			<?php
				$this->Js->buffer("
					$('#ajax-modal-container').on('hidden.bs.modal', function() {
						window.location.reload();
					});
				");
				?>
