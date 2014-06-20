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
use User\Helper\User as UserHelper;
use Zend\Stdlib\Parameters;
use Zend\Http\PhpEnvironment\Request;

class FacebookAdapter extends AbstractAdapter implements IAdapter {

	const TOKEN = '324410064378636';
	const SECRET = 'c4f122cd43915686cca6c7c4b1eaef6e'; 
	const ADAPTER = "facebook";
	private $full_name;
	private $userService;

	public function initialize(){
		FacebookSession::setDefaultApplication($this::TOKEN, $this::SECRET);
	}

	public function signup($request) {
		$serviceZfc = $this->getServiceLocator()->get('zfcuser_user_service');
		$service = $this->getUserService();
		
		$this->initialize();
		$facebookUser = $this->getFacebookUser($request->get('facebookToken'));
	    $user = $this->getUserByEmail($facebookUser->getEmail());
	    $fbUser = $this->getUserByFacebook($request->get('facebookId'));
	    
	    if((empty($user))&&(empty($fbUser))){
	        try{

		        $passwordTemp = $request->get('facebookId');

		        $post = array(
                "email" => $facebookUser->getEmail(),
                "password" => $passwordTemp,
                "passwordVerify" => $passwordTemp
				);

		        $user = new User();

				$form = $this->getRegisterForm();
		        $form->setHydrator($this->getFormHydrator());
		        $form->bind($user);

		        $form->setData($post);
				
				if(!$form->isValid()) {
					$errors = $form->getMessages();
					if(isset($errors['email']['recordFound'])) {
						throw new \Exception("This email is already taken, please login as a normal user and then merge your account with Facebook.", \User\Module::ERROR_DUPLICATED_EMAIL);	
					}
					else {
						throw new \Exception(json_encode($errors), \User\Module::ERROR_UNEXPECTED);	
					}
				}
				$user = $serviceZfc->register($post);
				
				if($user){
					$user->setName($this->full_name);
		        
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
				}

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

			$adapter = $this->getAuthPlugin()->getAuthAdapter();

			$params = new Parameters();
			$params->set('identity', $user->getEmail());
			$params->set('credential', $user->getOauthAdapter($this::ADAPTER)->getId());
			$emulateRequest = new Request();
			$emulateRequest->setPost($params);
			
	        $result = $adapter->prepareForAuthentication($emulateRequest);
	        if ($result instanceof Response) {
	            return $result;
	        }

	        $auth = $this->getAuthPlugin()->getAuthService()->authenticate($adapter);
	        if(! $auth->isValid()) {
	        	$result = $auth->getMessages();
				$message = "Bad request.";
				$errorCode = \User\Module::ERROR_UNEXPECTED;
				if(isset($result[0])) {
					$message = $result[0];
					$errorCode = \User\Module::ERROR_LOGIN_FAILED;
				}
	        	throw new \Exception($message, \User\Module::ERROR_LOGIN_FAILED);
	        }

	        $user = $this->getAuthPlugin()->getIdentity();

	        /*$this->getAuthPlugin()->getAuthAdapter()->resetAdapters();
	        $this->getAuthPlugin()->getAuthService()->clearIdentity();
	        $this->getAuthPlugin()->getAuthService()->getStorage()->write($user);*/

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
    	$user = $this->getAuthService()->getIdentity();
    	
    	if(empty($user)){
    		$user = $this->getCurrentUser();
    	}

    	if(empty($user->getPicture())){
    		$picture = new Picture();
	        $picture->setId(new \MongoId());
	        $picture->setUrl("https://graph.facebook.com/".$data->get('facebookId')."/picture?width=".\User\Helper\Picture::PICTURE_WIDTH);
	        $picture->setLongUrl("https://graph.facebook.com/".$data->get('facebookId')."/picture?width=".\User\Helper\Picture::PICTURE_WIDTH);
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

    	$user = $this->getAuthPlugin()->getIdentity();
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
    }

    private function getRegisterForm() {
		return $this->getServiceLocator()->get('zfcuser_register_form');
    }

    private function getFormHydrator(){
		return ($this->getServiceLocator()->get('zfcuser_register_form_hydrator'));
    }
	
	private function getLoginForm() {
		return $this->getServiceLocator()->get('zfcuser_login_form');
	}
}