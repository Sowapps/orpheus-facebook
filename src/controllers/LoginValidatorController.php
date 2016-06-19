<?php

namespace Orpheus\Web\Facebook;

/*
 * Require Orpheus lib export 
 */

class LoginValidatorController extends \HTTPController {
	
	/**
	 * @param HTTPRequest $request The input HTTP request
	 * @return HTTPResponse The output HTTP response
	 * @see HTTPController::run()
	 */
	public function run(\HTTPRequest $request) {
		
		return new \RedirectHTTPResponse(u(User::isLogged() ? DEFAULTMEMBERROUTE : DEFAULTROUTE));
	}

	
}
