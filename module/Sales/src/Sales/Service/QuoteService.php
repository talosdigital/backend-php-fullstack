<?php

namespace Sales\Service;

use MyZend\Service\Service as Service;
use Salse\Document\Quote;

class QuoteService extends Service {

	protected $document = "Sales\Document\Quote";

	public function __construct($sm) {
		$this->dm = $sm->get('doctrine.documentmanager.odm_default');
	}
}