<?php

namespace User\Facade;

use User\Entity\User;

class OauthFacade {
	
	private function getFacebook($Oauth){
		if(empty($Oauth->getFacebook())){
        	$facebook = null;
        }
        else{
        	$facebook = array(
        		'id' => $Oauth->getFacebook()->getId(),
        		'full_name' => $Oauth->getFacebook()->getFullName(),
        		'email' => $Oauth->getFacebook()->getEmail(),
        		'picture' => $Oauth->getFacebook()->getPicture()
        		);
        }
        return $facebook;
	}

	private function getTwitter($Oauth){
		if(empty($Oauth->getTwitter())){
        	$twitter = null;
        }
        else{
        	$twitter = array(
        		'id' => $Oauth->getTwitter()->getId(),
        		'full_name' => $Oauth->getTwitter()->getFullName(),
        		'email' => $Oauth->getTwitter()->getEmail(),
        		'picture' => $Oauth->getTwitter()->getPicture()
        		);
        }
        return $twitter;
	}

	private function getGoogle($Oauth){
		if(empty($Oauth->getGoogle())){
        	$google = null;
        }
        else{
        	$google = array(
        		'id' => $Oauth->getGoogle()->getId(),
        		'full_name' => $Oauth->getGoogle()->getFullName(),
        		'email' => $Oauth->getGoogle()->getEmail(),
        		'picture' => $Oauth->getGoogle()->getPicture()
        		);
        }
        return $google;
	}

	public function get($Oauth) {
        if($Oauth){
            return array(
            	'facebook' => $this->getFacebook($Oauth),
            	'twitter' => $this->getTwitter($Oauth),
            	'google' => $this->getGoogle($Oauth)
               	);
        }
        else{
            return null;
        }
	}

}
