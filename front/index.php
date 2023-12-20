<?php
require_once 'vendor/autoload.php';

// Pour utiliser l'app sans passer par la google auth
$length = 8;
session_start();
if (! isset($_SESSION["logged_user_email"])) {
  $_SESSION["logged_user_email"] = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}
require(dirname(__FILE__)."/main.php");


/* $client = new Google\Client();
$client->setAuthConfig(dirname(__FILE__).'/client_credentials.json');
//$client->addScope(Google_Service_Drive::DRIVE);
$client->addScope("profile");
$client->addScope("email");
//session_destroy();
if (isset($_GET['code'])) {
    session_start();
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);
    
    // get profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email = $google_account_info->email;
    $name = $google_account_info->name;

    $_SESSION["logged_user_email"] = $email;
    // on clean l'url pour retirer le code envoyé par google
    ?>
   <script>document.location="/";</script>
    <?php
} else {
  session_start();
  if (strlen($_SESSION["logged_user_email"]) == 0 ) {
      ?>
    <script>document.location="<?php echo $client->createAuthUrl(); ?>";</script>
      <?php
    
  } else {
    require(dirname(__FILE__)."/main.php");
  }
} */


