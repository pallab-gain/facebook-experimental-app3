<!--all useful helper functions-->
<?php 
  function idx(array $array, $key, $default = null) {
    return array_key_exists($key, $array) ? $array[$key] : $default;
  }
 //determine if the user is new
  function isExistinguser( $link, $user_er_id ){
    $sql_string = "SELECT COUNT( * ) as cnt FROM  `user_table` WHERE  `user_id` = '$user_er_id'";
    $data = mysql_query($sql_string, $link);
    $row = mysql_fetch_assoc($data);
    
    return $row['cnt'] ? true : false;
  }
  //get user friend list
  function getFriendlist($link, $user_er_id){
    $sql_string = "SELECT `friend_list` FROM  `user_table` WHERE  `user_id` = '$user_er_id'";
    $data = mysql_query($sql_string, $link);
    $row = mysql_fetch_assoc($data);
    return $row['friend_list'];
  }
  function insertNewuser($link, $user_er_id, $currentFriends){
    $sql_string= "INSERT INTO `user_table`(`user_id`, `friend_list`) VALUES ( '$user_er_id','$currentFriends')";
    if(!mysql_query($sql_string,$link)){
      die('Can not insert user into database ' . mysql_error());
    }
    echo 'Insert into database successfully <br/>';
  }
  function updateFriends($link, $user_er_id, $currentFriends){
      $sql_string = "UPDATE `user_table` SET `friend_list` = '$currentFriends' WHERE `user_id`='$user_er_id' ";
      if(!mysql_query($sql_string,$link)){
	die('Can not update database ' . mysql_error());
      }
    echo 'Update database successfully <br/>';
  }
  function getStupidfriends($pastFriends, $currentFriends){
      $stupid = array();
      foreach( $pastFriends as $friend ){
	if( !in_array($friend, $currentFriends) ){
	  array_push($stupid, $friend);
	}
      }
      return $stupid;
  }
?>
<!--this part is for connecting with facebook and -->
<?php
require ('facebook-php-sdk-master/src/facebook.php');
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
<?
    $services_json = json_decode(getenv("VCAP_SERVICES"),true);
    $mysql_config = $services_json["mysql-5.1"][0]["credentials"];
    $username = $mysql_config["username"];
    $password = $mysql_config["password"];
    $hostname = $mysql_config["hostname"];
    $port = $mysql_config["port"];
    $db = $mysql_config["name"];
    $link = mysql_connect("$hostname:$port", $username, $password);
    if (!$link) {
	die('Could not connect: ' . mysql_error());
    }
//     echo 'Connected successfully <br />';    
    $db_selected = mysql_select_db($db, $link);
    if (!$db_selected) {
	die('Could not select database: ' . mysql_error());
    }
//     echo 'Database selection successfully <br/>';
?>
<html>
	<head>
		<title>Know Who Deleted You</title>
	</head>
	<body>
		<h3> Successfully Processed Your Information </h3>
      
		<?php if ($user_id):?>
		<a href=" <?php echo $logoutUrl; ?> "> Facebook Logout <br/></a>
		<?php else : ?>
		<a href=" <?php echo $loginUrl; ?>"> Facebook Login <br /></a>
		<?php endif ?>
		
		<?php if ($user_id):?>
			<?php 
			  $user_er_id = $user_id;
			  echo "Hello  = ".$user_er_id."<br />";
			  //akta akta kore user'er friend current friend list'a add korlam
			  $currentFriendlistArray = array();
			      foreach ($amer_friend_list as $friend) {
				$id = idx($friend, 'uid2');
				array_push($currentFriendlistArray,$id);
			      }  
			  //database'a update korber jonno array'ke string of ID's a convert kore rakhlam
			  $currentFriendlist = implode(',',$currentFriendlistArray);
			?>
			
			<?php
			  //if user is already present in the database
			  if( isExistinguser($link, $user_er_id) ){
			      //ter past friend gula database theke load korlam
			      $pastFriendlist = getFriendlist($link,$user_er_id);
			      $pastFriendListArray = explode(',',$pastFriendlist);
			      
			      $stupidFriendArray = getStupidfriends($pastFriendListArray,$currentFriendlistArray);
			      if( count($stupidFriendArray) ){
				echo "get list of your stupid friends !!! <br/>";
				foreach( $stupidFriendArray as $friends){
				  echo $friends. '<br/>';
				}    
			      }else{
				echo "wow!!!, your friends know the values of you. you have such buch of beautiful friend in facebook. <br/>";
			      }
			      if( count($pastFriendListArray)!= count($currentFriendlistArray) ){
				updateFriends($link, $user_er_id, $currentFriendlist);
			      }
			  }else{
			      echo 'welcome new user. I wish everyboy of your friends will be so nice, and I never have to say who deleted you <br />';
			      insertNewuser($link,$user_er_id,$currentFriendlist);
			  }
			  //closing the database connection
			  mysql_close($link);
			?>
			
		<?php endif ?>
		
		

	</body>
</html>