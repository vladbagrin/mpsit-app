<?php
require_once("global.php");
require_once("facebook.php");

// TODO: move when have better architecture
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use Facebook\FacebookRequestException;

// session originates in facebook.php
if ($session) {
	try {
		$year = getCurrentYear();
	
		// birthday comes from facebook.php
		echo "Birthday: " . $birthday . "<br/>";
		$timestamp = getBirthdayTimestamp($birthday, $year);
		echo "Birthday wishes from " . formatTimestamp($timestamp) . " to " . formatTimestamp(getLastDay($timestamp)) . "<br/>";
		
		$wishesList = getWishesList($session, $timestamp);
		
		// pass variables to the commenting script via hidden form inputs
		// let the user choose whom to thank with this form
		?>
		<form method="post" action="thanks.php">
		<input type="hidden" name="signed_request" value="<?=$_REQUEST['signed_request']?>">
		<?php
		
		foreach ($wishesList as $i => $wishData) {
			$id = $wishData["id"];
			$authorId = $wishData["authorId"];
			$authorName = $wishData["authorName"];
			$message = $wishData["message"];
			$hasThanked = $wishData["hasThanked"];
			$date = formatTimestamp($wishData["createdTs"]);
			
			$language = detectMessageLanguage($message);
			if ($language == null) {
				$language = "EN";
			}
			
			// by default only check the posts that weren't thanked yet
			if ($hasThanked) {
				$isChecked = "";
			} else {
				$isChecked = "checked";
			}
			
			// TODO: the functionality from Razvan's utils.php
			TODOreplaceWithGetElementType($i, $id, $authorId, $authorName, $message, $date, $isChecked, $language);
		}
		
		$commentType = $_POST["commentType"];
		
		// when writing custom messages
		if ($commentType === "write") {
			?>
			Custom message: <input type="text" name="custom_thanks_0">
			<?php
		}
		
		?>
		<input type="submit" value="Thank Selected">
		</form>
		<a target="_parent" href="<?=$canvasUrl?>">Back</a>
		<?php
		
		echo "<br/><br/>Variables:<br/>\n";
		echo "commentType=$commentType";
	} catch (FacebookRequestException $e) {
		echo "Exception occured, code: " . $e->getCode();
		echo " with message: " . $e->getMessage();
	}   
}

function TODOreplaceWithGetElementType($index, $postId, $authorId, $authorName, $message, $date, $isChecked, $language) {
	?>
	<input type="checkbox" name="checked[]" value="<?=$index?>" <?=$isChecked?>/>
		<?=$language?> <?=$date?> <?=$authorName?>: <?=$message?><br/>
	<input type="hidden" name="post_id_<?=$index?>" value="<?=$postId?>">
	<input type="hidden" name="author_id_<?=$index?>" value="<?=$authorId?>">
	<input type="hidden" name="language_<?=$index?>" value="<?=$language?>">
	<?php
}

function getCurrentYear() {
	$date = new DateTime("now");
	return $date->format("Y");
}

// birthday is formatted as m/d/Y
// the return variable is a timestamp
function getBirthdayTimestamp($birthday, $year) {
	$birthDate = new DateTime($birthday);
	$m = $birthDate->format("m");
	$d = $birthDate->format("d");
	
	$birthDate = new DateTime();
	$birthDate->setDate($year, $m, $d);
	$birthDate->setTime(0, 0);
	
	// one day before is counted, too
	return $birthDate->getTimestamp() - 24 * 60 * 60;
}

function getLastDay($start) {
	return $start + 3 * 24 * 60 * 60;
}


function formatTimestamp($ts) {
	$date = new DateTime("@$ts");
	
	// hack to set the correct timezone
	$tzDate = new DateTime();
	$date->setTimezone($tzDate->getTimezone());
	
	return $date->format("d/m/Y H:i:s");
}

// returns TRUE if this is a message we're interested in
/*
Possible phrases:

Romanian
La multi ani
Sa ai parte de fericire
Felicitari cu ziua de nastere
Ziua nasterii (lol)

English
Happy Birthday
You have to get older, but you don't have to grow up
Take a day off to celebrate you birthday

French
Je te souhaite tout le succès possible
Passe une merveilleuse journée
Bonne fête
Bon anniversaire
Joyeux anniversaire
Passe une merveilleuse journée
Je te souhaite tout le succès possible
Je te souhaite beaucoup de santé, de bonheur, des jours ensoleillés et des moments inoubliables
*/
function validateMessage($message) {
	global $roPatterns, $frPatterns, $enPatterns;
	
	foreach ($roPatterns as $pattern) {
		if (preg_match($pattern, $message) === 1) {
			return TRUE;
		}
	}
	foreach ($frPatterns as $pattern) {
		if (preg_match($pattern, $message) === 1) {
			return TRUE;
		}
	}
	foreach ($enPatterns as $pattern) {
		if (preg_match($pattern, $message) === 1) {
			return TRUE;
		}
	}
}

// returns EN, FR or RO or null
function detectMessageLanguage($message) {
	global $roPatterns, $frPatterns, $enPatterns;
	
	foreach ($roPatterns as $pattern) {
		if (preg_match($pattern, $message) === 1) {
			return "RO";
		}
	}
	foreach ($frPatterns as $pattern) {
		if (preg_match($pattern, $message) === 1) {
			return "FR";
		}
	}
	foreach ($enPatterns as $pattern) {
		if (preg_match($pattern, $message) === 1) {
			return "EN";
		}
	}
	
	return null;
}

// TODO: sometimes the target is missing
function validateTarget($targetId) {
	return TRUE;
}

function validateDate($date, $timestamp) {
	$ts = strtotime($date);
	return $ts <= getLastDay($timestamp);
}

function isPostThankedFb($comments, $fbId) {
	if ($comments == null) {
		return FALSE;
	}
	
	foreach ($comments as $comment) {
		$from = $comment->getProperty("from");
		if ($fbId === $from->getProperty("id")) {
			return TRUE;
		}
	}
	return FALSE;
}

function isUserTargeted($toList, $fbId) {
	foreach ($toList as $to) {
		$graphObject = new GraphObject($to);
		if ($fbId === $graphObject->getProperty("id")) {
			return TRUE;
		}
	}
	return FALSE;
}

// returns a list of arrays mapping: id, authorId, targetId (or null), message
function getWishesList($session, $timestamp) {
	global $fbId;
	
	$request = new FacebookRequest(
		$session,
		"GET",
		"/me/feed?fields=id,type,from,to.limit(64){id},message,created_time,comments.limit(64){from.fields(id)}&limit=100&since=" . $timestamp
	);
	
	// the birthday wishes to comment on
	$wishesList = array();
		
	while (!is_null($request)) {
		$response = $request->execute();
		$graphObjectList = $response->getGraphObjectList();
		
		foreach ($graphObjectList as $graphObject) {
			$type = $graphObject->getProperty("type");
			$createdTs = strtotime($graphObject->getProperty("created_time"));
			$dateValid = validateDate($createdTs, $timestamp);
			
			if ($type == "status" && $dateValid) {
				$id = $graphObject->getProperty("id");
				
				$from = $graphObject->getProperty("from");
				$authorId = $from->getProperty("id");
				$authorName = $from->getProperty("name");
				$message = $graphObject->getProperty("message");
				
				$to = $graphObject->getProperty("to");
				$isTargeted = FALSE;
				if ($to !== null) {
					$toList = $to->asArray();
					$isTargeted = isUserTargeted($toList, $fbId);
				}
				
				$comments = $graphObject->getProperty("comments");
				if ($comments !== null) {
					$comments = $comments->getPropertyAsArray("data");
				}
				
				// check the message text if targeted
				// TODO: good pattern matching
				if ($isTargeted && validateMessage($message)) {
					$wishData = array(
						"id" => $id,
						"authorId" => $authorId,
						"authorName" => $authorName,
						"message" => $message,
						"createdTs" => $createdTs,
						"hasThanked" => isPostThankedFb($comments, $fbId)
					);
					array_push($wishesList, $wishData);
					//var_dump($wishData);
					//var_dump($graphObject);
				}
			}
		}
		$request = $response->getRequestForNextPage();
	}
	
	return $wishesList;
}
?>