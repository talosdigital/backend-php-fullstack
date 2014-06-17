<?php

namespace User\Facade;

class PictureFacade {

	public function get($user) {
		
		if(!$user){
			throw new \Exception("Empty user", \User\Module::ERROR_EMPTY_USER);			
		}
		
		$picture = $user->getPicture();
		if(empty($picture)){
			throw new \Exception("User without picture", \User\Module::ERROR_USER_WITHOUT_PICTURE);
			
		}

       	$response = array(
       		"url" => $picture->getUrl(),
       		"longUrl" => $picture->getLongUrl(),
			"width" => $picture->getWidth(),
			"height" => $picture->getHeight(),
			"type" => $picture->getType()
       		);

        return $response;
	}

}
