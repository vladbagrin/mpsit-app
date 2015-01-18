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

if (getenv('IS_HEROKU')) {
	FacebookSession::setDefaultApplication("1522623887991819", "add1bd65208deeeaf85a70bb33034b22");
	$fb_app_id = "1522623887991819";
} else {
	FacebookSession::setDefaultApplication("1536267096623198", "0f3ec72570d77672f2e2ad68a29f8aea");
	$fb_app_id = "1536267096623198";
}

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

// the user is logged in or authorized
if ($session) {
	try {
		$userProfile = (new FacebookRequest(
		  $session, "GET", "/me?fields=birthday,id"
		))->execute()->getGraphObject(GraphUser::className());
		
		$fbId = $userProfile->getProperty("id");
		$birthday = $userProfile->getProperty("birthday");
		
		$picture = (new FacebookRequest(
		  $session, "GET", "/me/?fields=picture"
		))->execute()->getGraphObject(GraphObject::className())->getProperty("picture")->getProperty("url");
		
		//$picture = $profilePicture->getProperty("picture");
	
		
	} catch (FacebookRequestException $e) {
		echo "Exception occured, code: " . $e->getCode();
		echo " with message: " . $e->getMessage();
		return;
	}
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