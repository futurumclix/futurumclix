<div class="col-md-12">
	<div class="title">
		<h2><?=__d('forum', 'Edit moderator')?></h2>
	</div>
	<?=$this->AdminForm->create('Moderator')?>
		<?=__d('forum', 'Username')?><?=$this->AdminForm->input('User.username')?>
		<?=__d('forum', 'Forum')?><?=$this->AdminForm->input('forum_id')?>
		<div class="text-center col-sm-12 paddingten">
			<button class="btn btn-primary"><?=__d('forum', 'Save')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
