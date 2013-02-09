<!--this part is for connecting with facebook and -->
<?php
require ('facebook-php-sdk-master/src/facebook.php');

function idx(array $array, $key, $default = null) {
  return array_key_exists($key, $array) ? $array[$key] : $default;
}

//creat an facebook object
$facebook = new Facebook( array('appId' => '480752041981146', 'secret' => 'e6fe578af9929362a60e6faf65a78e28', ));

//get user ID
$user_id = $facebook -> getUser();

if ($user_id) {
	try {
		$logoutUrl = $facebook -> getLogoutUrl();
		
		$amer_friend_list = $facebook->api(array(
		      'method' => 'fql.query',
		      'query' => 'SELECT uid2 FROM friend WHERE uid1 = me()',
		));
	} catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	}
} else {
		$loginUrl = $facebook -> getLoginUrl(array( 'scope' => 'user_birthday,user_relationships,friends_birthday,friends_relationships,read_friendlists',
'redirect_uri' => 'http://apps.facebook.com/my-app-z', ) );
}
?>
<!--this part will to connect with appfog database-->

<html>
	<head>
		<title>my second working apps </title>
	</head>
	<body>
		<h3> Hello world </h3>
      
		<?php if ($user_id):?>
		<a href=" <?php echo $logoutUrl; ?> "> Facebook Logout </a>
		<?php else : ?>
		<a href=" <?php echo $loginUrl; ?>"> Facebook Login </a>
		<?php endif ?>
		<pre> <?php print_r($_SESSION); ?></pre>										
		
		<?php if ($user_id):?>
			<?php 
			  $amer_final_uid = $user_id;
			  echo "amer final uid = ".$amer_final_uid."_".$user_id."<br />";
			?>
			
			<?php
			  echo "amer friend list = > <br />";
			      foreach ($amer_friend_list as $friend) {
				$id = idx($friend, 'uid2');
				echo $id."<br />";
			      }
			?>
			
		<?php endif ?>
		
		

	</body>
</html>