<?php

namespace User\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Application\Entity\Response;
use User\Entity\User;
use Zend\View\Model\JsonModel;

class ProfileController extends AbstractRestfulController
{

	public function indexAction() {
		
		return new JsonModel(array("message" => "This is your dashboard."));
	}
}