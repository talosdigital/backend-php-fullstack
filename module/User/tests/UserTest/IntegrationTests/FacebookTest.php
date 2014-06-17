<?php
namespace IntegrationTests\Tests;

use IntegrationTests\AbstractTestCase;
use User\Entity\User\Oauth\Oauth;
use User\Entity\User\Oauth\Facebook;
use User\Entity\User;

use User\Service\UserService;

class FacebookTest extends AbstractTestCase {

	const ADAPTER = "facebook";

	const ID = '1234567';
	const EMAIL = 'mytest@test.com';
	const FULL_NAME = 'test javier';
	const PICTURE = "text.jpg";

	const EDIT_ID = '987654';
	const EDIT_EMAIL = 'mytest@test.com';
	const EDIT_FULL_NAME = 'test javier';
	const EDIT_PICTURE = "text.jpg";
	
	protected function alterConfig(array $config) {
		return $config;
	}

	public function setup() {
		parent::setup();

		$this->userService = new UserService($this->getServiceManager());
	}

	public function createOauthFacebook(){

		$facebook = new Facebook();
		$facebook->setId($this::ID);
		$facebook->setEmail($this::EMAIL);
		$facebook->setPicture($this::PICTURE);
		
		return $facebook;
	}

	public function testCreateFacebook(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$user->getOauth()->add($this->createOauthFacebook());
		
		$this->userService->save($user);
		$nUser = $this->userService->findOneBy(array('email' => $this::EMAIL));

		$Oauth = $nUser->getOauth();

		$this->assertEquals($this::EMAIL, $Oauth[0]->getEmail());
		$this->assertEquals($this::ID, $Oauth[0]->getId());
		$this->assertEquals($this::PICTURE, $Oauth[0]->getPicture());
	}

	public function testListFacebook(){
		$this->testCreateFacebook();
	}

	public function testModifyFacebook(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$user->getOauth()->add($this->createOauthFacebook());
		
		$this->userService->save($user);
		$nUser = $this->userService->findOneBy(array('email' => $this::EMAIL));

		$facebook = $nUser->getOauthAdapter($this::ADAPTER);

		$facebook->setId($this::EDIT_ID);
		$facebook->setEmail($this::EDIT_EMAIL);
		$facebook->setPicture($this::EDIT_PICTURE);
		
		$this->userService->save($facebook);

		$facebook = $nUser->getOauthAdapter($this::ADAPTER);

		$this->assertEquals($this::EDIT_EMAIL, $facebook->getEmail());
		$this->assertEquals($this::EDIT_ID, $facebook->getId());
		$this->assertEquals($this::EDIT_PICTURE, $facebook->getPicture());
	}

	public function testDeleteFacebook(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$user->getOauth()->add($this->createOauthFacebook());
		
		$this->userService->save($user);

		$fb = $user->getOauthAdapter($this::ADAPTER);

		$user->getOauth()->removeElement($fb);
		$this->userService->save($user);

		$this->assertEquals(0, count($user->getOauth()));
	}
}