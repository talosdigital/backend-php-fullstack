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

}
