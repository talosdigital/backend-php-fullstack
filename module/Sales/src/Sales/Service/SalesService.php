<?php

namespace Sales\Service;

use MyZend\Service\Service as Service;

class SalesService extends Service {
	
	protected $document = "Sales\Document\Sales";
	
	public function __construct($sm) {
		$this->dm = $sm->get('doctrine.documentmanager.odm_default');
		
	}
}
