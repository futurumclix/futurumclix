<div class="col-md-12 itemReports form">
	<div class="title">
		<h2><?=__d('admin', 'Edit Item Report')?></h2>
	</div>
	<?=$this->AdminForm->create('ItemReport', array('class' => 'form-horizontal'))?>
		<?=$this->AdminForm->input('id')?>
		<div class="form-group">
			<label class="col-sm-2">
				<?=__d('admin', 'Reporter')?>
			</label>
			<div class="col-sm-2">
			<?php if(!empty($this->request->data['Reporter'])): ?>
				<?=$this->Html->link($this->request->data['Reporter']['username'], array('controller' => 'users', 'action' => 'edit', $this->request->data['Reporter']['id']))?>
			<?php else: ?>
				<?=__d('admin', 'User deleted')?>
			<?php endif; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2">
				<?=__d('admin', 'Reason')?>
			</label>
			<div class="col-sm-2">
				<?=$this->AdminForm->input('type_enum', array('options' => $this->Utility->enum('ItemReport', 'type')))?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2">
				<?=__d('admin', 'Reported Item')?>
			</label>
			<div class="col-sm-2">
				<?php if($viewURL): ?>
					<?=$this->Html->link($this->request->data['ItemReport']['item'], $viewURL)?>
				<?php else: ?>
					<?=__d('admin', 'Item deleted')?>
				<?php endif; ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2">
				<?=__d('admin', 'Reason')?>
			</label>
			<div class="col-sm-10">
				<?=$this->AdminForm->input('reason')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2">
				<?=__d('admin', 'Comment')?>
			</label>
			<div class="col-sm-10">
				<?=$this->AdminForm->input('comment')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2">
				<?=__d('admin', 'Action')?>
			</label>
			<div class="col-sm-10">
				<?=$this->AdminForm->input('action', array('empty' => __d('admin', 'Select Action...')))?>
			</div>
		</div>
		<?php
			echo $this->AdminForm->input('model', array('type' => 'hidden'));
			echo $this->AdminForm->input('foreign_key', array('type' => 'hidden'));
		?>
		<div class="text-center paddingten">
			<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?>
		</div>
		<?=$this->AdminForm->end();?>
</div>
