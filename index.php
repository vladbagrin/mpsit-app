<?php
require_once("global.php");
require_once("db.php");
require_once("facebook.php");

// TODO: move when have better architecture
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use Facebook\FacebookRequestException;

// birthday is formatted as m/d/Y
// the return variable is formatted as Y-m-d
function computeBirthDate($birthday, $year) {
	$birthDate = new DateTime($birthday);
	$m = $birthDate->format("m");
	$d = $birthDate->format("d");
	
	$birthDate = new DateTime();
	$birthDate->setDate($year, $m, $d);
	$birthDateString = $birthDate->format("Y-m-d");
	
	return $birthDateString;
}

function getNextDay($dateString) {
	$date = new DateTime($dateString);
	$date->add(new DateInterval("P1D"));
	return $date->format("Y-m-d");
}

// returns TRUE if this is a message we're interested in
function validateMessage($message) {
	return strpos($message, "multi") !== FALSE ||
		strpos($message, "sanatate") !== FALSE;
}

// TODO: sometimes the target is missing
function validateTarget($targetId) {
	return TRUE;
}

// returns a list of arrays mapping: id, authorId, targetId (or null), message
function getWishesList($session, $birthDateString) {
	//$request = new FacebookRequest($session, "GET", "/me/feed?limit=100&since=" . $birthDateString . "&until=" . getNextDay($birthDateString));
	$request = new FacebookRequest($session, "GET", "/me/feed?limit=100&since=2014-12-28T00:00:00+0000&until=2014-12-30T00:00:00+0000");
	
	// the birthday wishes to comment on
	$wishesList = array();
		
	while (!is_null($request)) {
		$response = $request->execute();
		$graphObjectList = $response->getGraphObjectList();
		
		foreach ($graphObjectList as $graphObject) {
			$type = $graphObject->getProperty("type");
			if ($type == "status") {
				$id = $graphObject->getProperty("id");
				$from = $graphObject->getProperty("from");
				$authorId = $from->getProperty("id");
				$to = $graphObject->getProperty("to");
				if ($to !== null) {
					$targetId = $to->getProperty("id");
				} else {
					$targetId = null;
				}
				$message = $graphObject->getProperty("message");
				
				// check the message text, post date and target
				// TODO: good pattern matching
				if (validateMessage($message) &&
					validateTarget($targetId)) {
					
					$wishData = array(
						"id" => $id,
						"authorId" => $authorId,
						"targetId" => $targetId,
						"message" => $message,
					);
					array_push($wishesList, $wishData);
					//var_dump($graphObject);
				}
			}
		}
		$request = $response->getRequestForNextPage();
	}
	
	return $wishesList;
}

// session originates in facebook.php
if ($session) {
	try {
		// TODO: select the year from a dropdown
		$year = "2014";
	
		// birthday comes from facebook.php
		echo "Birthday: " . $birthday . "<br/>";
		$birthDateString = computeBirthDate($birthday, $year);
		echo "Birthday wishes from " . $birthDateString . " to " . getNextDay($birthDateString) . "<br/>";
		
		$wishesList = getWishesList($session, $birthDateString);
		
		// pass variables to the commenting script via hidden form inputs
		// let the user choose whom to thank with this form
		?>
		<form method="post" action="thanks.php">
		<input type="hidden" name="signed_request" value="<?=$_REQUEST['signed_request']?>">
		<?php
		
		foreach ($wishesList as $i => $wishData) {
			$id = $wishData["id"];
			$authorId = $wishData["authorId"];
			$message = $wishData["message"];
			
			// by default only check the posts that weren't thanked yet
			if (isPostThanked($conn, $fbId, $id)) {
				$isChecked = "";
			} else {
				$isChecked = "checked";
			}
			
			?>
			<input type="checkbox" name="checked[]" value="<?=$i?>" <?=$isChecked?>/><?=$authorId?>: <?=$message?><br/>
			<input type="hidden" name="post_id_<?=$i?>" value="<?=$id?>">
			<input type="hidden" name="author_id_<?=$i?>" value="<?=$authorId?>">
			<?php
		}
		
		?>
		<input type="submit" value="Thank Selected">
		</form>
		<?php
	} catch (FacebookRequestException $e) {
		echo "Exception occured, code: " . $e->getCode();
		echo " with message: " . $e->getMessage();
	}   
}
?>