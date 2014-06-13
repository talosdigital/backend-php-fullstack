<?php
namespace IntegrationTests\Tests;

use IntegrationTests\AbstractTestCase;
use User\Entity\User\Oauth;
use User\Entity\User\Oauth\Facebook;
use User\Entity\User;

use User\Service\UserService;

class FacebookTest extends AbstractTestCase {

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
		$Oauth = new Oauth();
		$facebook = new Facebook();
		$facebook->setId($this::ID);
		$facebook->setFullName($this::FULL_NAME);
		$facebook->setEmail($this::EMAIL);
		$facebook->setPicture($this::PICTURE);
		$Oauth->setFacebook($facebook);
		return $Oauth;
	}

	public function testCreateFacebook(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$user->setOauth($this->createOauthFacebook());
		
		$this->userService->save($user);
		$nUser = $this->userService->findOneBy(array('email' => $this::EMAIL));

		$this->assertEquals($this::EMAIL, $nUser->getOauth()->getFacebook()->getEmail());
		$this->assertEquals($this::ID, $nUser->getOauth()->getFacebook()->getId());
		$this->assertEquals($this::FULL_NAME, $nUser->getOauth()->getFacebook()->getFullName());
		$this->assertEquals($this::PICTURE, $nUser->getOauth()->getFacebook()->getPicture());
	}

	public function testListFacebook(){
		$this->testCreateFacebook();
	}

	public function testModifyFacebook(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$user->setOauth($this->createOauthFacebook());
		
		$this->userService->save($user);
		$nUser = $this->userService->findOneBy(array('email' => $this::EMAIL));

		$nUser->getOauth()->getFacebook()->setId($this::EDIT_ID);
		$nUser->getOauth()->getFacebook()->setEmail($this::EDIT_EMAIL);
		$nUser->getOauth()->getFacebook()->setFullName($this::EDIT_FULL_NAME);
		$nUser->getOauth()->getFacebook()->setPicture($this::EDIT_PICTURE);

		$this->userService->save($nUser);

		$this->assertEquals($this::EDIT_EMAIL, $nUser->getOauth()->getFacebook()->getEmail());
		$this->assertEquals($this::EDIT_ID, $nUser->getOauth()->getFacebook()->getId());
		$this->assertEquals($this::EDIT_FULL_NAME, $nUser->getOauth()->getFacebook()->getFullName());
		$this->assertEquals($this::EDIT_PICTURE, $nUser->getOauth()->getFacebook()->getPicture());
	}

	public function testDeleteFacebook(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$user->setOauth($this->createOauthFacebook());
		
		$this->userService->save($user);

		$user->getOauth()->removeFacebook();
		$this->userService->save($user);

		$facebook  = $user->getOauth()->getFacebook();
		$this->assertEquals(null, $facebook);
	}
}