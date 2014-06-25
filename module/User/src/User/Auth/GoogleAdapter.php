<?php

namespace User\Auth;

use User\Entity\User as User;
use User\Entity\User\Oauth\Oauth;
use User\Entity\User\Oauth\Twitter as TwitterDocument;
use User\Entity\User\Picture;
use User\Entity\User\Role;
use Zend\Gdata\ClientLogin;
use ZendOAuth\OAuth as OAuthService;

class GoogleAdapter extends AbstractAdapter implements IAdapter {

	const ADAPTER = "google";
	
	private $full_name;
	private $config;

	public function getClient(){

	}

	public function signup($request) {
		throw new \Exception("Not supported function", \User\Module::ERROR_NOT_SUPPORTED_FUNCTION);

	}

	public function login($request) {

	}
	
	public function logout() {
		parent::logout();
	}

    private function getUserByGoogle(){
    	
    }

    private function getGoogleUser(){ 
      
    }

    public function merge($request){
    }

    public function unmerge($data){

    }
}