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
		$addresses = new AddressFacade();
		$picture = new PictureFacade();

        return array(
        		'name' => $user->getName(),
	        	'email' => $user->getEmail(),
	        	'role' => $user->getRoles()->getRoleId(),
	        	'oauth' => $oauth->get($user),
	        	'addresses' => $addresses->getList($user),
	        	'picture' => $picture->get($user)
        	);
	}

}
