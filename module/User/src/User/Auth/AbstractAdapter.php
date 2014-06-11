<?php

namespace User\Auth;

class AbstractAdapter {
		
	private $serviceLocator;

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
	
}
