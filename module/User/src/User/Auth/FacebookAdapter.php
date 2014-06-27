<?php

namespace User\Auth;

use User\Entity\User as User;
use User\Entity\User\Oauth\Oauth;
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
use User\Entity\User\Picture;
use User\Entity\User\Role;

class FacebookAdapter extends AbstractAdapter implements IAdapter {

	const ADAPTER = "facebook";
	private $full_name;
	private $userService;

	public function initialize(){
		$config = $this->getServiceLocator()->get("Config");
		$token = $config['Facebook']['appId'];
		$secret = $config['Facebook']['secret'];

		FacebookSession::setDefaultApplication($token, $secret);
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
		        $user->setName($this->full_name);
		        $user->setPassword(null);
		        
		        $picture = new Picture();
		        $picture->setId(new \MongoId());
		        $picture->setUrl("https://graph.facebook.com/".$request->get('facebookId')."/picture?width=".\User\Helper\Picture::PICTURE_WIDTH);
		        $picture->setLongUrl("https://graph.facebook.com/".$request->get('facebookId')."/picture?width=".\User\Helper\Picture::PICTURE_WIDTH);

		        $user->setPicture($picture);
		        $user->getOauth()->add($facebookUser);
		        
		        $role = new Role();
            	$role->setRoleId('user');

		        $user->setRoles($role);
		        $service->save($user);

		        $this->getAuthPlugin()->getAuthAdapter()->resetAdapters();
		        $this->getAuthPlugin()->getAuthService()->clearIdentity();
		        $this->getAuthPlugin()->getAuthService()->getStorage()->write($user);
				
		        return $user;
		    }
		    catch (Exception $ex){
		    	throw new \Exception("Error Processing Facebook API", \User\Module::ERROR_FACEBOOK_REGISTER_FAILED);
		    }
	    }
	    else{
	    	
	    	if(empty($user)){
	    		$user = $fbUser;
	    	}
	    	
	    	$result = $user->getOauthAdapter($this::ADAPTER);
	        
	        if(empty($result)) { 
	            $this->merge($request);
			}
			
	        $this->getAuthPlugin()->getAuthAdapter()->resetAdapters();
	        $this->getAuthPlugin()->getAuthService()->clearIdentity();
	        $this->getAuthPlugin()->getAuthService()->getStorage()->write($user);
	        
	        return $user;
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

    private function getUserByFacebook($facebookId){
    	$dm = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $query = $dm->getRepository($this->getDocument());
        $user = $query->findOneBy(array('oauth.id' => $facebookId, 'oauth.adapter' => 'facebook'));
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
		$facebookDocument->setEmail($facebookArray['email']);
		$facebookDocument->setPicture("https://graph.facebook.com/".$facebookArray['id']."/picture?width=".\User\Helper\Picture::PICTURE_WIDTH);
		$this->full_name = $graph->getName();
        return $facebookDocument;
    }

    public function merge($data){
    	$service = $this->getUserService();
    	$this->initialize();
    	$user = $this->getCurrentUser();


    	if($user->getPicture()==''){
    		$picture = new Picture();
	        $picture->setId(new \MongoId());
	        $picture->setUrl("https://graph.facebook.com/".$data->get('facebookId')."/picture?width=".\User\Helper\Picture::PICTURE_WIDTH);
	        $picture->setLongUrl("https://graph.facebook.com/".$data->get('facebookId')."/picture?width=".\User\Helper\Picture::PICTURE_WIDTH);

	        $user->setPicture($picture);
    	}

    	$facebookToken = $data->get('facebookToken');
    	$facebookUser = $this->getFacebookUser($facebookToken);

    	$fbUser = $this->getUserByFacebook($data->get('facebookId'));
    	
    	if($fbUser){
    		throw new \Exception("This facebook account is already merged", \User\Module::ERROR_FACEBOOK_ALREADY_MERGED);	
    	}

    	$user->getOauth()->add($facebookUser);
        $service->save($user);
    }

    public function unmerge($data){
    	$service = $this->getUserService();
    	$this->initialize();

    	$user = $this->getCurrentUser();

    	$fb = $user->getOauthAdapter($this::ADAPTER);
    	$user->getOauth()->removeElement($fb);

    	if(sizeof($user->getOauth())==0){
    		$user->resetOauth();
    	}

    	$service->save($user);    	
    	$token = $data->get('facebookToken');


    	$session = new FacebookSession($token);
    	$request = new FacebookRequest($session, 'DELETE', '/me/permissions');
    	$response = $request->execute();

    	$this->logout();
    }
}