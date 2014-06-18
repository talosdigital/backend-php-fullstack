<?php

namespace User\Auth;

use User\Entity\User as User;
use User\Entity\User\Oauth\Oauth;
use User\Entity\User\Oauth\Twitter as TwitterDocument;
use User\Entity\User\Picture;
use User\Entity\User\Role;
use ZendService\Twitter\Twitter;
use ZendOAuth\OAuth as OAuthService;

class TwitterAdapter extends AbstractAdapter implements IAdapter {

	const CONSUMER_KEY = '3MkJXuXe8Vk1q9dhThVDObFme';
	const CONSUMER_SECRET = 'bvldg5Mrv2aj4pHWNnyGPrXcepVtr78pkEmw2tAK2EUF4NxYZY'; 
	const ADAPTER = "twitter";
	
	private $full_name;
	private $config;

	public function initialize($request){
		$connection = curl_init("https://api.twitter.com");
		curl_setopt($connection, CURLOPT_CAINFO, dirname(__DIR__)."/Certificates/cacert.pem");

		$config = array(
		    'access_token' => array(
		        'token'  => $request->get('twitterToken'),
		        'secret' => $request->get('twitterSecret'),
		        'username' => $request->get('twitterName')
		    ),
		    'oauth_options' => array(
		        'consumerKey' => $this::CONSUMER_KEY,
		        'consumerSecret' => $this::CONSUMER_SECRET,
		    ),
		    'http_client_options' => array(
		        'adapter' => 'Zend\Http\Client\Adapter\Curl'
		        )
		);

		$twitter = new Twitter($config);

		return $twitter;
	}

	public function signup($request) {
		throw new \Exception("Not supported function", \User\Module::ERROR_NOT_SUPPORTED_FUNCTION);

	}

	public function login($request) {
		$twitter = $this->initialize($request);      

        $validate = $twitter->account->verifyCredentials();
        if (!$validate->isSuccess()) {
		    throw new \Exception("Bad request on Twitter API", \User\Module::ERROR_TWITTER_API_BAD_REQUEST);
		}

		$response = $twitter->users->show($request->get('twitterName'));
		$user = $this->getUserByTwitter($response->id_str);

		$this->getAuthPlugin()->getAuthAdapter()->resetAdapters();
        $this->getAuthPlugin()->getAuthService()->clearIdentity();
        $this->getAuthPlugin()->getAuthService()->getStorage()->write($user);

        return $user;
	}
	
	public function logout() {
		parent::logout();
	}

    private function getUserByTwitter($twitterId){
    	$dm = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        $query = $dm->getRepository($this->getDocument());
   
        $user = $query->findOneBy(array('oauth.id' => $twitterId, 'oauth.adapter' => $this::ADAPTER));
        if(!empty($user))
            return $user;
        else
            return null;
    }

    private function getTwitterUser($twitterResponse){ 
        $twitterDocument = new TwitterDocument();
		
		$twitterDocument->setAdapter($this::ADAPTER);
		$twitterDocument->setId($twitterResponse->id);
		$twitterDocument->setEmail(null);
		$twitterDocument->setPicture($twitterResponse->profile_image_url);
		$this->full_name = $twitterResponse->name;

        return $twitterDocument;
    }

    public function merge($request){

    	$twitter = $this->initialize($request);      

        $validate = $twitter->account->verifyCredentials();
        if (!$validate->isSuccess()) {
		    throw new \Exception("Bad request on Twitter API", \User\Module::ERROR_TWITTER_API_BAD_REQUEST);
		}

		$response = $twitter->users->show($request->get('twitterName'));
        $twitterDocument = $this->getTwitterUser($response);

        $user = $this->getAuthService()->getIdentity();
    	
    	if(empty($user)){
    		$user = $this->getCurrentUser();
    	}

    	if(empty($user->getPicture())){
    		$picture = new Picture();
	        $picture->setId(new \MongoId());
	        $picture->setUrl($twitterDocument->getPicture());
	        $picture->setLongUrl($twitterDocument->getPicture());
    	}

    	$twUser = $this->getUserByTwitter($request->get('twitterId'));
    	
    	if($twUser){
    		throw new \Exception("This twitter account is already merged", \User\Module::ERROR_TWITTER_ALREADY_MERGED);	
    	}

    	$user->getOauth()->add($twitterDocument);
        $this->getUserService()->getUserMapper()->update($user);
    }

    public function unmerge($data){

    	$user = $this->getAuthService()->getIdentity();
    	$twitter = $user->getOauthAdapter($this::ADAPTER);
    	$user->getOauth()->removeElement($twitter);

    	$this->getUserService()->getUserMapper()->update($user);
    }
}