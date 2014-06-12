<?php

namespace User\Facade;

use User\Entity\User\Address;

class AddressFacade {
	
	public function getList($address) {
        return array(
            array('name' => 'test'),
            array('name' => 'second')
        );
	}
	
	public function get($address) {
		if(! $address) {
			$address = new Address();
		}
        return array(
        	'street' => $address->getStreet(),
        	'post_code' => $address->getPostCode()
        );
	}

}
