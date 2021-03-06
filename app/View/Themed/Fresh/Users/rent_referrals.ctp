<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Rent Referrals')?></h2>
			<div class="uk-child-width-1-2@m uk-grid-small uk-grid-match" uk-grid>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('Purchase balance')?></h6>
						<div class="uk-child-width-1-2@m uk-grid-collapse" uk-grid>
							<div>
								<h3><?=h($this->Currency->format($user['User']['purchase_balance']))?></h3>
							</div>
							<div class="uk-text-right">
								<?=$this->Html->link('<i class="mdi mdi-18px mdi-chevron-up"></i>', array('controller' => 'users', 'action' => 'deposit'), array(
									'title' => __('Add funds'),
									'uk-tooltip' => '',
									'escape' => false,
									))?>
							</div>
						</div>
					</div>
				</div>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('Rented referrals')?></h6>
						<div class="uk-child-width-1-2@m uk-grid-collapse" uk-grid>
							<div>
								<h3><?=__('%s / %s', $user['User']['rented_refs_count'], $user['ActiveMembership']['Membership']['rented_referrals_limit'] == -1 ? __('unlimited') : $user['ActiveMembership']['Membership']['rented_referrals_limit'])?></h3>
							</div>
							<div class="uk-text-right">
								<a title="<?=__('Already rented referrals / your referral limit')?>" uk-tooltip><i class="mdi mdi18-px mdi-information"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="uk-text-center uk-margin-top">
				<?php if(!empty($refsPacks)): ?>
				<p><?=__('Please choose your referral pack:')?></p>
				<?=$this->UserForm->create(false)?>
				<?php foreach($refsPacks as $refsno => $pack): ?>
				<div class="uk-display-inline" uk-tooltip title="<?=$pack['disabled'] == false ? __('Price: %s', $this->Currency->format($pack['tooltip'])) : $pack['tooltip']?>">
					<?=
						$this->UserForm->submit($refsno, array(
						'div' => false,
						'disabled' => $pack['disabled'],
						'name' => 'refs_no',
						'class' => 'uk-button uk-button-primary',
						))
						?>
				</div>
				<?php endforeach; ?>
				<?=$this->UserForm->end()?>
				<?php else: ?>
				<?=__('Sorry, but we don\'t have any referrals to rent at the moment.');?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
