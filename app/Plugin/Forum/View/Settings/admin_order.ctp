<?php
function print_child($data, &$forums) {
	echo '<li class="panel panel-primary" data-forumid="'.$data['id'].'"><div class="panel-heading">'.$data['title'].'</div>';
	if(isset($data['children'])) {
		echo '<ul class="draggablePanelList">';
		foreach($data['children'] as $child_id) {
			print_child($forums[$child_id], $forums);
		}
		echo '</ul>';
	}
	echo '</li>';
}
?>

<div class="col-md-12">
	<div class="title">
		<h2><?=__d('forum_admin', 'Order Forums')?></h2>
	</div>
	<?php if(count($forums) > 0): ?>
		<p class="text-center"><?=__d('forum_admin', 'You can set the order of Forum categories in here. All the changes will be visible on user side on forum page.')?></p>
		<ul class="draggablePanelList list-group forumorder">
			<?php foreach($forums['']['children'] as $forum_id): ?>
				<?php print_child($forums[$forum_id], $forums);?>
			<?php endforeach; unset($forums['']);?>
		</ul>
		<?=$this->AdminForm->create('Forum')?>
			<?php $i = 0; foreach($forums as $forum): ?>
				<?=$this->AdminForm->input($forum['id'].'.id', array(
					'type' => 'hidden',
					'value' => $forum['id'],
				))?>
				<?=$this->AdminForm->input($forum['id'].'.orderNo', array(
					'type' => 'numeric',
					'style' => 'display: none',
					'readonly' => true,
					'value' => $forum['orderNo'],
				))?>
			<?php ++$i; endforeach; ?>
			<div class="text-center">
				<button class="btn btn-primary"><?=__d('forum_admin', 'Save')?></button>
			</div>
		<?=$this->AdminForm->end()?>
	<?php else: ?>
		<p class="text-center"><?=__d('forum_admin', 'Before you start ordering forums, you should create any. You can do it %s.', $this->Html->link(__d('forum_admin', 'here'), array('action' => 'add')))?></p>
	<?php endif; ?>
</div>
<?php
	$this->Js->buffer("
		jQuery(function($) {
			var panelList = $('.draggablePanelList');
			panelList.sortable({
				toleranceElement: '> div',
				handle: '.panel-heading', 
				update: function() {
					$('.panel', panelList).each(function(index, elem) {
						var listItem = $(elem), newIndex = listItem.index();
						console.log(index);
						console.log(newIndex);
						console.log(elem);
						$('#Forum'+listItem.data('forumid')+'OrderNo').val(newIndex);
						console.log('#Forum'+listItem.data('forumid')+'OrderNo');
						console.log($('#Forum'+listItem.data('forumid')+'OrderNo'));
					});
				}
			 });
		});
	");
?>

