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
			<div class="uk-text-lead"><?=h($news['News']['title'])?></div>
			<div class="uk-text-meta"><?=__('Posted at %s', $this->Time->nice($news['News']['created']))?></div>
			<?php if($news['News']['created'] != $news['News']['modified']): ?>
			<div class="uk-text-meta"><?=__('Last modification at %s', $this->Time->nice($news['News']['modified']))?></div>
			<?php endif; ?>
			<div class="uk-margin-top">
				<?=$news['News']['content']?>
			</div>
		</div>
	</div>
</div>
