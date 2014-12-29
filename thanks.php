<?php
require_once("global.php");
require_once("db.php");
require_once("facebook.php");

// TODO: move when have better architecture
use Facebook\FacebookRequest;
use Facebook\GraphObject;
use Facebook\FacebookRequestException;

// send a comment
// returns the new comment ID or null
// TODO: varied comments and mention the original poster
function postComment($session, $postId) {
	$request = new FacebookRequest(
	  $session,
	  'POST',
	  '/' . $postId . '/comments',
	  array (
		'message' => 'This is a test comment',
	  )
	);
	$response = $request->execute();
	$graphObject = $response->getGraphObject();
	$id = $graphObject->getProperty("id");
	
	return $id;
}

// session originates in facebook.php
if ($session) {
	// in case no birthday wish was checked
	if (!isset($_POST["checked"]) ||
		empty($_POST["checked"])) {
		
		echo "No thanks to give<br/>";
	} else {
		$checked = $_POST["checked"];
		try {
			foreach ($checked as $i) {
				if (!isset($_POST["post_id_" . $i])) {
					echo "Invalid form: no post_id_" . $i. "<br/>";
					continue;
				}
				if (!isset($_POST["author_id_" . $i])) {
					echo "Invalid form: no author_id_" . $i. "<br/>";
					continue;
				}
				
				$postId = $_POST["post_id_" . $i];
				$fromId = $_POST["author_id_" . $i];
				$id = postComment($session, $postId);
				
				// TODO: is this good error checking?
				if ($id !== null) {
					// mark the post as thanked
					markPostThanked($conn, $fbId, $postId);
					echo "Created comment with ID: " . $id . "<br/>";
				} else {
					echo "Failed to thank: " . $postId . "<br/>";
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
?>