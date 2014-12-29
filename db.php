<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "birthday_thanker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function isPostThanked($conn, $fbId, $id) {
	$sql = "select * from posts_thanked where fb_id='" . $fbId . "' and post_id='" . $id . "';";
	$dbResult = $conn->query($sql);

	if ($dbResult === FALSE) {
		echo $conn->error . "<br/>";
	} else if ($dbResult->num_rows > 0) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function markPostThanked($conn, $fbId, $postId) {
	$sql = "insert into posts_thanked(fb_id, post_id) values('" . $fbId . "', '" . $postId . "');";
	if ($conn->query($sql) === FALSE) {
		echo "Error: " . $sql . "<br/>" . $conn->error . "<br/>";
	}
}
?>