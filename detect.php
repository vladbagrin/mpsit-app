<?php		
require_once("global.php");
require_once("facebook.php");
require_once("utils.php");
?>
			
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>Birthday Thanker</title>
 
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.3.0/build/cssreset/reset-min.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
<script src="http://code.jquery.com/jquery-latest.js" type="text/javascript"></script>
<script src="js/script.js" type="text/javascript"></script>
</head>
 

<body background="images/wallpaper.jpg">
<div class="wrapper">
<INPUT Type="BUTTON" class="btn" VALUE="Home Page" ONCLICK="top.location.href='index.php'"> 
    <div class="maincontent">
			<div class="logo"><img src="images/thankyou2.png" width="379" height="128" alt="thankyou logo" a href="index.php"></div>
        <div class="tab_container">
		<div id="user_info">
		<div id="picture">
			<img src="<?php echo $_SESSION['PICTURE']; ?>"> </br>
		</div>
		<div id="birthday">
			<?php echo $_SESSION['BIRTHDAY']; ?>
		</div>
		</div>		
		<?php
			// session originates in facebook.php
			if ($session) {
				getCommentByDetectingLanguageOutput();
			}
		?>
        </div><!--End Tab Container -->
     
     </div><!--End Main Content-->
     
    <div class="sidebar">
    </div><!--End Sidebar-->
 
</div><!--End Wrapper -->
 
 
</body>
</html>