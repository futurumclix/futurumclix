<i class="mdi mdi-18px mdi-check-circle"></i>
<span>
<?=__d('ad_grid', 'Congratulations. You\'ve won %s!', $this->Currency->format($prize))?>
</span>
<script>
	window.opener.location.reload();
</script>
