<?php
/**
 * reCaptcha 2.0 drop-in replacement for recaptchalib (reCaptcha API 1.0)
 *
 */

define('RECAPTCHA_SERVER', 'https://www.google.com/recaptcha/');
define('RECAPTCHA_VERIFY_URL', 'https://www.google.com/recaptcha/api/siteverify');

function recaptcha_get_html($pubkey, $theme = 'dark') {
	return '<script type="text/javascript" src="'. RECAPTCHA_SERVER . 'api.js" async defer></script>
	<div class="g-recaptcha" data-sitekey="'.$pubkey.'" data-theme="'.$theme.'"></div>
	<br/>';
}

class ReCaptchaResponse {
	var $is_valid;
	var $error;
}

function _recaptcha_http_post($url, $data, $port = 443) {
	$req = http_build_query($data);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	curl_setopt($ch, CURLOPT_PORT, 443);
	curl_setopt($ch, CURLOPT_VERBOSE, true);

	$res = curl_exec($ch);

	curl_close($ch);
	return $res;
}

function recaptcha_check_answer($privkey, $response, $extra_params = array())
{
	if($privkey == null || $privkey == '') {
		die("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
	}

	$recaptcha_response = new ReCaptchaResponse();
	$recaptcha_response->is_valid = false;
	$recaptcha_response->error = 'incorrect-captcha-sol';

	if(!$response || empty($response)) {
		return $recaptcha_response;
	}

	$response = _recaptcha_http_post(RECAPTCHA_VERIFY_URL, array(
			'secret' => $privkey,
			'response' => $response,
		) + $extra_params
	);

	$response = json_decode($response, true);

	$recaptcha_response->is_valid = $response['success'];
	if(isset($response['error_codes'])) {
		$recaptcha_response->error = $response['error_codes'];
	}

	return $recaptcha_response;
}
?>
