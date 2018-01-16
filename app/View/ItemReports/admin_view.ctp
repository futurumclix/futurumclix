<div class="col-md-12 itemReports view form-horizontal">
	<div class="title">
		<h2><?=__d('admin', 'Item Report')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-2">
			<?=__d('admin', 'Id')?>
		</label>
		<div class="col-sm-2">
			<?php echo h($itemReport['ItemReport']['id']); ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2">
			<?=__d('admin', 'Reporter')?>
		</label>
		<div class="col-sm-2">
			<?php if(!empty($itemReport['Reporter'])): ?>
				<?=$this->Html->link($itemReport['Reporter']['username'], array('controller' => 'users', 'action' => 'edit', $itemReport['Reporter']['id'])); ?>
			<?php else: ?>
				<?=__d('admin', 'User deleted')?>
			<?php endif; ?>
		</div>
	</div>
	<?php if(isset($itemReport['Resolver'])): ?>
		<div class="form-group">
			<label class="col-sm-2">
				<?=__d('admin', 'Resolver')?>
			</label>
			<div class="col-sm-2">
				<?=$this->Html->link($itemReport['Resolver']['email'], array('controller' => 'admins', 'action' => 'edit', $itemReport['Resolver']['id'])); ?>
			</div>
		</div>
	<?php endif; ?>
	<div class="form-group">
		<label class="col-sm-2">
			<?=__d('admin', 'Status')?>
		</label>
		<div class="col-sm-2">
			<?php echo h($this->Utility->enum('ItemReport', 'status', $itemReport['ItemReport']['status'])); ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2">
			<?=__d('admin', 'Type')?>
		</label>
		<div class="col-sm-2">
			<?php echo h($this->Utility->enum('ItemReport', 'type', $itemReport['ItemReport']['type'])); ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2">
			<?=__d('admin', 'Model')?>
		</label>
		<div class="col-sm-2">
			<?php echo h($itemReport['ItemReport']['model']); ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2">
			<?=__d('admin', 'Foreign Key')?>
		</label>
		<div class="col-sm-2">
			<?php echo h($itemReport['ItemReport']['foreign_key']); ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2">
			<?=__d('admin', 'Item')?>
		</label>
		<div class="col-sm-2">
			<?php echo h($itemReport['ItemReport']['item']); ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2">
			<?=__d('admin', 'Reason')?>
		</label>
		<div class="col-sm-10">
			<?php echo h($itemReport['ItemReport']['reason']); ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2">
			<?=__d('admin', 'Comment')?>
		</label>
		<div class="col-sm-10">
			<?php echo h($itemReport['ItemReport']['comment']); ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2">
			<?=__d('admin', 'Created')?>
		</label>
		<div class="col-sm-2">
			<?php echo h($itemReport['ItemReport']['created']); ?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2">
			<?=__d('admin', 'Modified')?>
		</label>
		<div class="col-sm-2">
			<?php echo h($itemReport['ItemReport']['modified']); ?>
		</div>
	</div>
	<div class="text-center paddingten">
		<?=$this->Html->link(__d('admin', 'Return'), $return, array('class' => 'btn btn-danger'))?>
	</div>
</div>
