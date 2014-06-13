<?php

namespace User\Auth;

use User\Entity\User as User;
use User\Entity\User\Oauth;
use User\Entity\User\Oauth\Facebook as FacebookDocument;
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
	    $fbUser = $this->getUserByFacebook($request->get('facebookId'));
	    if((empty($user))&&(empty($fbUser))){
	        try{

		        $user = new User();
		        $user->setEmail($facebookUser->getEmail());
		        $user->setName($facebookUser->getFullName());

		        $user->setOauth(new Oauth());
		        $user->getOauth()->setFacebook($facebookUser);
		        
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
	    	
	    	if(empty($user)){
	    		$user = $fbUser;
	    	}
	    	
	    	$this->setCurrentUser($user);

	    	$result = $user->getOauth()->getFacebook();
	        if(empty($result)) { 
	            $this->merge($request);
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
        $user = $query->findOneBy(array('email' => $email));
        if(!empty($user))
            return $user;
        else
            return null;
    }

    private function getUserByFacebook($facebook_id){
    	$dm = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $query = $dm->getRepository($this->getDocument());
        $user = $query->findOneBy(array('oauth.facebook.id' => $facebook_id));
        if(!empty($user))
            return $user;
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
		
		$facebookDocument->setId($facebookArray['id']);
		$facebookDocument->setFullName($graph->getName());
		$facebookDocument->setEmail($facebookArray['email']);
		$facebookDocument->setPicture("https://graph.facebook.com/".$facebookArray['id']."/picture");

        return $facebookDocument;
    }

    public function merge($data){
    	$this->initialize();
    	$user = $this->getAuthService()->getIdentity();
    	
    	if(empty($user)){
    		$user = $this->getCurrentUser();
    	}

    	$facebookToken = $data->get('facebookToken');
    	$facebookUser = $this->getFacebookUser($facebookToken);

    	$fbUser = $this->getUserByFacebook($data->get('facebookId'));
    	
    	if($fbUser){
    		throw new \Exception("This facebook account is already merged", \User\Module::ERROR_FACEBOOK_ALREADY_MERGED);	
    	}

    	$oauth = $user->getOauth();
    	if(!$oauth){
    		$oauth = new Oauth();
    	}

    	$oauth->setFacebook($facebookUser);
        $user->setOauth($oauth);

        $this->getUserService()->getUserMapper()->update($user);
    }

    public function unmerge(){
    	$user = $this->getAuthPlugin()->getIdentity();
    	$user->getOauth()->removeFacebook();
    	$this->getUserService()->getUserMapper()->update($user);
    }

}