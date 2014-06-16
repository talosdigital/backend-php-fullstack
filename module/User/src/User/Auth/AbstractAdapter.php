<?php

namespace User\Auth;

use User\Facade\ProfileFacade;

class AbstractAdapter {
		
	private $serviceLocator;
	private $document = "User\Entity\User";
	protected $currentUser;

	public function __construct($serviceLocator) {
		$this->serviceLocator = $serviceLocator;		
	}

	protected function logout() {
	    $this->getAuthPlugin()->getAuthAdapter()->resetAdapters();
	    $this->getAuthPlugin()->getAuthAdapter()->logoutAdapters();
	    $this->getAuthPlugin()->getAuthService()->clearIdentity();
	}
	
	protected function getServiceLocator() {
		return $this->serviceLocator;
	}

	protected function getAuthService() {
		return $this->getServiceLocator()->get('zfcuser_auth_service');
	}
	
	protected function getAuthAdapter() {
		return $authAdapter = $this->getServiceLocator()->get('ZfcUser\Authentication\Adapter\AdapterChain');
	}
	
	protected function getAuthPlugin() {
        $controllerPlugin = new \ZfcUser\Controller\Plugin\ZfcUserAuthentication;
        $controllerPlugin->setAuthService($this->getAuthService());
        $controllerPlugin->setAuthAdapter($this->getAuthAdapter());
        return $controllerPlugin;
	}

	protected function getDocument(){
		return $this->document;
	}
	
	protected function getUserService(){
        return $this->getServiceLocator()->get('zfcuser_user_service');
    }

    protected function merge(){
    	
    }

    protected function unmerge(){
    	
    }

    public function getList(){
    	$user = $this->getAuthService()->getIdentity();
    	if($user){
            return ProfileFacade::get($user);
        }
       	else{
           throw new \Exception("User is not logged in", \User\Module::ERROR_NOT_LOGGED_IN);
       }
    }

    protected function setCurrentUser($currentUser){
    	$this->currentUser = $currentUser;
    }

    protected function getCurrentUser(){
    	return $this->currentUser;
    }
}
