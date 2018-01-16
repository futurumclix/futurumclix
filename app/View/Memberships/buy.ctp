<div class="container">
	<div class="row">
		<div class="col-md-12 padding30-sides">
			<?=$this->element('userBreadcrumbs')?>
			<?=$this->Notice->show()?>
			<div class="panel">
				<div class="padding30-col">
					<div class="col-sm-3 moneypanel">
						<h6><?=__('Purchase balance')?></h6>
						<h3><?=h($this->Currency->format($user['User']['purchase_balance']))?></h3>
					</div>
					<div class="col-sm-1 moneypanel moneypanelicon">
						<?=$this->Html->link('<i class="fa fa-chevron-up"></i>', array('controller' => 'users', 'action' => 'deposit'), array(
							'title' => __('Add funds'),
							'data-toggle' => 'tooltip',
							'data-placement' => 'left',
							'escape' => false,
							))?>
					</div>
					<div class="col-sm-4 moneypanel">
						<h6><?=__('Current membership')?></h6>
						<h3><?=h($user['ActiveMembership']['Membership']['name'])?></h3>
					</div>
					<div class="col-sm-4 moneypanel">
						<h6><?=__('Membership valid until')?></h6>
						<h3><?=$user['ActiveMembership']['ends'] == null ? __('Unlimited') : h($user['ActiveMembership']['ends'])?></h3>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="col-md-12 text-xs-center margin30-top">
					<h5>
						<?=__('You are buying %s Membership %d months for %s, pay with:', $toBuy['Membership']['name'], $duration, $this->Currency->format($toBuy['Membership']['price']))?>
					</h5>
					<div class="gatewaybuttons">
						<?=$this->UserForm->create(null);?>
						<?=$this->UserForm->getGatewaysButtons($activeGateways, array('prices' => $prices))?>
						<?=$this->UserForm->end()?>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>