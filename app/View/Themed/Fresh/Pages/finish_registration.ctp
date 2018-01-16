<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('Finish your registration')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1 uk-text-center">
			<h2><?=__('Finish your registration')?></h2>
			<p>
				<strong><?=__('Your registration is almost finished!')?></strong>
				<br/><br/>
				<?php
					echo __('We have just sent you an activation email to the address you have provided while signing up. '); 
					echo __('Please click on the activation link to verify your email address. If you did not get your activation email, please check your spam folder. ');
					echo __('If it is still not there after few minutes, please open support ticket. ');
					echo __('You will be automatically redirected to login page. If not please click ').$this->Html->link(__('here.'), ['controller' => 'users', 'action' => 'login']);
					?>
			</p>
		</div>
	</div>
</div>
<?php
	$redirectUrl = Router::url(['controller' => 'users', 'action' => 'login'], true);
	$this->Js->buffer("setTimeout(function(){window.location = '$redirectUrl';}, 10000);");
	?>
