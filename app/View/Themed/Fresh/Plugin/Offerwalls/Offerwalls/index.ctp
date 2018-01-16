<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__d('offerwalls', 'Offerwalls')?></h2>
			<ul class="uk-margin-top" uk-tab uk-switcher="animation: uk-animation-fade">
				<?php $active = true; foreach($offers as $wall => $html): ?>
				<li> <a href="#<?=h($wall)?>"><?=h($wall)?></a></li>
				<?php $active = false; endforeach; ?>
			</ul>
			<ul class="uk-switcher uk-margin">
				<?php  $active = true; foreach($offers as $wall => $html): ?>
				<li id="<?=h($wall)?>">
					<?=$html?>
				</li>
				<?php $active = false; endforeach; ?>
			</ul>
		</div>
	</div>
</div>
