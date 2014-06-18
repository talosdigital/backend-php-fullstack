<?php

namespace User\Facade;

use User\Entity\User;

class OauthFacade {

	public function get($user) {
        $Oauth = $user->getOauth();
        $response = array();

        if(empty($Oauth)){
            return null;
        }

        foreach ($Oauth as $oauth) {
            array_push($response, array(
                'adapter' => $oauth->getAdapter(),
                'id' => $oauth->getId(),
                'email' => $oauth->getEmail(),
                'picture' => $oauth->getPicture()
                ));            
        }
        return $response;
	}

}
