<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class ErrorController extends AbstractActionController
{
    public function indexAction()
    {	
    	return array('message' => 'hi');
    }

}