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
							<h5><?=__('Buy Referrals')?></h5>
						</div>
						<div class="col-sm-3 col-md-offset-3 moneypanel">
							<h6><?=__('Purchase balance')?></h6>
							<h3><?=h($this->Currency->format($user['User']['purchase_balance']))?></h3>
						</div>
						<div class="col-sm-1 moneypanel moneypanelicon">
							<?=$this->Html->link('<i class="fa fa-chevron-up"></i>', array('action' => 'deposit'), array(
								'title' => __('Add funds'),
								'data-toggle' => 'tooltip',
								'data-placement' => 'left',
								'escape' => false,
								))?>
						</div>
						<div class="col-sm-3 moneypanel">
							<h6><?=__('Direct referrals')?></h6>
							<h3><?=__('%s / %s', $user['User']['refs_count'], $user['ActiveMembership']['Membership']['direct_referrals_limit'] == -1 ? __('unlimited') : $user['ActiveMembership']['Membership']['direct_referrals_limit'])?></h3>
						</div>
						<div class="col-sm-1 moneypanel moneypanelicon">
							<a title="<?=__('Referrals count / your referral limit')?>" data-toggle="tooltip" data-placement="top"><i class="fa fa-info"></i></a>
						</div>
						<div class="col-sm-12 text-xs-center margin30-top">
							<?php if(!empty($showPacks)): ?>
							<?=__('Please choose your referral pack:')?>
							<br /><br />
							<?=$this->UserForm->create(false)?>
							<?php foreach($packs as $refsno => $pack): ?>
							<div style="display: inline-block" data-toggle="tooltip" data-title="<?=$pack['disabled'] == false ? __('Price: %s', $this->Currency->format($pack['tooltip'])) : $pack['tooltip']?>">
								<?=
									$this->UserForm->submit($refsno, array(
										'div' => false,
										'disabled' => $pack['disabled'],
										'name' => 'refs_no',
										'class' => 'btn btn-primary',
									))
									?>
							</div>
							<?php endforeach; ?>
							<?=$this->UserForm->end()?>
							<?php else: ?>
							<?=__('Sorry, but we don\'t have any direct referrals for sale at the moment.');?>
							<?php endif; ?>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>