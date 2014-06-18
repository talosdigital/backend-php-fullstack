<?php

namespace User\Facade;

class PictureFacade {

	public function get($user) {
		
		if(!$user){
			throw new \Exception("Empty user", \User\Module::ERROR_EMPTY_USER);			
		}
		
		$picture = $user->getPicture();
		if(empty($picture)){
			return null;
		}

       	$response = array(
       		"url" => $picture->getUrl(),
       		"longUrl" => $picture->getLongUrl(),
			"size" => $picture->getSize(),
			"type" => $picture->getType()
       		);

        return $response;
	}

}
