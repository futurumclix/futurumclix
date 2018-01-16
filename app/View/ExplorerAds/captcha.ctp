<?=
	$this->UserForm->create(false, array(
		'onsubmit' => 'verifyCaptcha();return false;',
		'id' => 'captchaForm',
		'url' => array('controller' => 'explorer_ads', 'action' => 'verify_captcha', $adId),
	))
?>

<?=$this->Captcha->show($captchaType, $captchaKeys)?>
<?=$this->UserForm->end()?>
<script>
	document.write = function(a){$("#captchaForm").append(a);};

	<?php if($captchaType == 'reCaptcha'): ?>
		var intervalId = window.setInterval(function(){
			if($('#g-recaptcha-response').val().length > 5) {
				$('#captchaForm').submit();
				clearInterval(intervalId);
			}
		}, 100);
	<?php endif; ?>
</script>
