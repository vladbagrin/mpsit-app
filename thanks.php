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
	if (!isset($_POST["checked"]) ||
		empty($_POST["checked"])) {
		
		echo "No thanks to give<br/>";
	} else {
		$checked = $_POST["checked"];
		try {
			// custom replies
			$customThanks = getCustomThanksList();
			
			foreach ($checked as $i) {
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
				$language = $_POST["language_" . $i];
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
					echo "$fromName: $commentText <br/>";
				} else {
					echo "Failed to thank $fromName for postId=$postId <br/>";
				}
			}
		} catch (FacebookRequestException $e) {
			echo "Exception occured, code: " . $e->getCode();
			echo " with message: " . $e->getMessage();
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