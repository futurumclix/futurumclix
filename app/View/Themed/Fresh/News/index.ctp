<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('%s news', Configure::read('siteName'))?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1 news">
			<?php foreach($allNews as $news): ?>
			<div class="uk-text-lead"><?=h($news['News']['title'])?></div>
			<div class="uk-text-meta"><?=__('Posted at %s', $this->Time->nice($news['News']['created']))?></div>
			<?php if($news['News']['created'] != $news['News']['modified']): ?>
			<div class="uk-text-meta"><?=__('Last modification at %s', $this->Time->nice($news['News']['modified']))?></div>
			<?php endif; ?>
			<div class="uk-margin-top">
				<?=$news['News']['content']?>
			</div>
			<hr>
			<?php endforeach; ?>
			<div class="uk-text-right">
				<?=$this->Paginator->counter(array('format' => __('Page {:page} of {:pages}')))?>
			</div>
			<ul class="uk-pagination uk-flex-center">
				<?php
					echo $this->Paginator->first('&laquo;', array('tag' => 'li', 'escape' => false));
					echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'currentClass' => 'uk-active', 'currentTag' => 'a'));
					echo $this->Paginator->last('&raquo;', array('tag' => 'li', 'escape' => false));
					?>
			</ul>
		</div>
	</div>
</div>
