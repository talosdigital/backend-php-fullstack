<?php

namespace User\Service;

use MyZend\Service\Service as Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;  
use Zend\ServiceManager\ServiceLocatorInterface;

class UserService extends Service implements ServiceLocatorAwareInterface
{
	protected $document = "User\Entity\User";
    protected $services;
	
	public function __construct($serviceLocator) {
        $this->setServiceLocator($serviceLocator);
	}
	
 	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
		$this->dm = $serviceLocator->get('doctrine.documentmanager.odm_default');
    }

    public function getServiceLocator()
    {
        return $this->services;
    }	
	
}
