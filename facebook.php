<?php
require_once "global.php";
require_once "facebook/autoload.php";

use Facebook\FacebookCanvasLoginHelper;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use Facebook\GraphObject;
use Facebook\GraphUser;

FacebookSession::setDefaultApplication("1536267096623198", "0f3ec72570d77672f2e2ad68a29f8aea");

$helper = new FacebookCanvasLoginHelper();
try {
	$session = $helper->getSession();
} catch(FacebookRequestException $ex) {
	// When Facebook returns an error
	echo "Facebook error";
} catch(\Exception $ex) {
	// When validation fails or other local issues
	echo "Validation failed";
}

// the user is not logged in or authorized
if ($session) {
	$userProfile = (new FacebookRequest(
      $session, 'GET', '/me?fields=birthday,id'
    ))->execute()->getGraphObject(GraphUser::className());
	
	$fbId = $userProfile->getProperty("id");
	$birthday = $userProfile->getProperty("birthday");
} else {
	$redirectHelper = new FacebookRedirectLoginHelper($canvasUrl);
	$loginUrl = $redirectHelper->getLoginUrl(array("read_stream", "publish_actions", "user_birthday"));
	?>
	<script>
		top.location.href="<?=$loginUrl?>";
	</script>
	<?php
}
?>