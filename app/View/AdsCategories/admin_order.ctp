<div class="col-md-12">
		<div class="title">
			<h2><?=__d('admin', 'Order PTC categories')?></h2>
		</div>
		<?php if(count($adsCategories) > 0): ?>
		<p class="text-center"><?=__d('admin', 'You can set the order of PTC categories in here. All the changes will be visible on user side while clicking ads on View Advertisement page.')?></p>
		<ul id="draggablePanelList" class="list-unstyled">
			<?php $i = 0; foreach($adsCategories as $adsCat): ?>
				<li class="panel panel-info" data-catno="<?=$i?>">
					<div class="panel-heading"><?=$adsCat['AdsCategory']['name']?></div>
				</li>
				<?php $i++; endforeach; ?>
		</ul>
		<?=$this->AdminForm->create('AdsCategoriesOrder')?>
			<?php $i = 0; foreach($adsCategories as $adsCat): ?>
				<?=$this->AdminForm->input($i.'.id', array(
					'type' => 'hidden',
					'value' => $adsCat['AdsCategory']['id'],
				))?>
				<?=$this->AdminForm->input($i.'.position', array(
					'type' => 'numeric',
					'style' => 'display: none',
					'readonly' => true,
					'value' => $adsCat['AdsCategory']['position'],
				))?>
			<?php ++$i; endforeach; ?>
			<div class="text-center">
				<button class="btn btn-primary"><?=__d('admin', 'Save')?></button>
			</div>
		<?=$this->AdminForm->end()?>
		<?php else: ?>
		<p class="text-center"><?=__d('admin', 'Before you start ordering categories, you should create any. You can do it %s.', $this->Html->link(__d('admin', 'here'), array('action' => 'add')))?></p>
		<?php endif; ?>
</div>
<?php
	$this->Js->buffer("
		jQuery(function($) {
		    var panelList = $('#draggablePanelList');
		    panelList.sortable({
		        handle: '.panel-heading', 
		        update: function() {
		            $('.panel', panelList).each(function(index, elem) {
		               var listItem = $(elem), newIndex = listItem.index();
		               $('#AdsCategoriesOrder'+listItem.data('catno')+'Position').val(newIndex);
		            });
		        }
		    });
		});
	");
?>
