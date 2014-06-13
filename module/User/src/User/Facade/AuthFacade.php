<?php

namespace User\Facade;

use User\Entity\User;
use User\Facade\OauthFacade;

class AuthFacade {
	
	public function get($user) {
		$oauth = new OauthFacade();
        return array(
        		'full_name' => $user->getName(),
	        	'email' => $user->getEmail(),
	        	'role' => $user->getRole(),
	        	'oauth' => $oauth->get($user->getOauth())
        	);
	}

}
