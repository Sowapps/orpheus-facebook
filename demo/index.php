<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();

define('VENDOR_PATH', dirname(__DIR__) . '/vendor');

// echo VENDOR_PATH.'/autoload.php<br>';
if( !file_exists(VENDOR_PATH . '/autoload.php') ) {
	throw new Exception('Unable to find vendor autoload, check composer.');
}
$PackageLoader = require_once VENDOR_PATH . '/autoload.php';
// $PackageLoader->add('Orpheus\\Web\\Facebook\\', '../../orpheus-facebook/src/');

// require_once '../../git/orpheus-facebook/src/';

// *** USE YOURS ***
// define('FACEBOOK_APP_ID',		'');
// define('FACEBOOK_APP_SECRET',	'');

use Orpheus\Web\Facebook\FacebookService;

$FBService = new FacebookService();
// $fb = new Facebook\Facebook([
// 		'app_id' => '{app-id}', // Replace {app-id} with your app id
// 		'app_secret' => '{app-secret}',
// 		'default_graph_version' => 'v2.2',
// ]);


$SelfURL = 'http://testfb.orpheus-framework.com/index.php';

if( !empty($_GET['logout']) ) {
	$FBService->disconnectUser();
	header('Location: '.$SelfURL);
	die();
}

$accessToken = $FBService->getAccessToken();

// if( isset($_SESSION['fb_access_token']) ) {
// 	$accessToken = $_SESSION['fb_access_token'];
	
// } else {
if( !$accessToken ) {
	// Check facebook login
	try {
		if( $FBService->connectUser() ) {
			header('Location: '.$SelfURL);
			die();
		}
		
		/*
		$accessToken = $helper->getAccessToken();
// 		echo 'FB Accesstoken => '.$accessToken.'<br>';
	
		if( $accessToken ) {
			// Logged in
	// 		echo '
	// <h3>Facebook was just logged in</h3>
	// <h6>Access Token</h6>';
	// 		var_dump($accessToken->getValue());
		
			// The OAuth 2.0 client handler helps us manage access tokens
			$oAuth2Client = $FBService->getOAuth2Client();
		
			// Get the access token metadata from /debug_token
			$tokenMetadata = $oAuth2Client->debugToken($accessToken);
	// 		echo '
	// <h6>Metadata</h6>';
	// 		var_dump($tokenMetadata);
		
			// Validation (these will throw FacebookSDKException's when they fail)
			$tokenMetadata->validateAppId(FACEBOOK_APP_ID); // Replace {app-id} with your app id
			// If you know the user ID this access token belongs to, you can validate it here
			//$tokenMetadata->validateUserId('123');
			$tokenMetadata->validateExpiration();
		
			if( !$accessToken->isLongLived() ) {
// 				echo 'Access token is not a long lived<br />';
				// Exchanges a short-lived access token for a long-lived one
				try {
					$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
// 					echo 'Got long lived access token<br />';
				} catch (Facebook\Exceptions\FacebookSDKException $e) {
					echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
					exit;
				}
		
	// 			echo '
	// <h6>Long-lived</h6>';
	// 			var_dump($accessToken->getValue());
// 			} else {
// 				echo 'Access token IS a long lived<br>'.$accessToken.'<br>';
			}
			$_SESSION['fb_access_token'] = (string) $accessToken;
			header('Location: '.$SelfURL);
// 			header('Location: index.php');
			die();
		}
		*/
		
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: '.$e;
// 		exit;
		
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: '.$e;
// 		exit;
	}
}

// $helper = $FBService->getFacebook()->getRedirectLoginHelper();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Orpheus Facebook</title>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" type="text/css" />
	
</head>
<body>
	<div class="container">
	
		<h1>Facebook Login</h1>
		<p class="lead">
			Test for facebook login.
			<a href="https://developers.facebook.com/docs/facebook-login/web" target="_blank">Documentation</a>
		</p>
		<?php
		if( $accessToken ) {
			echo '
				<h3>User is logged in</h3>';
			
			try {
				$user = $FBService->getUser('name,picture,gender');
// 				var_dump($user);echo '<br>';
				$picture = $user->getPicture();
				echo '
				<div class="media">'.($picture ? '
					<div class="media-left">
						<a href="'.$picture->getUrl().'" target="_blank">
							<img class="media-object" src="'.$picture->getUrl().'" alt="'.$user->getName().'">
						</a>
					</div>' : '').'
					<div class="media-body">
						<h4 class="media-heading">'.$user->getName().'</h4>
						Welcome, you are logged in via Facebook.
					</div>
				</div><br>';
// 				<p>Welcome '.$user->getName().', you logged in via Facebook.</p>';
			
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				echo 'Graph returned an error: '.$e;
		// 		exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				echo 'Facebook SDK returned an error: '.$e;
		// 		exit;
			}
		
			echo '
				<a class="btn btn-default" href="'.$SelfURL.'?logout=1"><i class="fa fa-fw fa-sign-out"></i> Log out</a>';
		} else {
// 		if( !$accessToken ) {
// 			$permissions = ['email']; // Optional permissions
// 			$loginUrl = $helper->getLoginUrl($SelfURL, $permissions);
			$loginUrl = $FBService->getLoginUrl($SelfURL, array('email'));
			echo '
		<a class="btn btn-primary" href="' . htmlspecialchars($loginUrl) . '"><i class="fa fa-fw fa-facebook"></i> Log in with Facebook !</a>';
		}
		
		?>

	</div>

<script>
window.fbAsyncInit = function() {
	FB.init({
		appId	: '<?php echo FACEBOOK_APP_ID; ?>',
		xfbml	: true,
		version	: 'v2.6'
	});
};

(function(d, s, id){
	var js, fjs = d.getElementsByTagName(s)[0];
	if( d.getElementById(id) ) {
		return;
	}
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>
</div>
	<!-- External JS libraries -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js"></script>
</body>
</html>
