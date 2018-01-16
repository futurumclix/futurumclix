<div class="col-sm-2 moneypanel">
	<h6><?=__('Purchase balance')?></h6>
	<h3><?=h($this->Currency->format($user['User']['purchase_balance']))?></h3>
</div>
<div class="col-sm-1 moneypanel moneypanelicon">
	<?=$this->Html->link('<i class="fa fa-chevron-up"></i>', array('plugin' => '', 'controller' => 'users', 'action' => 'deposit'), array(
		'title' => __('Add funds'),
		'data-toggle' => 'tooltip',
		'data-placement' => 'left',
		'escape' => false,
	))?>
</div>
<div class="col-sm-3 moneypanel">
	<h6><?=__('Active Advertisements')?></h6>
	<h3><?=$ads_no?></h3>
</div>
<div class="col-sm-1 moneypanel moneypanelicon">
	<?=$this->Html->link('<i class="fa fa-plus"></i>', $add, array('escape' => false, 'title' => __('Add new advertisement'), 'data-toggle' => 'tooltip', 'data-placement' => 'top'))?>
</div>
<div class="col-sm-4 moneypanel packets">
	<h6><?=__('My Click Packages')?></h6>
	<h3><?php $txt = ''; foreach($packsSum as $k => $v): ?>
		<?php $txt .= __(' \ %d %s', $v, __(lcfirst($k)));?>
		<?php endforeach; ?>
		<?=substr($txt, 2)?>
	</h3>
</div>
<div class="col-sm-1 moneypanel moneypanelicon">
	<?=$this->Html->link('<i class="fa fa-plus"></i>', $buy, array('escape' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => __('Buy more advertisement exposures')))?>
	<?=$this->Html->link('<i class="fa fa-exchange"></i>', $assign, array('escape' => false, 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => __('Attach your current exposures')))?>
</div>
<div class="clearfix"></div>
