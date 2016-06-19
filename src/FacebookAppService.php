<?php

namespace Orpheus\Web\Facebook;

use Facebook\GraphUser;
use Facebook\FacebookRequest;
use Facebook\Entities\SignedRequest;


class FacebookAppService extends FacebookService {
	
	public function __construct($appAccessToken, SignedRequest $signedRequest = null) {
		parent::__construct($appAccessToken, $signedRequest);
	}

	public function getUserProfile($userID) {
		return (new FacebookRequest($this, HTTP_METHOD_GET, '/'.$userID))->execute()->getGraphObject(GraphUser::className())->asArray();
	}

	public function getUserFriends($userID) {
		return $this->extractListAsStdClass(
			new FacebookRequest($this, HTTP_METHOD_GET, '/'.$userID.'/friends', array('fields'=>'id,name'))
		);
	}

	public function getPhoto($photoID) {
		return $this->extractListAsStdClass(
			new FacebookRequest($this, HTTP_METHOD_GET, '/'.$photoID, array('fields'=>'id,images'))
		);
	}

	protected static $service;
	public static function getService() {
		if( static::$service === null ) {
			static::$service = new FacebookAppService(static::getAppAccessToken());
		}
		return static::$service;
	}
	
	protected static function getAppAccessToken() {
		return FACEBOOK_APP_ID.'|'.FACEBOOK_APP_SECRET;
	}
}
