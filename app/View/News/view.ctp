<div class="container">
	<div class="row">
		<div class="col-md-12 front_text newspage">
			<h2 class="text-xs-center"><?=__('%s news', Configure::read('siteName'))?></h2>
				<div class="newsitem col-md-12">
					<h3><?=h($news['News']['title'])?></h3>
					<p class="postdate"><?=__('Posted at %s', $this->Time->nice($news['News']['created']))?></h6>
					<?php if($news['News']['created'] != $news['News']['modified']): ?>
						<p class="postdate"><?=__('Last modification at %s', $this->Time->nice($news['News']['modified']))?></h6>
					<?php endif; ?>
					<div class="margin30-top">
						<?=$news['News']['content']?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
