<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class ErrorController extends AbstractRestfulController
{
    public function indexAction()
    {	
    	return new JsonModel(array('message' => 'hi'));
    }

}