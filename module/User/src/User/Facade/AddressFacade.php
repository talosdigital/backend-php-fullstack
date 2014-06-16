<?php

namespace User\Facade;

use User\Entity\User\Address;

class AddressFacade {

	protected $addresses;

	function __construct($user){
		if(!$user){
			throw new \Exception("Empty user", \User\Module::EMPTY_USER);			
		}
		$this->addresses = $user->getAddresses();
	}
	
	public function getList() {
        $addressesArray = array();
        $i=0;
        foreach ($this->addresses as $address) {
        	array_push($addressesArray, array(
        			'id' => $i,
        			'label' => $address->getLabel(),
        			'companyName' => $address->getCompanyName(),
        			'fullName' => $address->getFirstname()." ".$address->getLastname(),
        			'street' => $address->getStreet(),
        			'postCode' => $address->getPostCode(),
        			'geolocation' => $address->getGeolocation()
        		));
        	$i++;
        }

        return $addressesArray;
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
