<?php

include('auth.php');

$auth = new WebkioskAuth('<YOUR_USERNAME>', '<YOUR_PASSWORD>');

try {
  $auth->authenticate();
} catch (IncorrectLoginCredentialsException $e) {
  print($e->errorMessage());
}

if ($auth->getAuthStatus()) {
  print 'Username and password verified.';
}

?>
