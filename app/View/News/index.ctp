<div class="container">
	<div class="row">
		<div class="col-md-12 front_text newspage">
			<h2 class="text-xs-center"><?=__('%s news', Configure::read('siteName'))?></h2>
				<?php foreach($allNews as $news): ?>
					<div class="newsitem col-md-12">
						<h3><?=h($news['News']['title'])?></h3>
						<p class="postdate"><?=__('Posted at %s', $this->Time->nice($news['News']['created']))?></p>
						<?php if($news['News']['created'] != $news['News']['modified']): ?>
							<p class="postdate"><?=__('Last modification at %s', $this->Time->nice($news['News']['modified']))?></p>
						<?php endif; ?>
						<div class="margin30-top">
							<?=$news['News']['content']?>
						</div>
						<hr>
					</div>
				<?php endforeach; ?>
				<div class="col-sm-12 text-xs-right pagecounter">
					<?=
						$this->Paginator->counter(array(
							'format' => __('Page {:page} of {:pages}')
						))
					?>
				</div>
				<div class="col-sm-12 text-xs-center">
					<nav>
						<ul class="pagination pagination-sm">
							<?php
								echo $this->Paginator->first('&laquo;', array('tag' => 'li', 'class' => 'page-item', 'escape' => false));
								echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'class' => 'page-item', 'currentClass' => 'active', 'currentTag' => 'a'));
								echo $this->Paginator->last('&raquo;', array('tag' => 'li', 'class' => 'page-item', 'escape' => false));
							?>
						</ul>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>
