<?php

namespace User\Auth;

use User\Entity\User as User;
use User\Entity\User\Facebook as FacebookDocument;
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

	const TOKEN = '324410064378636';
	const SECRET = 'c4f122cd43915686cca6c7c4b1eaef6e';

	public function initialize(){
		FacebookSession::setDefaultApplication($this::TOKEN, $this::SECRET);
	}

	public function signup($request) {
		$service = $this->getUserService();
		$this->initialize();
		$facebookUser = $this->getFacebookUser($request->get('facebookToken'));
	    $user = $this->getUserByEmail($facebookUser->getEmail());

	    if(empty($user)){
	        try{

		        $user = new User();
		        $user->setEmail($facebookUser->getEmail());
		        $user->setName($facebookUser->getUsername());

		        $user->setFacebook($facebookUser);
		        
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
	    	$result = $user->getFacebook();
	        if(empty($result)) { 
	            $this->mergeFacebook($user, $request->get('facebookToken'));
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
        $facebookDocument = new FacebookDocument();
        
        $request = new FacebookRequest($session, 'GET', '/me');
        $response = $request->execute();
        $graph = $response->getGraphObject(GraphUser::className());
		$facebookArray = $graph->asArray();
		
		$facebookDocument->setFacebookId($facebookArray['id']);
		$facebookDocument->setUsername($graph->getName());
		$facebookDocument->setEmail($facebookArray['email']);
		$facebookDocument->setPicture("https://graph.facebook.com/".$facebookArray['id']."/picture");

        return $facebookDocument;
    }

    public function merge($data){
    	$this->initialize();
    	$user = $this->getAuthPlugin()->getIdentity();
    	$facebookToken = $data->get('facebookToken');
    	$facebookUser = $this->getFacebookUser($facebookToken);

        $user->setFacebook($facebookUser);
        $this->getUserService()->getUserMapper()->update($user);
    }

    public function unmerge(){
    	$user = $this->getAuthPlugin()->getIdentity();
    	$user->setFacebook(new FacebookDocument());
    	$this->getUserService()->getUserMapper()->update($user);
    }

}