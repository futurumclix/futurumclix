<i class="fa fa-check fa-3x pull-left"></i>
<span>
	<?=__d('ad_grid', 'Congratulations. You\'ve won %s!', $this->Currency->format($prize))?>
</span>
<script>
	window.opener.location.reload();
</script>
