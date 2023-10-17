<?php
	include("./includes/config.php");
  CheckLogin(true);
	$ip = getIpAddress();
	CleanRequestVars();
	if(Post('submit')!=null){
		if(confirmIPAddress($ip)){
			if($_SESSION['hash']==Post('hash','')){
				$user = Post("username");
				$pass = Post("password");
				$args = array($user,$pass);
				if(isnull($args)==false){
					$sql = "SELECT id, username, name, email,password, accessLevel FROM users WHERE username='$user' AND enabled=1 AND accessLevel>0 LIMIT 1";
					$userInfo=$database->getRows($sql);
					// var_dump($userInfo);
					if($userInfo!=false||count($userInfo)>0){
						$userInfo = $userInfo[0];
						if ($crypt->check($userInfo['password'], $pass)) {
							$user = new User($userInfo['id']);
							$_SESSION['admin_user_obj'] = $user;
							clearLoginAttempts($ip);
              if(isset($_SESSION['referrer'])){
                header("Location: ".$_SESSION['referrer']);
							}else
                header("Location: index.php");
						}else{
							$message = "Invalid username or password";
							addLoginAttempt($ip);
						}
					}else{
						$message =  "Invalid username or password";
						addLoginAttempt($ip);
					}
				}else{
					$message = "Invalid username or password";
					addLoginAttempt($ip);
				}
			}else {
				$message = "Invalid session please refresh and try again.";
				addLoginAttempt($ip);
        $hash = $_SESSION['hash'] = md5(uniqid(mt_rand(),true));
			}
		}else{
			$message = "Sorry you have been blocked for 30 min from logging in.<br>Someone on your ip address is trying to guess the password and has failed 25+ times";
			addLoginAttempt($ip);
		}
	}else{
		$hash = $_SESSION['hash'] = md5(uniqid(mt_rand(),true));
	}
function confirmIPAddress($value) { 
global $database;
  $q = "SELECT Attempts, (CASE when lastlogin is not NULL and DATE_ADD(LastLogin, INTERVAL 30 MINUTE)>NOW() then 1 else 0 end) as Denied FROM loginattempts WHERE ip = '$value'"; 

  
  $data = $database->getRows($q);

  //Verify that at least one login attempt is in database 

  if (!$data) { 
    return 1; 
  } 
  $data =$data[0];
  if ($data["Attempts"] >= 25) 
  { 
    if($data["Denied"] == 1) 
    { 
      return 0; 
    } 
    else 
    { 
      $this->clearLoginAttempts($value); 
      return 1; 
    } 
  } 
  return 1; 
} 

function addLoginAttempt($value) {
global $database;
   //Increase number of attempts. Set last login attempt if required.

   $q = "SELECT * FROM loginattempts WHERE ip = '$value'"; 
  $data = $database->getRows($q);
   
   if($data)
   {
   	 $data =$data[0];
     $attempts = $data["Attempts"]+1;         

     if($attempts==25) {
       $q = "UPDATE loginattempts SET Attempts=".$attempts.", lastlogin=NOW() WHERE ip = '$value'";
       $result = $database->query($q);
     }
     else {
       $q = "UPDATE loginattempts SET Attempts=".$attempts." WHERE ip = '$value'";
       $result = $database->query($q);
     }
   }
   else {
     $q = "INSERT INTO loginattempts (Attempts,IP,lastlogin) values (1, '$value', NOW())";
     $result = $database->query($q);
   }
}

function clearLoginAttempts($value) {
	global $database;
  $q = "UPDATE loginattempts SET Attempts = 0 WHERE ip = '$value'"; 
  return $database->query($q);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf8" />
    <title>Synergy Cms</title>

    <link href="assets/css/main.css" rel="stylesheet" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
    <!--[if IE]>
        <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js" type="text/javascript"></script>
    <![endif]-->
</head>

<!--Synergy Networks Content Management Backend-->
<body>
    <div id="wrapper" style='width: 440px;'>
        <a href="#" class="logo">Synergy CMS</a>

        <section id="main" style='text-align: center;'>
            <section class="login">
                <form action="login.php" method="POST">
                    <h1>Login Form</h1><hr>
					<?php if (isset($message) && $message) echo $message."<br>"; ?>
					<label for='username'>Username:</label><br>
					<input type='text' name='username'/><br>
					<label for='password'>Password:</label><br>
					<input type='password' name='password'/><br>
					<input type='hidden' name='hash' value='<?php echo(@$hash); ?>'/><br>
					<input type='submit' name='submit' value='login'/>
                </form>
            </section><!--content end-->
<?php include('includes/footer.php'); ?>
