<div class="topstats uk-visible@l">
	<div class="users" uk-tooltip="pos: bottom" title="<?=__('Account Balance')?>"><i class="mdi mdi-cash mdi-18px"></i><?=h($this->Currency->format($user['User']['account_balance']))?></div>
	<div class="users" uk-tooltip="pos: bottom" title="<?=__('Purchase Balance')?>"><i class="mdi mdi-cash-multiple mdi-18px"></i><?=h($this->Currency->format($user['User']['purchase_balance']))?></div>
	<div class="users" uk-tooltip="pos: bottom" title="<?=__('Direct Referrals')?>"><i class="mdi mdi-account mdi-18px"></i><?=h($user['User']['refs_count'])?></div>
	<div class="users" uk-tooltip="pos: bottom" title="<?=__('Rented Referrals')?>"><i class="mdi mdi-account-multiple mdi-18px"></i><?=h($user['User']['rented_refs_count'])?></div>
</div>
