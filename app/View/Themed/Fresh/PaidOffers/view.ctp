<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Paid Offers')?></h2>
			<ul class="uk-card uk-card-body" uk-accordion>
				<li>
					<h6 class="uk-accordion-title">
					<?=__('Paid Offers Terms Of Service')?></h3>
					<div class="uk-accordion-content">
						Put your ToS for Paid Offers in here.
					</div>
				</li>
			</ul>
			<ul class="uk-margin-top" uk-tab uk-switcher="animation: uk-animation-fade">
				<li><a href="#"><?=__('Active Offers')?>&nbsp;<span class="uk-badge"><?=count($offers)?></span></a></li>
				<li><a href="#"><?=__('Ignored Offers')?>&nbsp;<span class="uk-badge"><?=count($ignoredOffers)?></span></a></li>
				<li><a href="#"><?=__('Completed')?>&nbsp;<span class="uk-badge"><?=$user['User']['accepted_applications']?></span></a></li>
				<li><a href="#"><?=__('Pending')?>&nbsp;<span class="uk-badge"><?=$user['User']['pending_applications']?></span></a></li>
				<li><a href="#"><?=__('Rejected')?>&nbsp;<span class="uk-badge"><?=$user['User']['rejected_applications']?></span></a></li>
			</ul>
			<ul class="uk-switcher uk-margin">
			<li>
				<?php foreach($offers as $offer): ?>
				<table class="uk-table uk-table-small">
					<thead>
						<tr>
							<th colspan="4" class="uk-text-center"><?=$this->Html->link($offer['PaidOffer']['title'], $offer['PaidOffer']['url'])?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="4"><?=h($offer['PaidOffer']['description'])?></td>
						</tr>
					</tbody>
					<thead>
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
								<button class="uk-button-primary uk-button-small" uk-toggle="target: #ajax-modal" data-ajaxsource="<?=$this->Html->url(array('action' => 'applicationAdd', $offer['PaidOffer']['id']))?>"><?=__('Submit')?></button>
								<?=
									$this->UserForm->postLink('<button class="uk-button-secondary uk-button-small">'.__('Ignore').'</i></button>',
									array('action' => 'ignore', $offer['PaidOffer']['id']),
									array('escape' => false),
									__('Are you sure you want to add "%s" to ignore list?', $offer['PaidOffer']['title'])
									)
									?>
								<button class="uk-button-secondary uk-button-small" uk-toggle="target: #ajax-modal" data-ajaxsource="<?=$this->Html->url(array('action' => 'report', $offer['PaidOffer']['id']))?>"><?=__('Report')?></button>
							</td>
						</tr>
					</tbody>
				</table>
				<hr>
				<?php endforeach; ?>
			</li>
			<li>
				<?php foreach($ignoredOffers as $offer): ?>
				<table class="uk-table uk-table-small">
					<thead>
						<tr>
							<th colspan="4" class="uk-text-center"><?=$this->Html->link($offer['PaidOffer']['title'], $offer['PaidOffer']['url'])?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="4"><?=h($offer['PaidOffer']['description'])?></td>
						</tr>
					</tbody>
					<thead>
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
								<button class="uk-button-primary uk-button-small" uk-toggle="target: #ajax-modal" data-ajaxsource="<?=$this->Html->url(array('action' => 'applicationAdd', $offer['PaidOffer']['id']))?>"><?=__('Submit')?></button>
								<?=
									$this->UserForm->postLink('<button class="uk-button-secondary uk-button-small">'.__('Unignore').'</button>',
									array('action' => 'unignore', $offer['PaidOffer']['id']),
									array('escape' => false),
									__('Are you sure you want to remove "%s" from ignore list?', $offer['PaidOffer']['title'])
									)
									?>
								<button class="uk-button-secondary uk-button-small" uk-toggle="target: #ajax-modal" data-ajaxsource="<?=$this->Html->url(array('action' => 'report', $offer['PaidOffer']['id']))?>"><?=__('Report')?></button>
							</td>
						</tr>
					</tbody>
				</table>
				<hr>
				<?php endforeach; ?>
			</li>
			<li>
				<?php foreach($user['AcceptedApplication'] as $app): ?>
				<table class="uk-table uk-table-small">
					<thead>
						<tr>
							<th colspan="4" class="uk-text-center"><?=h($app['PaidOffer']['title'])?></th>
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
			</li>
			<li>
				<?php foreach($user['PendingApplication'] as $app): ?>
				<table class="uk-table uk-table-small">
					<thead>
						<tr>
							<th colspan="4" class="uk-text-center"><?=$this->Html->link($offer['PaidOffer']['title'], $offer['PaidOffer']['url'])?></th>
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
			</li>
			<li>
				<?php foreach($user['RejectedApplication'] as $app): ?>
				<table class="uk-table uk-table-small">
					<thead>
						<tr>
							<th colspan="4" class="uk-text-center"><?=h($app['PaidOffer']['title'])?></th>
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
	</li>
</div>
<?php
	$this->Js->buffer("
	$('#ajax-modal').on('hide', function() {
	window.location.reload();
	});
	");
	?>
