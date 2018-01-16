    <div class="counter insidenav">
        <?php echo $this->Paginator->counter(__d('admin', 'Showing %s-%s of %s', array(
            '<span>{:start}</span>',
            '<span>{:end}</span>',
            '<span>{:count}</span>'
        ))); ?>
    </div>
    
    <nav class="<?php echo $class; ?>">
	    <ul class="pagination pagination-sm">
	        <?php echo $this->Paginator->numbers(array(
	            'first' => __d('admin', 'First'),
	            'last' => __d('admin', 'Last'),
	            'currentTag' => 'a',
	            'currentClass' => 'active',
	            'separator' => '',
	            'ellipsis' => '<li><span>...</span></li>',
	            'tag' => 'li',
	            'class' => 'page-item'
	        )); ?>
	    </ul>
	</nav>

    <script type="text/javascript">
        $(function() {
            // Add button class to pagination links since CakePHP doesn't support it
            $('.pagination a').addClass('page-link');
        });
    </script>
</nav>
