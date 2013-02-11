<!--all useful helper functions-->
<?php 
  function idx(array $array, $key, $default = null) {
    return array_key_exists($key, $array) ? $array[$key] : $default;
  }
  function he($str) {
    return htmlentities($str, ENT_QUOTES, "UTF-8");
  }
 //determine if the user is new
  function isExistinguser( $link, $user_er_id ){
    $sql_string = "SELECT COUNT( * ) as cnt FROM  `user_table` WHERE  `user_id` = '$user_er_id'";
    $data = mysql_query($sql_string, $link);
    if(!$data){
      return null;
    }
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
    //echo 'Insert into database successfully <br/>';
  }
  function updateFriends($link, $user_er_id, $currentFriends){
      $sql_string = "UPDATE `user_table` SET `friend_list` = '$currentFriends' WHERE `user_id`='$user_er_id' ";
      if(!mysql_query($sql_string,$link)){
	die('Can not update database ' . mysql_error());
      }
    //echo 'Update database successfully <br/>';
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
  function getNewfriends($pastFriends, $currentFriends){
      $newFriends = array();
      foreach( $currentFriends as $friend ){
	if( !in_array($friend, $pastFriends) ){
	  array_push($newFriends, $friend);
	}
      }
      return $newFriends;
  }
  function getUsername($facebook,$facebook_id){
    $infos = $facebook->api('/'.$facebook_id);
    return $infos==null ? " [ account deactivated ] " : he(idx($infos, 'name')) ;
  }
?>
<!--this part is for connecting with facebook and -->
<?php
require ('facebook-php-sdk-master/src/facebook.php');
//creat an facebook object
$facebook = new Facebook( array('appId' => '530849616955384', 'secret' => 'c22362d8de9704308ccadbaf53c77026', ));

//get user ID
$user_id = $facebook -> getUser();

if ($user_id) {
	try {
		$logoutUrl = $facebook -> getLogoutUrl();
		$basic = $facebook->api('/me');
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
'redirect_uri' => 'https://apps.facebook.com/friend-list-info', ) );
		echo ("<script> top.location.href='".$loginUrl."'</script>");
}
//list of variables
$user_er_id=null;
$currentFriendlistArray=null;
$currentFriendlist=null;
$pastFriendlist=null;
$pastFriendListArray=null;
$stupidFriendArray=null;
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
     //echo 'Connected successfully <br />';    
    $db_selected = mysql_select_db($db, $link);
    if (!$db_selected) {
	die('Could not select database: ' . mysql_error());
    }
     //echo 'Database selection successfully <br/>';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Know Your Friends</title>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <meta name="description" content="Creative CSS3 Animation Menus" />
        <meta name="keywords" content="menu, navigation, animation, transition, transform, rotate, css3, web design, component, icon, slide" />
        <meta name="author" content="Codrops" />
        <link rel="shortcut icon" href="../favicon.ico"> 
        <link rel="stylesheet" type="text/css" href="css/demo.css" />
        <link rel="stylesheet" type="text/css" href="css/style2.css" />
        <link href='http://fonts.googleapis.com/css?family=Terminal+Dosis' rel='stylesheet' type='text/css' />
    </head>
    <body>
        <div class="container">
            <div class="header">
                <string> version: 0.0.1 </string>
                <span class="right">
		    <?php if ($user_id):?>		      
                        <li class="selected"><a href=" <?php echo $logoutUrl; ?> "> <strong> Facebook Logout </strong></a> <br/></a></li>
                        <?php else : ?>
                        <li class="selected"><a href=" <?php echo $loginUrl; ?>"> <strong> Facebook Login </strong></a> </li>
		    <?php endif ?>
                </span>
                <div class="clr"></div>
            </div>
            <h1>Know Your Friends <span>
			<?php  if( $user_id && !isExistinguser($link, $user_id) ) : ?> 
			<?php echo 'You are now conncted, F5 / ctrl+R to see change. ' ; ?>
			<?php else: ?>
			<?php echo 'Know who deleted you, &amp; who added you'; ?>
			<?php endif ?>
		      </span></h1>
            <div class="content">
                <div class="more">
                    <ul>
                        <?php if ($user_id):?>
			<string > You are Logged in as : <?php echo he(idx($basic, 'name')) ?> [ F5 / ctrl+R to see change ]  </strong>			
			<?php endif ?>
                    </ul>
                </div>
                <ul class="ca-menu">
		      <?php 
			if ($user_id){
			  $user_er_id = $user_id;
			  //akta akta kore user'er friend current friend list'a add korlam
			  $currentFriendlistArray = array();
			      foreach ($amer_friend_list as $friend) {
				$id = idx($friend, 'uid2');
				array_push($currentFriendlistArray,$id);
			      }  
			  //database'a update korber jonno array'ke string of ID's a convert kore rakhlam
			  $currentFriendlist = implode(',',$currentFriendlistArray);
			}
		      ?>
		      <?php
			  //if user is already present in the database
			  if( isExistinguser($link, $user_er_id) ){
			      //ter past friend gula database theke load korlam
			      $pastFriendlist = getFriendlist($link,$user_er_id);
			      $pastFriendListArray = explode(',',$pastFriendlist);
			      
			      $stupidFriendArray = getStupidfriends($pastFriendListArray,$currentFriendlistArray);
			      $newFriendsArray = getNewfriends($pastFriendListArray,$currentFriendlistArray);
			      if( count($stupidFriendArray) ){
				foreach( $stupidFriendArray as $friends){
				  ?>
				    <li>
				      <a href="https://www.facebook.com/<?php echo he($friends); ?>">
					  <span class="ca-icon" >U</span>
					  <div class="ca-content">
					      <h2 class="ca-main"> <?php echo getUsername($facebook,$friends); ?>   </h2>
					  </div>
				      </a>
				    </li>
				  <?php
				}    
			      }
			      if( count($newFriendsArray) ){
				foreach( $newFriendsArray as $friends){
				  ?>
				    <li>
				      <a href="https://www.facebook.com/<?php echo he($friends); ?>">
					  <span class="ca-icon" >N</span>
					  <div class="ca-content">
					      <h2 class="ca-main"> <?php echo getUsername($facebook,$friends); ?>  </h2>
					  </div>
				      </a>
				    </li>
				  <?php
				}    
			      }
			      
			      if( count($pastFriendListArray)!=count($currentFriendlistArray) ){
				updateFriends($link, $user_er_id, $currentFriendlist);
			      }else{
				  ?>
				  <li style="position=center">
				  <a href="#">
				      <span class="ca-icon">N</span>
				      <div class="ca-content">
					  <h2 class="ca-main">Currently Friendlist is in equilibrium state!</h2>
				      </div>
				  </a>
				  </li>
				  <?php 
			      }
			  }else{
			      insertNewuser($link,$user_er_id,$currentFriendlist);
			  }
			  //closing the database connection
			  mysql_close($link);
			?>                    
                </ul>
            </div><!-- content -->
        </div>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
    </body>
</html>