<?php foreach($allNews as $news): ?>
	<div class="col-sm-4 newsitem">
		<h3><?=h($news['News']['title'])?></h3>
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
		<span class="date"><?=h($news['News']['modified'])?></span>
		<?php if($maxLength !== 'full'): ?>
			<?=$this->Html->link(__('Read more...'), array('plugin' => null, 'controller' => 'news', 'action' => 'view', $news['News']['id']), array('class' => 'readmore'))?>
		<?php endif; ?>
	</div>
<?php endforeach; ?>
