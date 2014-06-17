<?php

namespace User\Facade;

use User\Entity\User;

class OauthFacade {

	public function get($Oauth) {
        $response = array();
        foreach ($Oauth as $oauth) {
            array_push($response, array(
                'label' => $oauth->getLabel(),
                'email' => $oauth->getEmail(),
                'picture' => $oauth->getPicture()
                ));            
        }
        return $response;
	}

}
