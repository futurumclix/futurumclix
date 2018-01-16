<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<?php if($this->params['controller'] == 'users' && $this->params['action'] == 'dashboard'): ?>
					<li class="uk-active"><?=__('Dashboard')?></li>
					<?php else: ?>
					<li><?=$this->Html->link(__('Dashboard'), array('plugin' => null, 'controller' => 'users', 'action' => 'dashboard'))?></li>
					<li class="uk-active"><?=$breadcrumbTitle?></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
</div>
