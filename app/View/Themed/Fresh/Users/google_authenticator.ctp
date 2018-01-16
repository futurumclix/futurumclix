<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('Please enter Google Authenticator code')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<?=$this->Notice->show()?>
			<h2 class="uk-text-center"><?=__('Please enter Google Authenticator code')?></h2>
			<?=$this->UserForm->create(false, array ('class' => 'uk-text-center'))?>
			<div class="uk-margin">
				<?=$this->UserForm->input('ga_code', array('class' => 'uk-input uk-form-width-large', 'placeholder' => __('Enter your Google Authenticator code')))?>
			</div>
			<div class="uk-margin">
				<?=$this->UserForm->submit('Log in', array('class' => 'uk-button uk-button-primary'))?>
				<?=$this->UserForm->end()?>
			</div>
		</div>
	</div>
</div>
