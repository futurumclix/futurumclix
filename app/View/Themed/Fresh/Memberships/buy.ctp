<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('Upgrade Your Membership')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container dashpage">
	<div uk-grid>
		<div class="uk-width-1-1">
			<?=$this->Notice->show()?>
		</div>
	</div>
	<div class="uk-child-width-1-3@m uk-grid-small uk-grid-match" uk-grid>
		<div>
			<div class="uk-card uk-card-body pb">
				<h6><?=__('Purchase balance')?></h6>
				<h3><?=h($this->Currency->format($user['User']['purchase_balance']))?></h3>
				<?=$this->Html->link('<i title="Add funds" class="mdi mdi-arrow-up-drop-circle mdi-18px" uk-tooltip></i>', array('controller' => 'users', 'action' => 'deposit'), array(
					'escape' => false,
					))?>
			</div>
		</div>
		<div>
			<div class="uk-card uk-card-body">
				<h6><?=__('Current membership')?></h6>
				<h3><?=h($user['ActiveMembership']['Membership']['name'])?></h3>
			</div>
		</div>
		<div>
			<div class="uk-card uk-card-body">
				<h6><?=__('Membership valid until')?></h6>
				<h3><?=h($user['User']['refs_count'])?></h3>
			</div>
		</div>
	</div>
	<div uk-grid>
		<div class="uk-width-1-1 uk-text-center uk-margin-bottom">
			<h5>
				<?=__('You are buying %s Membership %d months for %s, pay with:', $toBuy['Membership']['name'], $duration, $this->Currency->format($toBuy['Membership']['price']))?>
			</h5>
			<div class="gatewaybuttons" uk-margin>
				<?=$this->UserForm->create(null);?>
				<?=$this->UserForm->getGatewaysButtons($activeGateways, array('prices' => $prices))?>
				<?=$this->UserForm->end()?>
			</div>
		</div>
	</div>
</div>
