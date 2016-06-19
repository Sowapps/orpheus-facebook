<?php

namespace Orpheus\Web\Facebook;

class LoginValidatorController extends \HTTPController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 * @see HTTPController::run()
	 */
	public function run(\HTTPRequest $request) {
		
		try {
			$FBService = new FacebookService();

			if( $FBService->connectUser() ) {
				return $this->getValidReponse($request);
// 				header('Location: '.$SelfURL);
// 				die();
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
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 * @see HTTPController::run()
	 */
	public function getValidReponse(\HTTPRequest $request) {
		return new \RedirectHTTPResponse(DEFAULTROUTE);
	}
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 * @see HTTPController::run()
	 */
	public function getInvalidReponse(\HTTPRequest $request, \Exception $exception) {
		return new \RedirectHTTPResponse(DEFAULTROUTE);
	}

	
}
