<ol class="breadcrumb">
	<?php if($this->params['controller'] == 'users' && $this->params['action'] == 'dashboard'): ?>
	<li class="active"><?=__('Dashboard')?></li>
	<?php else: ?>
	<li><?=$this->Html->link(__('Dashboard'), array('plugin' => null, 'controller' => 'users', 'action' => 'dashboard'))?></li>
	<li class="active"><?=$breadcrumbTitle?></li>
	<?php endif; ?>
</ol>
