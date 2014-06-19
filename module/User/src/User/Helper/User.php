<?php

namespace User\Helper;

class User {
	
	public function getCurrentUser(){
        return $this->zfcUserAuthentication()->getIdentity();
    }

    public function getUserMapper(){
        return $this->getServiceLocator()->get('zfcuser_user_service')->getUserMapper();
    }

    public function getUserService(){
    	return new UserService($this->getServiceLocator());
    }
}