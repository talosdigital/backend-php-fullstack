<?php

namespace User\Auth;

use User\Entity\User as User;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;

class FacebookAdapter extends AbstractAdapter implements IAdapter {

	public function signup($request) {
		$service = $this->getUserService();
		FacebookSession::setDefaultApplication('324410064378636', 'c4f122cd43915686cca6c7c4b1eaef6e');
		$facebookUser = $this->getFacebookUser($request->get('facebookToken'));
	    $user = $this->getUserByEmail($facebookUser['email']);
	    if(empty($user)){
	        try{
		        $user = new User();
		        $user->setEmail($facebookUser['email']);
		        $user->setName($facebookUser['name']);
		        $user->setFacebookId($request->get('facebookId'));
		        $user->setPassword(null);
		        $user->setRole('user');
		        $service->getUserMapper()->insert($user);
		       
		        $this->getAuthPlugin()->getAuthAdapter()->resetAdapters();
		        $this->getAuthPlugin()->getAuthService()->clearIdentity();
		        $this->getAuthPlugin()->getAuthService()->getStorage()->write($user);

		        return true;
		    }
		    catch (Exception $ex){
		    	throw new \Exception("Error Processing Facebook API", \User\Module::ERROR_FACEBOOK_REGISTER_FAILED);
		    }
	    }
	    else{
	    	$result = $user->getFacebookId();
	        if(empty($result)) { 
	            $this->mergeFacebook($user, $request->get('facebookId'));
			}
	        $this->getAuthPlugin()->getAuthAdapter()->resetAdapters();
	        $this->getAuthPlugin()->getAuthService()->clearIdentity();
	        $this->getAuthPlugin()->getAuthService()->getStorage()->write($user);
	        
	        return true;
	   }
	}
	
	public function login($request) {
		$this->signup($request);
	}
	
	public function logout() {
		parent::logout();
	}

	private function getUserByEmail($email){
        $dm = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $query = $dm->getRepository($this->getDocument());
        $userDocument = $query->findBy(array('email' => $email));
        if(!empty($userDocument))
            return $userDocument[0];
        else
            return null;
    }

    private function getFacebookUser($token){
        $session = new FacebookSession($token);
        $request = new FacebookRequest($session, 'GET', '/me');
        $response = $request->execute();
        $graph = $response->getGraphObject(GraphUser::className());
		$facebookArray = $graph->asArray();
        $userRequest = array(
        	'name' => $graph->getName(), 
        	'email' => $facebookArray['email'], 
        	'facebook_id' => $token
        	);
        return $userRequest;
    }

    private function mergeFacebook($user, $facebookId){
        $user->setFacebookId($facebookId);
        $this->getUserService()->getUserMapper()->update($user);
    }

}
