<?php

namespace Orpheus\Web\Facebook\Controller;

use Exception;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\GraphNodes\GraphUser;
use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\RedirectHTTPResponse;
use Orpheus\Web\Facebook\FacebookService;

abstract class AbstractFacebookLoginController extends HTTPController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @see HTTPController::run()
	 */
	public function run($request) {
		
		try {
			if( $request->hasParameter('error_code') ) {
				$errorCode = $request->getParameter('error_code');
				$errorMessage = $request->getParameter('error_message');
				return $this->getErrorReponse($errorCode, $errorMessage);
			}
			
			$fbService = new FacebookService();
			
			if( $fbService->connectUser() ) {
				
				$fbUser = $fbService->getUser('id,name,email,picture.type(large)');
				$this->connectUser($fbService, $fbUser);
				
				return $this->getValidResponse($request);
			}
			
		} catch( FacebookResponseException $e ) {
			// When Graph returns an error
			echo 'Graph returned an error: ' . $e;
			
		} catch( FacebookSDKException $e ) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e;
		}
		
		die();
	}
	
	public function getErrorReponse($errorCode, $errorMessage) {
		echo 'Facebook Error (' . $errorCode . ')<br />' . text2HTML($errorMessage);
		die();
	}
	
	public abstract function connectUser(FacebookService $fbService, GraphUser $fbUser);
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return RedirectHTTPResponse
	 * @see HTTPController::run()
	 */
	public function getValidResponse($request) {
		return new RedirectHTTPResponse(DEFAULTROUTE);
	}
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @see HTTPController::run()
	 */
	public function getInvalidResponse($request, Exception $exception) {
		return new RedirectHTTPResponse(DEFAULTROUTE);
	}
	
	
}
