<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Application\Entity\Response;
use User\Entity\User;
use Zend\View\Model\JsonModel;

class AuthController extends AbstractRestfulController
{

	private function loadAdapter() {
		$data = $this->getRequest()->getPost();
		
		if($data->get('adapter') == 'facebook') {
			$adapter = new \User\Auth\FacebookAdapter($this->getServiceLocator());
		}
		else {
			$adapter = new \User\Auth\EmailAdapter($this->getServiceLocator());
		}
		
		
		return $adapter;
	}

	public function signupAction() {
		$data = $this->getRequest()->getPost();
		$adapter = $this->loadAdapter();

		$user = $adapter->signup($data);
		return new JsonModel(array("message" => "User was created."));	
	}
	
	public function loginAction() {
		$data = $this->getRequest()->getPost();
		$adapter = $this->loadAdapter();

		$user = $adapter->login($data);
		return new JsonModel(array("message" => "Welcome back."));
	}
	
	public function logoutAction() {
		$adapter = $this->loadAdapter();

		$user = $adapter->logout();
		return new JsonModel(array("message" => "Logout completed."));
	}
}