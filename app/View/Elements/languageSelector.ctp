<button class="languagebutton <?=Configure::read('Config.language')?>" id="<?=$id?>" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
<div class="dropdown-menu dropdown-menu-right languageselector">
	<?php foreach($this->UserForm->getAvailableTranslations() as $k => $v): ?>
	<?=
		$this->UserForm->postLink('',
			array('plugin' => null, 'controller' => 'settings', 'action' => 'locale', $k),
			array('class' => $k)
		)
	?>
	<?php endforeach; ?>
</div>
