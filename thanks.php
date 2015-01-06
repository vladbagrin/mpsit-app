<?php
require_once("global.php");
require_once("facebook.php");

// TODO: move when have better architecture
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\GraphObject;
use Facebook\GraphUser;

// session originates in facebook.php
if ($session) {

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
			</br>
            <div id="list4">	
			<ul>
			
<?php
	// in case no birthday wish was checked
	if (isset($_POST["checkedThank"]) &&
		!empty($_POST["checkedThank"])) {
		
		$checkedThank = $_POST["checkedThank"];
		
		try {
			// custom replies
			$customThanks = getCustomThanksList();
			
			// chosen language
			if (isset($_POST["language"])) {
				$chosenLanguage = $_POST["language"];
				if ($chosenLanguage !== "EN" &&
					$chosenLanguage !== "FR" &&
					$chosenLanguage !== "RO") {
					$chosenLanguage = null;
				}
			} else {
				$chosenLanguage = null;
			}			
			foreach ($checkedThank as $i) {
				if (!isset($_POST["post_id_" . $i])) {
					echo "<li><div class=\"item\"> <div class=\"error\"> Invalid form: no post_id_" . $i. "</div></div></li>";
					continue;
				}
				if (!isset($_POST["author_id_" . $i])) {
					echo "<li><div class=\"item\"> <div class=\"error\"> Invalid form: no author_id_" . $i. "</div></div></li>";
					continue;
				}
				if (!isset($_POST["language_" . $i])) {
					echo "<li><div class=\"item\"> <div class=\"error\"> Invalid form: no language_" . $i. "</div></div></li>";
					continue;
				}
				
				$postId = $_POST["post_id_" . $i];
				$language = $chosenLanguage === null ? $_POST["language_" . $i] : $chosenLanguage;
				$fromId = $_POST["author_id_" . $i];
				$fromName = getUserName($session, $fromId);
				
				if (empty($customThanks)) {
					$id = postComment($session, $postId, $fromName, $language);
				} else {
					$id = postCustomComment($session, $postId, $fromName, $customThanks);
				}
				
				// TODO: is this good error checking?
				if ($id !== null) {
					$commentText = getCommentText($session, $id);
					echo "<li><div class=\"item\"> <div class=\"thanked\"> <p class=\"fromName\"> $fromName </p> <p class=\"commentText\"> $commentText";
					echo "</p></div></div></li>";
				} else {
					echo "<li><div class=\"item\"> <div class=\"error\"> Failed to thank $fromName for postId=$postId </div></div></li>";
				}
			}
		} catch (FacebookRequestException $e) {
			echo "Exception occured, code: " . $e->getCode();
			echo " with message: " . $e->getMessage();
		}
	}
	// which posts to like
	// keeping this separate in case we just want to like some posts
	if (isset($_POST["checkedLike"]) && !empty($_POST["checkedLike"])) {
		$checkedLike = $_POST["checkedLike"];
		foreach ($checkedLike as $i) {
			if (!isset($_POST["post_id_" . $i])) {
				echo "<li><div class=\"item\"> <div class=\"error\">  Invalid form: no post_id_" . $i. "</div></div></li>";
				continue;
			}
			
			$postId = $_POST["post_id_" . $i];
			
			try {
				$likeResult = like($session, $postId);
				
				if ($likeResult) {
					$fromId = $_POST["author_id_" . $i];
					$fromName = getUserName($session, $fromId);
					echo "<li><div class=\"item\"> <div class=\"liked\"> <p class=\"fromName\"> $fromName </p> <p class=\"commentText\"> <b> Liked </b> </p> </div> </div></li>";
				}
			} catch (FacebookRequestException $e) {
				echo "<br/>Exception occured, code: " . $e->getCode();
				echo " with message: " . $e->getMessage() . "<br/>\n";
			}
		}
	}
	
	?>
		</ul>
		 </div>
		 </div><!--End Tab Container -->
	     </div><!--End Main Content-->
     
			<div class="sidebar">
			</div><!--End Sidebar-->
		 
		</div><!--End Wrapper -->
		</body>
		</html>
	<?php
}

function getCustomThanksList() {
	$list = array();
	$i = 0;
	
	//echo "<br/>Custom thanks:<br/>\n";
	while (isset($_POST["custom_thanks_" . $i])) {
		$message = $_POST["custom_thanks_" . $i];
		array_push($list, $message);
		//echo "$message<br/>";
		$i++;
	}
	//echo "<br/>";
	
	return $list;
}

function selectThanks($languageCode, $addressee, $includeAddressed = TRUE) {
	global $commentChoices, $commentChoicesAddressed;

	if (!array_key_exists($languageCode, $commentChoices)) {
		echo "Language $languageCode not supported<br/>\n";
	} else if ($includeAddressed && rand(0, 1) == 1) {
		$pool = $commentChoicesAddressed[$languageCode];
		$i = array_rand($pool);
		return sprintf($pool[$i], $addressee);
	} else {
		$pool = $commentChoices[$languageCode];
		$i = array_rand($pool);
		return $pool[$i];
	}
}

function selectCustomThanks($pool, $addressee) {
	$i = array_rand($pool);
	$text = $pool[$i];
	
	// add the addressee name if there is a %s in the string
	if (strpos($text, "%s") === FALSE) {
		return $text;
	} else {
		return sprintf($text, $addressee);
	}
}

// send a comment
// returns the new comment ID or null
function postComment($session, $postId, $authorName, $languageCode = "EN") {
	$request = new FacebookRequest(
	  $session,
	  "POST",
	  "/$postId/comments",
	  array (
		"message" => selectThanks($languageCode, $authorName)
	  )
	);
	$response = $request->execute();
	$graphObject = $response->getGraphObject();
	$id = $graphObject->getProperty("id");
	
	return $id;
}

// send a like
function like($session, $postId) {
	$request = new FacebookRequest(
	  $session,
	  "POST",
	  "/$postId/likes"
	);
	$response = $request->execute();
	$graphObject = $response->getGraphObject();
	$success = $graphObject->getProperty("success");
	
	return $success === True;
}

function postCustomComment($session, $postId, $authorName, $comments) {
	$request = new FacebookRequest(
	  $session,
	  "POST",
	  "/$postId/comments",
	  array (
		"message" => selectCustomThanks($comments, $authorName)
	  )
	);
	$response = $request->execute();
	$graphObject = $response->getGraphObject();
	$id = $graphObject->getProperty("id");
	
	return $id;
}

function getUserName($session, $id) {
	try {
		$userProfile = (new FacebookRequest(
		  $session, "GET", "/$id?fields=first_name"
		))->execute()->getGraphObject(GraphUser::className());
		return $userProfile->getProperty("first_name");
	} catch (FacebookRequestException $e) {
		echo "Exception occured, code: " . $e->getCode();
		echo " with message: " . $e->getMessage();
		return $id;
	}
}

function getCommentText($session, $id) {
	try {
		$comment = (new FacebookRequest(
		  $session, "GET", "/$id"
		))->execute()->getGraphObject();
		return $comment->getProperty("message");
	} catch (FacebookRequestException $e) {
		echo "Exception occured, code: " . $e->getCode();
		echo " with message: " . $e->getMessage();
		return $id;
	}
}
?>