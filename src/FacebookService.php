<?php

use Facebook\FacebookSession;
use Facebook\GraphUser;
use Facebook\FacebookRequest;
use Facebook\GraphAlbum;
use Facebook\Entities\SignedRequest;


class FacebookService extends FacebookSession {
	
	const ALBUM_TYPE_PROFILE		= 'profile';
	protected static $initialized	= false;
	
	public function __construct($accessToken, SignedRequest $signedRequest = null) {
		parent::__construct($accessToken, $signedRequest);
		static::initialize();
	}

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
				throw new Exception('Error getting response from facebook');
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

	public static function initialize() {
		if( static::$initialized ) { return; }
		FacebookSession::setDefaultApplication(FACEBOOK_APP_ID, FACEBOOK_APP_SECRET);
		static::$initialized	= true;
	}
}

define('HTTP_METHOD_GET', 'GET');
