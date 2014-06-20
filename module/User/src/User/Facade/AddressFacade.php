<?php

namespace User\Facade;

use User\Entity\User\Address;

class AddressFacade {

	protected $addresses;
	
	public function getList($user) {
        $addresses = $user->getAddresses();
        if(count($addresses)==0){
            return null;
        }

        $addressesArray = array();
        $i=0;
        foreach ($addresses as $address) {
        	array_push($addressesArray, array(
        			'id' => $i,
        			'label' => $address->getLabel(),
        			'companyName' => $address->getCompanyName(),
        			'fullName' => $address->getFirstname()." ".$address->getLastname(),
        			'street' => $address->getStreet(),
        			'postCode' => $address->getPostCode(),
        			'geolocation' => $address->getGeolocation(),
                    'city' => $address->getCity(),
                    'state' => $address->getState(),
                    'country' => $address->getCountry()
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
