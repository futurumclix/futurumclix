<div class="col-md-12">
	<div class="title">
		<h2><?=__d('forum', 'Edit Forum:')?> <?=h($this->request->data['Forum']['title'])?></h2>
	</div>
	<div>
		<?=
			$this->AdminForm->create('Forum', array(
				'class' => 'form-horizontal',
				'type' => 'file',
			))
		?>
		<?=$this->AdminForm->input('Forum.id')?>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Parent')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('parent_id', array('empty' => __d('forum_admin', 'None')))?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Title')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('title')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Slug')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('slug')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Description')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('description', array('type' => 'textarea'))?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Icon')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('file', array('type' => 'file'))?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Status')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('status')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Order')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('orderNo')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Auto Locking')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('autoLock', array('type' => 'checkbox'))?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Topic Count')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('topic_count')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Post Count')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('post_count')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Access Read')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('accessRead', array(
						'options' => $aros,
						'empty' => __d('forum_admin', 'All'),
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Access Post')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('accessPost', array(
						'options' => $aros,
						'empty' => __d('forum_admin', 'All'),
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Access Poll')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('accessPoll', array(
						'options' => $aros,
						'empty' => __d('forum_admin', 'All'),
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('forum_admin', 'Access Reply')?></label>
			<div class="col-sm-4">
				<?=
					$this->AdminForm->input('accessReply', array(
						'options' => $aros,
						'empty' => __d('forum_admin', 'All'),
					))
				?>
			</div>
		</div>
		<div class="text-center col-sm-12 paddingten">
			<button class="btn btn-primary"><?=__d('forum_admin', 'Save Info')?></button>
		</div>
		<?=$this->AdminForm->end()?>
	</div>
</div>
