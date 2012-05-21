<?php

define('COOKIE_FILE', 'JIITWebkiosAuth_Cookie.txt');

class WebkioskAuth {
  private $username;
  private $password;

  // Stores status of authentication/verification.
  private $authStatus;

  function __construct($username, $password) {
    $this->setUsername($username);
    $this->setPassword($password);

    $this->setAuthStatus(false);
  }

  function getAuthStatus() {
    return $this->authStatus;
  }

  function getUsername() {
    return $this->username;
  }

  function getPassword() {
    return $this->password;
  }

  function setPassword($password) {
    $this->password = $password;
  }

  function setUsername($username) {
    $this->username = $username;
  }

  function setAuthStatus($authStatus) {
    $this->authStatus = $authStatus;
  }

  /**
   * Authenticates and verifies if the supplied username and password are
   * valid or not.
   */
  function authenticate() {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://webkiosk.jiit.ac.in/CommonFiles/UserAction.jsp');
    curl_setopt($ch, CURLOPT_POST, true);

    $post_fields = '';
    $post_fields .= 'txtInst=Institute&';
    $post_fields .= 'InstCode=JIIT&';
    $post_fields .= 'txtuType=Member+Type&';
    $post_fields .= 'UserType=S&';
    $post_fields .= 'txtCode=Enrollment+No&';
    $post_fields .= 'MemberCode=' . $this->getUsername() . '&';
    $post_fields .= 'txtPin=Password%2FPin&';
    $post_fields .= 'Password=' . urlencode($this->getPassword()) . '&';
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

    curl_setopt($ch, CURLOPT_HEADER, true);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, COOKIE_FILE);

    $server_output = curl_exec($ch);

    curl_close ($ch);

    if($server_output[9] == '3') {
      $this->setAuthStatus(true);
    } else {
      throw new IncorrectLoginCredentialsException;
    }

    return $this->authStatus;
  }

  /**
   * Gets some useful user information.
   */
  function getUserInfo() {
    if (!$this->getAuthStatus()) {
      throw new NotAuthenticatedException;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://webkiosk.jiit.ac.in/CommonFiles/TopTitle.jsp');
    curl_setopt($ch, CURLOPT_HEADER, true);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, COOKIE_FILE);

    $server_output = curl_exec($ch);
    preg_match('/<td>Welcome<br>[A-z]+<br>/', $server_output, $matches);
    $matches = preg_replace('/Welcome<br>/', '', $matches[0]);

    echo $matches;
    curl_close ($ch);
  }

  /**
   * Closes the session on the server.
   */
  function logout() {
    // Logout method to close the session on the server.
    // Should be taken care of so that the Webkiosk maintainers
    // don't start using their brains to block us out.
    ;
  }
}

class IncorrectLoginCredentialsException extends Exception {
  function errorMessage() {
    $errorMsg = 'Error on line ' . $this->getLine() . ' in ' . $this->getFile() . ': <br>';
    $errorMsg .= '<strong>Error:</strong> Incorrect Username or Password.';
    return $errorMsg;
  }
}

class NotAuthenticatedException extends Exception {
  function errorMessage() {
    return '<strong>Error:</strong> User has not been authenticated yet.<br>';
  }
}

?>
