<?php

namespace Orpheus\Web\Facebook;

use Facebook\Facebook;
use Facebook\GraphNodes\GraphUser;
// use Facebook\FacebookSession;
// use Facebook\GraphUser;
// use Facebook\FacebookRequest;
// use Facebook\GraphAlbum;
// use Facebook\Entities\SignedRequest;


class FacebookService extends Facebook {
	
	const ALBUM_TYPE_PROFILE		= 'profile';
	
// 	protected static $initialized	= false;
	protected static $service;
	
	public function __construct(array $config = array()) {
		$config += array(
			'app_id' => FACEBOOK_APP_ID,
			'app_secret' => FACEBOOK_APP_SECRET,
			'default_graph_version' => 'v2.2'
		);
		parent::__construct($config);
// 		static::initialize();
	}
	
	public function getAccessToken() {
		return isset($_SESSION['fb_access_token']) ? $_SESSION['fb_access_token'] : null;
	}
	
	public function isUserConnected() {
		if( session_status() !== PHP_SESSION_ACTIVE ) {
			throw new \Exception('Require the session started to check if user is connected');
		}
		return !!$this->getAccessToken();
	}
	
	/**
	 * 
	 * @param string[]|string|null $fields
	 * @return GraphUser
	 */
	public function getUser($fields=null) {
		$accessToken = $this->getAccessToken();
		if( !$accessToken ) {
			throw new \Exception('User is not connected');
		}
		// Returns a `Facebook\FacebookResponse` object
		$response = $this->get('/me'.($fields ? '?fields='.(is_array($fields) ? implode(',', $fields) : $fields) : ''), $accessToken);
		
		// 		$response = $FBService->get('/me?fields=id,name', $accessToken);
		// 		var_dump($response);echo '<br>';
		return $response->getGraphUser();
		// 		var_dump($user);echo '<br>';
// 		echo '
// 		<p>Welcome '.$user->getName().'</p>';
	}
	
	public function disconnectUser() {
		if( session_status() !== PHP_SESSION_ACTIVE ) {
			throw new \Exception('Require the session started to disconnect user');
		}
		unset($_SESSION['fb_access_token']);
	}
	
	/**
	 * @var boolean $connectedUser
	 * 
	 * True only if the use is just connnected
	 */
	protected $connectedUser = null;
	public function connectUser() {
		if( $this->connectedUser !== null ) {
			return $this->connectedUser;
		}
		$this->connectedUser = false;

		$helper = $this->getRedirectLoginHelper();
		
		$accessToken = $helper->getAccessToken();

		if( $accessToken ) {
			// Logged in
			// 		echo '
			// <h3>Facebook was just logged in</h3>
			// <h6>Access Token</h6>';
			// 		var_dump($accessToken->getValue());
		
			// The OAuth 2.0 client handler helps us manage access tokens
			$oAuth2Client = $this->getOAuth2Client();
		
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
// 				try {
				$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
					// 					echo 'Got long lived access token<br />';
// 				} catch (Facebook\Exceptions\FacebookSDKException $e) {
// 					echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
// 					exit;
// 				}
		
				// 			echo '
				// <h6>Long-lived</h6>';
				// 			var_dump($accessToken->getValue());
				// 			} else {
				// 				echo 'Access token IS a long lived<br>'.$accessToken.'<br>';
			}
			if( session_status() === PHP_SESSION_ACTIVE ) {
				$_SESSION['fb_access_token'] = (string) $accessToken;
			}
			$this->connectedUser = true;
			return $accessToken;
		}
		return false;
	}
	
	public function getLoginUrl($redirectUrl, array $scope = array(), $separator = '&') {
		$helper = $this->getRedirectLoginHelper();
		return $helper->getLoginUrl($redirectUrl, $scope, $separator);
	}
	
	public function getService() {
		if( !static::$service ) {
			static::$service = new static();
		}
		return static::$service;
	}
	
// 	public function __construct($accessToken = null, SignedRequest $signedRequest = null) {
// 		parent::__construct($accessToken, $signedRequest);
// 		static::initialize();
// 	}

	/*
	public function getPhotosFromID($id) {
		return $this->extractListAsStdClass(
			new FacebookRequest($this, HTTP_METHOD_GET, '/'.$id.'/photos', array('fields'=>'id,images'))
		);
	}

	public function extractListAsStdClass($input, $type='Facebook\GraphObject') {
// 		debug('Facebook - extractListAsStdClass', $input);
		if( $input instanceof Facebook\FacebookRequest ) {
// 			debug('Input is facebook request');
			$input	= $input->execute();
		}
// 		debug('extractListAsStdClass - FacebookResponse', $input);
		if( $input instanceof Facebook\FacebookResponse ) {
// 			debug('Input is facebook response', $input->getResponse());
			if( !is_object($input->getResponse()) ) {
				// Not parsed
				//::BASE_GRAPH_URL . '/' . $this->version . $this->path;
				$request = $input->getRequest();
// 				debug('Request URL', FacebookRequest::BASE_GRAPH_URL . '/' . FacebookRequest::GRAPH_API_VERSION . $request->getPath());
// 				debug('Raw response', $input->getRawResponse());
				throw new \Exception('Error getting response from facebook');
			}
// 			die('Test');
			$input	= $input->getGraphObjectList($type);
		}
// 		debug('extractListAsStdClass - object list', $input);
		$r	= array();
		foreach( $input as $object ) {
			$r[]	= (object) $object->asArray();
		}
		return $r;
	}
	
	public static function getFacebook() {
		return static::$facebook;
	}

	public static function initialize() {
		if( static::$initialized ) { return; }
		FacebookSession::setDefaultApplication(FACEBOOK_APP_ID, FACEBOOK_APP_SECRET);
		static::$facebook = new Facebook\Facebook([
			'app_id' => FACEBOOK_APP_ID,
			'app_secret' => FACEBOOK_APP_SECRET,
			'default_graph_version' => 'v2.2',
		]);
		static::$initialized	= true;
	}
	*/
}

define('HTTP_METHOD_GET', 'GET');
