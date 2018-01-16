<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('Request For A New Password')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<?=$this->Notice->show()?>
			<h2 class="uk-text-center"><?=__('Request For A New Password')?></h2>
			<?=$this->Session->flash('user_action')?>
			<?=$this->UserForm->create('User', array ('class' => 'uk-text-center'))?>
			<div class="uk-margin">
				<?=$this->UserForm->input('email', ['class' => 'uk-input uk-form-width-large', 'placeholder' => __('Enter Your E-mail Address')])?>
			</div>
			<div class="uk-margin">
				<button class="uk-button uk-button-primary">Resend Password</button>
			</div>
			<?=$this->UserForm->end(__(''))?>
		</div>
	</div>
</div>
