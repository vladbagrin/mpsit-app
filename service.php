<?php
// %s stands for the name of the addressee
$commentChoicesAddressed = array(
	"EN" => array(
		"Thanks, %s!",
		"Thank you for your wishes, %s",
		"Thanks for the wishes, %s"
	),
	"FR" => array(
		"Merci, %s!",
		"Merci beaucoup %s",
		"Merci pour tes bienfaits, %s"
	),
	"RO" => array(
		"Multumesc, %s",
		"Multumesc pentru urari, %s",
		"Mersi mult, %s!"
	)
);

$commentChoices = array(
	"EN" => array(
		"Thanks!",
		"Thank you for your wishes",
		"Thanks for the wishes"
	),
	"FR" => array(
		"Merci",
		"Merci beaucoup",
		"Merci pour tes bienfaits"
	),
	"RO" => array(
		"Multumesc",
		"Multumesc pentru urari",
		"Mersi mult!"
	)
);

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

if (isset($_POST["json_request"])) {
	$jsonRequest = $_POST["json_request"];
	$requestArray = json_decode($jsonRequest, true);
	$useBatch = True;
} else {
	$useBatch = False;
}

if (isset($_GET["name"])) {
	$name = $_GET["name"];
	$isAddressed = True;
} else {
	$name = "";
	$isAddressed = False;
}

if (isset($_GET["language"])) {
	$languageCode = $_GET["language"];
	if ($languageCode !== "EN" && $languageCode !== "FR" && $languageCode !== "RO") {
		$languageCode = "EN";
	}
} else {
	$languageCode = "EN";
}

if ($useBatch === True && $requestArray !== null) {
	$returnArray = array();
	foreach ($requestArray as $index => $postData) {
		$replyMessage = selectThanks($postData["language"], $postData["fromName"]);
		array_push($returnArray, array(
			"postId" => $postData["postId"],
			"message" => $replyMessage,
			"fromName" => $postData["fromName"]
		));
	}
} else {
	$thanksMessage = selectThanks($languageCode, $name, $isAddressed);
	$returnArray = array("reply" => $thanksMessage);
}

$jsonResponse = json_encode($returnArray);
echo $jsonResponse;

?>