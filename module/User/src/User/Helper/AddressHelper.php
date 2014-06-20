<?php

namespace User\Helper;

class AddressHelper {

	public function setAddressByRequest($address, $data){

    	if(!is_array($data)){
 		
    		if(!is_string($data->get('label'))){
    			throw new \Exception("Bad request", \User\Module::ERROR_BAD_REQUEST);
    			
    		}

    		$address->setLabel($data->get('label'));
	        $address->setCompanyName($data->get('companyName'));
	        $address->setFirstname($data->get('firstname'));
	        $address->setLastname($data->get('lastname'));
	        $address->setStreet($data->get('street'));
	        $address->setPostCode($data->get('postCode'));
	        $address->setCity($data->get('city'));
	        $address->setCity($data->get('state'));
	        $address->setCity($data->get('country'));
			$address->setGeolocation(null);
    	
    	}
    	else{

	    	$address->setLabel($data['label']);
	        $address->setCompanyName($data['companyName']);
	        $address->setFirstname($data['firstname']);
	        $address->setLastname($data['lastname']);
	        $address->setStreet($data['street']);
	        $address->setPostCode($data['postCode']);
	        $address->setCity($data->get('city'));
	        $address->setCity($data->get('state'));
	        $address->setCity($data->get('country'));
			$address->setGeolocation(null);
			
    	}

        return $address;
	}

}