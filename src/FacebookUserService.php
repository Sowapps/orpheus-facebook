<?php

use Facebook\GraphUser;
use Facebook\FacebookRequest;
use Facebook\GraphAlbum;
use Facebook\Entities\SignedRequest;


class FacebookUserService extends FacebookService {
	
	public function __construct($userAccessToken, SignedRequest $signedRequest = null) {
		parent::__construct($userAccessToken, $signedRequest);
	}

	public function getMyUserProfile() {
		return (new FacebookRequest($this, HTTP_METHOD_GET, '/me'))->execute()->getGraphObject(GraphUser::className())->asArray();
	}

	public function getMyUserAlbums() {
		return $this->extractListAsStdClass(
			new FacebookRequest($this, HTTP_METHOD_GET, '/me/albums', array('fields'=>'id,name,type'))
// 			GraphAlbum::className()
		);
	}

	public function getMyUserFriends() {
		return $this->extractListAsStdClass(
			new FacebookRequest($this, HTTP_METHOD_GET, '/me/friends', array('fields'=>'id'))
// 			GraphAlbum::className()
		);
	}

	public function getMyUserPicture() {
		return (new FacebookRequest($this, HTTP_METHOD_GET, '/me/picture', array('fields'=>'url', 'type'=>'large', 'redirect'=>'false')))
			->execute()->getGraphObject()->asArray();
	}

	public function getMyUserAlbumByType($type) {
		$albums	= $this->getMyUserAlbums();
// 		debug('Found '.count($albums).' albums.');
		foreach( $albums as $album ) {
			if( $album->type === $type ) {
				return $album;
			}
		}
// 		debug('User album with type '.$type.' not found.');
		return null;
	}

	protected $profilePictureID=null;
	public function getMyProfilePictureID() {
		if( $this->profilePictureID===null ) {
			$data	= $this->getMyUserPicture();
			if( preg_match('#/[0-9]+_([^_]+)_#', $data->url, $matches) ) {
				$this->profilePictureID	= $matches[1];
			} else {
				log_error('Unable to parse Picture ID from URL '.$data->url, 'Parsing Facebook profile picture ID in URL', false);
				$this->profilePictureID	= 0;
			}
// 			debug('Picture => '.$this->getUserPicture());
// 			debug('$matches', $matches);
		}
		return $this->profilePictureID;
	}

	public function getMyProfilePicture() {
		$photos		= $this->getMyProfilePhotos();
		$pictureID	= $this->getMyProfilePictureID();
		foreach( $photos as $photo ) {
			if( $photo->id == $pictureID ) {
				return $photo;
			}
		}
// 		return null;
		return !empty($photos) ? $photos[0] : null;
	}

	public function getMyProfilePhotos() {
		$album	= $this->getMyUserAlbumByType(self::ALBUM_TYPE_PROFILE);
		return $album ? $this->getPhotosFromID($album->id) : array();
	}

}
