<i class="mdi mdi-18px mdi-check-circle"></i>
<span>
<?=__('Advertisement credited.')?><br />
<?=__('You have earned %s for watching this ad.', $this->Currency->format($earn))?>
</span>
<script>
	window.opener.location.reload();
</script>
