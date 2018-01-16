<div class="uk-child-width-1-3@m uk-grid-small uk-grid-match" uk-grid>
	<div>
		<div class="uk-card uk-card-body adspanelbox">
			<h6><?=__('Purchase balance')?></h6>
			<div class="uk-child-width-1-2@m uk-grid-collapse" uk-grid>
				<div>
					<h3><?=h($this->Currency->format($user['User']['purchase_balance']))?></h3>
				</div>
				<div class="uk-text-right">
					<?=$this->Html->link('<i class="mdi mdi-18px mdi-chevron-up"></i>', array('plugin' => '', 'controller' => 'users', 'action' => 'deposit'), array(
						'title' => __('Add funds'),
						'uk-tooltip' => '',
						'escape' => false,
						))?>
				</div>
			</div>
		</div>
	</div>
	<div>
		<div class="uk-card uk-card-body adspanelbox">
			<h6><?=__('Active Advertisements')?></h6>
			<div class="uk-child-width-1-2@m uk-grid-collapse" uk-grid>
				<div>
					<h3><?=$ads_no?></h3>
				</div>
				<div class="uk-text-right">
					<?=$this->Html->link('<i class="mdi mdi-18px mdi-plus"></i>', $add, array('escape' => false, 'title' => __('Add new advertisement'), 'uk-tooltip' => ''))?>
				</div>
			</div>
		</div>
	</div>
	<div>
		<div class="uk-card uk-card-body adspanelbox">
			<h6><?=__('My Click Packages')?></h6>
			<div class="uk-child-width-1-2@m uk-grid-collapse" uk-grid>
				<div>
					<h6><?php $txt = ''; foreach($packsSum as $k => $v): ?>
						<?php $txt .= __(' \ %d %s', $v, __(lcfirst($k)));?>
						<?php endforeach; ?>
						<?=substr($txt, 2)?>
					</h6>
				</div>
				<div class="uk-text-right">
					<?=$this->Html->link('<i class="mdi mdi-18px mdi-plus"></i>',  $buy, array('escape' => false, 'uk-tooltip' => '', 'title' => __('Buy more advertisement exposures')))?>
					<?=$this->Html->link('<i class="mdi mdi-18px mdi-export"></i>', $assign, array('escape' => false, 'uk-tooltip' => '', 'title' => __('Attach your current exposures')))?>
				</div>
			</div>
		</div>
	</div>
</div>
