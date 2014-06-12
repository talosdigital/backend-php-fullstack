<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

use User\Entity\User;
use User\Facade\AddressFacade; 

/**
 *
 * @SWG\Model(id="profile")
 */
class AddressController extends AbstractRestfulController
{
	public function indexAction() {
        return new JsonModel();
    }
	
 	public function getList() {
		$userService = $this->getServiceLocator()->get('userService');
 		$user = $userService->findOneBy(array("_id" => "5398758c724f9aa721d63af1"));
		$facade = new AddressFacade($user);
		
		return new JsonModel($facade->getList($user->getAddresses())); 		
    }	
 	
	public function get($id) {
		$userService = $this->getServiceLocator()->get('userService');
 		$user = $userService->findOneBy(array("_id" => "5398758c724f9aa721d63af1"));
		$facade = new AddressFacade($user);
		$address = $user->getAddresses()->get($id);
		
		return new JsonModel($facade->get($address)); 		
	}
	
	public function create($data) {
        die("create");
    }

	public function update($id, $data) {
        die("update");
    }

	public function delete($id) {
		die("delete");
	}
	
}