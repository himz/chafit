<?php 
	require_once("src/FoursquareAPI.class.php");
  $client_key = "YZLHSQNJSNU1234A042RK3QZDLWBE4ECBB5WF5MNSWRO5HZB";
	$client_secret = "Q3NUPUWDB1OZKK3PZFF3YDKKCZK3XFDXEG5EDUXZ5PSQRY3B";
  $redirect_uri="http://localhost/chafit/";
	// Load the Foursquare API library
	$foursquare = new FoursquareAPI($client_key,$client_secret);
  
  if(array_key_exists("code",$_GET)){
		$token = $foursquare->GetToken($_GET['code'],$redirect_uri);
	}
?>

<!doctype html>
<html>
<head>
	<title>CharFit</title>
  <script src="http://code.jquery.com/jquery-latest.js"></script>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <script src="js/bootstrap.min.js"></script>
</head>
<body>
  
<div class="page-header">
  <h1>CharFit<small> - Get Fit or end up donating !</small></h1>
</div>
  <div class = "row">
  
  <div class="span5 offset1">
  
  	<?php 
	// If we have not received a token, display the link for Foursquare webauth
	if(!isset($token)){ 
		echo "<a href='".$foursquare->AuthenticationLink($redirect_uri)."'>Connect to this app via Foursquare</a>";
	// Otherwise display the token
	}else{
		//echo "Your auth token: $token";
    $foursquare->SetAccessToken($token);
    $today = time();
    $week= array();
    $week[0] = array("start" => strtotime("22 September 2012"), "end" => strtotime("30 September 2012"));
    $week[1] = array("start" => strtotime("30 September 2012"), "end" => strtotime("6 October 2012"));
    
    for($i =0; $i<2; $i++){
    $params= array("beforeTimestamp" => $week[$i]['end'], "afterTimestamp" => $week[$i]['start'] );
    $response = $foursquare->GetPrivate("users/self/venuehistory",$params);
    echo "<h2>Week ".($i + 1)."</h2>";
    $result = json_decode($response);
    //var_dump($result);
    if(isset($result->response->venues->count)){
      $times = $result->response->venues->count;
    }
    else{
      $times = 0;
    }
    if($times >=4 ){
      echo "<div class='row'><iframe width='400px' height='300px' src='https://kar2905.cartodb.com/tables/week".($i+1)."/embed_map'></iframe></div>";
      echo '<i class="icon-ok"></i>';
      echo "Congrats ! You went to the Gym " .$times." times in this week";
    }
    else{
      echo "<div class='row'><iframe  width='400px' height='300px' src='https://kar2905.cartodb.com/tables/week".($i+1)."/embed_map'></iframe></div>";
      echo '<i class="icon-remove"></i>';
      echo "Sorry, you just went $times times :(. You need to pay now.";
      
      //sleep(10);
      system("php src/twilio.php");
      file_get_contents("http://localhost/chafit/src/tumblr.php");
      ?>
      <script>
      <!--
      function delay(){
        window.location = "https://venmo.com/?txn=Pay&recipients=helpachild&amount=5&note=here%20is%20my%20lazy%20contribution%20for%20the%20charity";
      }
      //setTimeout('delay()', 5000);

      //-->
      </script>
      <?php
    }
    //echo "<hr/>";
    echo "</div><div class='span5 offset1'>";
    }
    echo "</div>";


	}
  echo "</div><div class='row'><h2>Results from Etsy</h2>";
  $res = file_get_contents("http://openapi.etsy.com/v2/listings/active.json?keywords=gym&api_key=svo0qhuhxb7gfgwsqoe4rmpe");
  $r = json_decode($res);
  echo "<ul>";
  foreach($r->results as $x){
    echo "<li><a href='".$x->url."'>".$x->title."</a></li>";
  }
  echo "</ul>";
  
	
	?>
	  
</div>
</body>
</html>
