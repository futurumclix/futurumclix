<h5 class="text-xs-center"><?=__d('installer', 'Welcome to the FuturumClix Installation')?></h5>
<p><?=__d('installer', 'You are going to install FuturumClix on your server now. First, we will check if you have all the files and correct permissions set on your server. If you do not, we will let you know. Otherwise you will get the button to move on to the next part of installation.')?></p>
<div class="text-xs-right">
	<?php if($anyErrors): ?>
		<?=$this->Html->link(__d('installer', 'Refresh'), array('action' => 'index'), array('class' => 'btn btn-primary'))?>
	<?php else: ?>
		<?=$this->Html->link(__d('installer', 'Install'), array('action' => 'step1'), array('class' => 'btn btn-primary'))?>
	<?php endif; ?>
</div>
