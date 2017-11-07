<?php

namespace Orpheus\Web\Facebook;

use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\RedirectHTTPResponse;

class LoginValidatorController extends HTTPController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 * @see HTTPController::run()
	 */
	public function run(HTTPRequest $request) {
		
		try {
			if( $request->hasParameter('error_code') ) {
				$errorCode = $request->getParameter('error_code');
				$errorMessage = $request->getParameter('error_message');
				echo 'Facebook Error ('.$errorCode.')<br />'.text2HTML($errorMessage);
				return;
			}
			
			/* @var \Orpheus\Web\Facebook\FacebookService $FBService */
			$FBService = new FacebookService();

			if( $FBService->connectUser() ) {
				return $this->getValidReponse($request);
			}
		
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			echo 'Graph returned an error: '.$e;
		
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: '.$e;
		}
		
	}
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 * @see HTTPController::run()
	 */
	public function getValidReponse(HTTPRequest $request) {
		return new RedirectHTTPResponse(DEFAULTROUTE);
	}
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 * @see HTTPController::run()
	 */
	public function getInvalidReponse(HTTPRequest $request, \Exception $exception) {
		return new RedirectHTTPResponse(DEFAULTROUTE);
	}

	
}
