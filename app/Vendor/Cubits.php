<?php

  if(!function_exists('curl_init')) {
      throw new Exception('The Cubits client library requires the CURL PHP extension.');
  }

  require_once(dirname(__FILE__) . '/Cubits/Exception.php');
  require_once(dirname(__FILE__) . '/Cubits/ApiException.php');
  require_once(dirname(__FILE__) . '/Cubits/ConnectionException.php');
  require_once(dirname(__FILE__) . '/Cubits/Cubits.php');
  require_once(dirname(__FILE__) . '/Cubits/Requestor.php');
  require_once(dirname(__FILE__) . '/Cubits/Rpc.php');
  require_once(dirname(__FILE__) . '/Cubits/Authentication.php');
  require_once(dirname(__FILE__) . '/Cubits/ApiKeyAuthentication.php');

?>
