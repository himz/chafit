<?php
//Taken from FireEagle example at php.net http://www.php.net/manual/en/oauth.examples.fireeagle.php

$conskey="Wu1ScwYWgQAnOIpeM8p7PAMUSbkCjtusvskFvzVG7lnFDIOvcd"; //Consumer Key
$conssec="C5uP8LQldF0dIVgDXqI2ada034qdsffdWzWoLPLsjxxHuYbS5y"; //Consumer Secret
$req_url = 'http://www.tumblr.com/oauth/request_token';
$authurl = 'http://www.tumblr.com/oauth/authorize';
$acc_url = 'http://www.tumblr.com/oauth/access_token';
$api_url = 'http://api.tumblr.com/v2/blog';


$blogurl="www.mkartik.net";		//Blog URL
session_start();

// In state=1 the next request should include an oauth_token.
// If it doesn't go back to 0
if(!isset($_GET['oauth_token']) && $_SESSION['state']==1) $_SESSION['state'] = 0;
try {
  $oauth = new OAuth($conskey,$conssec);
  $oauth->enableDebug();
  if(!isset($_GET['oauth_token']) && !$_SESSION['state']) {
    $request_token_info = $oauth->getRequestToken($req_url);
    $_SESSION['secret'] = $request_token_info['oauth_token_secret'];
    $_SESSION['state'] = 1;
    header('Location: '.$authurl.'?oauth_token='.$request_token_info['oauth_token']);
    exit;
  } else if($_SESSION['state']==1) {
    $oauth->setToken($_GET['oauth_token'],$_SESSION['secret']);
    $access_token_info = $oauth->getAccessToken($acc_url);
    $_SESSION['state'] = 2;
    $_SESSION['token'] = $access_token_info['oauth_token'];
    $_SESSION['secret'] = $access_token_info['oauth_token_secret'];
  } 
  $oauth->setToken($_SESSION['token'],$_SESSION['secret']);

  $oauth->fetch("$api_url/$blogurl/post",array("type"=>"text","title"=>"I just donated","body"=>" I just donated $10 to a charity"),"POST");
  $json = json_decode($oauth->getLastResponse());
  print_r($json);

} catch(OAuthException $E) {
  print_r($E);
}
?>
