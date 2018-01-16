<div class="container">
	<div class="row">
		<div class="col-md-12 front_text">
			<h2 class="text-xs-center"><?=__('Finish your registration')?></h2>
			<p class="text-xs-justify">
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
