<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Paid Offers Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'paid_offers', 'action' => 'add'),
				'buy' => array('controller' => 'paid_offers', 'action' => 'buy'),
				'assign' => array('controller' => 'paid_offers', 'action' => 'assign'),
				))
				?>
			<h2 class="uk-margin-top"><?=__('Applications for Paid Offer "%s"', $offer['PaidOffer']['title'])?></h2>
			<table class="uk-table-small uk-table">
				<thead>
					<tr>
						<th><?=__('Applicant')?></th>
						<th><?=__('Status')?></th>
						<th><?=__('Application date')?></th>
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
							<button class="uk-button-small uk-button-secondary" uk-toggle="target: #ajax-modal" uk-tooltip title="<?=__('Reject application')?>" data-ajaxsource="<?=$this->Html->url(array('action' => 'applicationReject', $app['id']))?>"><?=__('Reject')?></button>
							<?=
								$this->UserForm->postLink('<button class="uk-button-primary uk-button-small">'.__('Approve').'&nbsp;<i class="fa fa-thumbs-up"></i></button>',
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
