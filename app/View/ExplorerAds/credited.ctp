<i class="fa fa-check fa-3x pull-left"></i>
<span>
	<?=__('Advertisement credited.')?><br />
	<?=__('You have earned %s for watching this ad.', $this->Currency->format($earn))?>
</span>
<script>
window.opener.location.reload();
</script>
