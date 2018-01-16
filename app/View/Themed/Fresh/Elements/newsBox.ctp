<?php foreach($allNews as $news): ?>
<span class="date"><?=h($news['News']['modified'])?></span>
<h4><?=h($news['News']['title'])?></h4>
<p>
	<?php if($maxLength === 'full'): ?>
	<?=($news['News']['content'])?>
	<?php else: ?>
	<?=
		$this->Text->truncate($news['News']['content'], $maxLength, array(
			'exact' => false,
			'html' => true,
			'ellipsis' => '&hellip;',
		))
		?>
	<?php endif; ?>
</p>
<?php if($maxLength !== 'full'): ?>
<?=$this->Html->link(__('Read more...'), array('plugin' => null, 'controller' => 'news', 'action' => 'view', $news['News']['id']), array('class' => 'readmore'))?>
<?php endif; ?>
<?php endforeach; ?>
