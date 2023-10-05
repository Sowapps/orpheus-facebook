<?php

namespace Orpheus\Web\Facebook\Controller;

use Exception;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\GraphNodes\GraphUser;
use Orpheus\InputController\HttpController\HtmlHttpResponse;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpResponse;
use Orpheus\InputController\HttpController\RedirectHttpResponse;
use Orpheus\Web\Facebook\FacebookService;

abstract class FacebookLoginController extends HttpController {
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return HttpResponse
	 * @throws Exception
	 * @see HttpController::run()
	 */
	public function run($request): HttpResponse {
		
		try {
			if( $request->hasParameter('error_code') ) {
				$errorCode = $request->getParameter('error_code');
				$errorMessage = $request->getParameter('error_message');
				
				return $this->getErrorResponse($errorCode, $errorMessage);
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
	
	public function getErrorResponse($errorCode, $errorMessage): HtmlHttpResponse {
		return new HtmlHttpResponse('Facebook Error (' . $errorCode . ')<br />' . html($errorMessage));
		//		echo 'Facebook Error (' . $errorCode . ')<br />' . html($errorMessage);
		//		die();
	}
	
	public abstract function connectUser(FacebookService $fbService, GraphUser $fbUser);
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @return RedirectHttpResponse
	 * @see HttpController::run()
	 */
	public function getValidResponse($request) {
		return new RedirectHttpResponse(DEFAULT_ROUTE);
	}
	
	/**
	 * @param HttpRequest $request The input HTTP request
	 * @see HttpController::run()
	 */
	public function getInvalidResponse($request, Exception $exception) {
		return new RedirectHttpResponse(DEFAULT_ROUTE);
	}
	
	
}
