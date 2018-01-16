	<div class="col-sm-12 copyright text-xs-right">
		<!-- Do not remove this copyright unless you bought Copyright Removal from our shop. Otherwise your licence will be suspended! -->
		<p><a href="http://futurumclix.com">Powered by FuturumClix.com</a> &copy; 2014 - <?=date('Y')?></p>
	</div>
	</div>
</div>
<div class="modal fade" id="ajax-modal-container" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div id="ajax-modal" class="modal-content">
		</div>
	</div>
</div>
<script>
$('#backToTopBtn').click(function(){
	$('html,body').animate({scrollTop:0},'slow');return false;
});
$(document).ready(function(){
	$("[data-toggle=tooltip]").tooltip({placement : 'top'});
	$("[data-toggle=popover]").popover({placement : 'top'});
	$('[data-counter]').on('keyup', charCounter);
	$('[data-toggle=modal], [data-ajaxsource]').on('click', ajaxModal);
})
</script>
<?=$this->Js->writeBuffer()?>
<?=$this->fetch('postLink')?>
</body>
</html>
