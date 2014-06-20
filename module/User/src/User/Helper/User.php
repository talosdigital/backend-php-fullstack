<?php

namespace User\Helper;

class User {

    protected $currentUser;
	
	public function getCurrentUser(){
        $sm = $this->getServiceLocator();
        $auth = $sm->get('zfcuser_auth_service');
        if ($auth->hasIdentity()) {
            return $auth->getIdentity();
        }
    }

    public function getUserMapper(){
        return $this->getServiceLocator()->get('zfcuser_user_service')->getUserMapper();
    }

    public function getUserService(){

    	return new UserService($this->getServiceLocator());
  
    }


}