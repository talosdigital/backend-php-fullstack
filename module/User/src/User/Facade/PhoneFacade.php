<?php

namespace User\Facade;

use User\Entity\User\Phonenumber;

class PhoneFacade {

	protected $phonenumbers;

	function __construct($user){
		if(!$user){
			throw new \Exception("Empty user", \User\Module::EMPTY_USER);			
		}
		$this->phonenumbers = $user->getPhonenumbers();
	}
	
	public function getList() {
        $phonesArray = array();
        $i=0;
        foreach ($this->phonenumbers as $phonenumber) {
        	array_push($phonesArray, array(
        			'id' => $i,
        			'phonenumber' => $phonenumber->getPhonenumber()
        		));
        	$i++;
        }

        return $phonesArray;
	}
	
	public function get($phone) {
		if(! $phone) {
			$phone = new Phonenumber();
		}
        return array(
        	'phonenumber' => $phonenumber->getPhonenumber()
                );
	}

}
