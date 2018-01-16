<?php
	$this->Html->css('Utility.decoda.min', 'stylesheet', array('inline' => false));
	$this->Html->css('Forum.decoda', 'stylesheet', array('inline' => false));
	$this->Html->script('Utility.rangyinputs.min', array('inline' => false));
	$this->Html->script('Utility.jquery.decoda', array('inline' => false));
?>

<script type="text/javascript">
	$(function() {
		var decoda = new Decoda('#<?php echo $id; ?>', {
			previewUrl: '/forum/posts/preview',
			onInitialize: function() {
				this.editor.closest('div').addClass('input-decoda');
			},
			onSubmit: function() {
				return this.clean();
			},
			onRenderHelp: function(table) {
				table.addClass('table');
			},
			onRenderToolbar: function(toolbar) {
				toolbar.find('button').each(function(idx, button) {
					$(button).data('toggle', 'tooltip').data('placement', 'top').tooltip();
				});
			}
		}).defaults();
	});
</script>
