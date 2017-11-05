<?php

namespace Orpheus\Web\Facebook;

use Facebook\Facebook;
use Facebook\GraphNodes\GraphUser;


class FacebookService extends Facebook {
	
	const ALBUM_TYPE_PROFILE = 'profile';
	
	protected static $service;
	
	public function __construct(array $config = array()) {
		$config += array(
			'app_id' => FACEBOOK_APP_ID,
			'app_secret' => FACEBOOK_APP_SECRET,
			'default_graph_version' => 'v2.2'
		);
		parent::__construct($config);
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
		/* @var Facebook\FacebookResponse $response */
		$response = $this->get('/me'.($fields ? '?fields='.(is_array($fields) ? implode(',', $fields) : $fields) : ''), $accessToken);
		
		return $response->getGraphUser();
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
		
			// The OAuth 2.0 client handler helps us manage access tokens
			$oAuth2Client = $this->getOAuth2Client();
		
			// Get the access token metadata from /debug_token
			$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		
			// Validation (these will throw FacebookSDKException's when they fail)
			$tokenMetadata->validateAppId(FACEBOOK_APP_ID); // Replace {app-id} with your app id
			// If you know the user ID this access token belongs to, you can validate it here
			$tokenMetadata->validateExpiration();
		
			if( !$accessToken->isLongLived() ) {
				// Exchanges a short-lived access token for a long-lived one
				$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
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
	
	public static function getService() {
		if( !static::$service ) {
			static::$service = new static();
		}
		return static::$service;
	}
}

define('HTTP_METHOD_GET', 'GET');
