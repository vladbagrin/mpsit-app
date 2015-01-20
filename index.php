<?php		
require_once("global.php");
require_once("facebook.php");
require_once("utils.php");
// Link to the current page, for the like button
$link = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
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
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=<?=$fb_app_id?>&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class="wrapper">
	<p>
	<?php
	$ch = curl_init("$serverUrl/service.php");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
	curl_setopt($ch, CURLOPT_POST, True);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "value=ceva");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$data = curl_exec($ch);
	curl_close($ch);
	if ($data !== FALSE) {
		echo $data;
	} else {
		echo "Error :(";
	}
	?>
	</p>
	<div> </br> </div>
	
    <div class="maincontent">
			<div class="logo"><img src="images/thankyou2.png" width="379" height="128" alt="thankyou logo" a href="index.html"></div>
			 
		
        <div class="tab_container">
			<div class="pagina"> 
				<div class="line">
					<form action="detect.php" method="post" class="tile_form">
						<input type="hidden" name="signed_request" value="<?=$_REQUEST['signed_request']?>">
						<button class="tile tileLarge verde" name='type' type="submit" value="detect"> 
							</br> Comment by detecting language 
						</button>
					</form>
					<form action="choose.php" method="post" class="tile_form">
						<input type="hidden" name="signed_request" value="<?=$_REQUEST['signed_request']?>">
						<button class="tile tileLarge vermelho" name='type' type="submit" value="choose">					
							</br> Comment by choosing language	
						</button>
					</form>
				</div> 
				<div class="line">
					<form action="write.php" method="post" class="tile_form">
						<input type="hidden" name="signed_request" value="<?=$_REQUEST['signed_request']?>">
						<button class="tile tileLarge azul" name='type' type="submit" value="write"> 
							</br> Write your own comments
						</button>
					</form>
					<form action="like.php" method="post" class="tile_form">
						<input type="hidden" name="signed_request" value="<?=$_REQUEST['signed_request']?>">
						<button class="tile amarelo" name='type' type="submit" value="like"> 
							</br> Like posts
						</button>
					</form>
					<form action="about.php" method="post" class="tile_form">
						<input type="hidden" name="signed_request" value="<?=$_REQUEST['signed_request']?>">
						<button class="tile coral" name='type' type="submit" value="about"> 
							</br> About
						</button>
					</form>
				</div>
			</div>
        </div><!--End Tab Container -->
     </br></br></br>
	<div class="footbar">
	<div class="fb-like" data-href="<?=$link?>" data-width="200" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
	<div class="fb-comments" data-href="<?=$link?>" data-width="740" data-numposts="1" data-colorscheme="light"></div>
    </div><!--End Sidebar-->
	
     </div><!--End Main Content-->
 
</div><!--End Wrapper -->
 
 
</body>
</html>