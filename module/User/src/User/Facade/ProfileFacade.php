<?php

namespace User\Facade;

class ProfileFacade {
	
	public function getPasswordRequired($user){
	    if($user->getPassword()){
	        return true;
	    }
	    else{
	        return false;
	    }
     }

    public function get($user) {
		$oauth = new OauthFacade();
		$addresses = new AddressFacade($user);
        return array(
        		'full_name' => $user->getName(),
	        	'email' => $user->getEmail(),
	        	'role' => $user->getRole(),
	        	'oauth' => $oauth->get($user->getOauth()),
	        	'addresses' => $addresses->getList()
        	);
	}

}
