<?php

namespace Sales\Service;

use MyZend\Service\Service as Service;
use Salse\Document\Order;


class OrderService extends Service {

	protected $document = "Sales\Document\Order";

	public function __construct($sm) {
		$this->dm = $sm->get('doctrine.documentmanager.odm_default');

	}
}