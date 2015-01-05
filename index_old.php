<?php
require_once("global.php");
require_once("facebook.php");

// session originates in facebook.php
if ($session) {
	?>
	<form method="post" action="/wishes.php">
		<input type="hidden" name="signed_request" value="<?=$_REQUEST['signed_request']?>">
		<input type="hidden" name="commentType" value="detect" />
		<button>Comment by detecting language</button>
	</form>
	<form method="post" action="/wishes.php">
		<input type="hidden" name="signed_request" value="<?=$_REQUEST['signed_request']?>">
		<input type="hidden" name="commentType" value="choose" />
		<button>Comment by choosing language</button>
	</form>
	<form method="post" action="/wishes.php">
		<input type="hidden" name="signed_request" value="<?=$_REQUEST['signed_request']?>">
		<input type="hidden" name="commentType" value="write" />
		<button>Comment comments yourself</button>
	</form>
	<?php
}
?>