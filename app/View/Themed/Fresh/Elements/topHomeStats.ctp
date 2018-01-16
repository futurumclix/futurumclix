<div class="topstats uk-visible@l">
	<div class="users" uk-tooltip="pos: bottom" title="<?=__('Registered users')?>"><i class="mdi mdi-account mdi-18px"></i><?=$stats['users']?></div>
	<div class="users" uk-tooltip="pos: bottom" title="<?=__('Users yesterday')?>"><i class="mdi mdi-account-multiple mdi-18px"></i><?=$stats['yesterday_users']?></div>
	<?php if(isset($stats['users_online'])): ?>
	<div class="users" uk-tooltip=" pos:bottom" title="<?=__('Users online')?>"><i class="mdi mdi-counter mdi-18px"></i><?=$stats['users_online']?></div>
	<?php endif ;?>
	<div class="users" uk-tooltip="pos: bottom" title="<?=__('Paid so far')?>"><i class="mdi mdi-cash-usd mdi-18px"></i><?=$this->Currency->format($stats['total_cashouts'])?></div>
	<div class="users" uk-tooltip="pos: bottom" title="<?=__('Ads watched')?>"><i class="mdi mdi-eye mdi-18px"></i><?=$stats['clicks']?></div>
</div>
