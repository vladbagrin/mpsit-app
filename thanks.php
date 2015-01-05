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
			
			echo "Thanked posts:<br/>\n";
			foreach ($checkedThank as $i) {
				if (!isset($_POST["post_id_" . $i])) {
					echo "Invalid form: no post_id_" . $i. "<br/>";
					continue;
				}
				if (!isset($_POST["author_id_" . $i])) {
					echo "Invalid form: no author_id_" . $i. "<br/>";
					continue;
				}
				if (!isset($_POST["language_" . $i])) {
					echo "Invalid form: no language_" . $i. "<br/>";
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
					echo "$fromName: $commentText";
					echo "<br/>\n";
				} else {
					echo "Failed to thank $fromName for postId=$postId <br/>";
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
	
		echo "<br/>Liked posts:<br/>\n";
		foreach ($checkedLike as $i) {
			if (!isset($_POST["post_id_" . $i])) {
				echo "Invalid form: no post_id_" . $i. "<br/>";
				continue;
			}
			
			$postId = $_POST["post_id_" . $i];
			
			try {
				$likeResult = like($session, $postId);
				
				if ($likeResult) {
					$fromId = $_POST["author_id_" . $i];
					$fromName = getUserName($session, $fromId);
					echo "$fromName<br/>\n";
				}
			} catch (FacebookRequestException $e) {
				echo "<br/>Exception occured, code: " . $e->getCode();
				echo " with message: " . $e->getMessage() . "<br/>\n";
			}
		}
	}
	
	?>
	<a target="_parent" href="<?=$canvasUrl?>">Back</a>
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