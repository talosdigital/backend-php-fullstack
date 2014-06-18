<?php
namespace IntegrationTests\Tests;

use IntegrationTests\AbstractTestCase;
use User\Entity\User\Oauth\Oauth;
use User\Entity\User\Oauth\Twitter;
use User\Entity\User;

use User\Service\UserService;

class TwitterTest extends AbstractTestCase {

	const ADAPTER = "twitter";

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

	public function createOauthTwitter(){

		$twitter = new Twitter();
		$twitter->setId($this::ID);
		$twitter->setPicture($this::PICTURE);
		
		return $twitter;
	}

	public function testCreateTwitter(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$user->getOauth()->add($this->createOauthTwitter());
		
		$this->userService->save($user);
		$nUser = $this->userService->findOneBy(array('email' => $this::EMAIL));

		$Oauth = $nUser->getOauth();

		$this->assertEquals($this::ID, $Oauth[0]->getId());
		$this->assertEquals($this::PICTURE, $Oauth[0]->getPicture());
	}

	public function testListTwitter(){
		$this->testCreateTwitter();
	}

	public function testModifyTwitter(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$user->getOauth()->add($this->createOauthTwitter());
		
		$this->userService->save($user);
		$nUser = $this->userService->findOneBy(array('email' => $this::EMAIL));

		$twitter = $nUser->getOauthAdapter($this::ADAPTER);

		$twitter->setId($this::EDIT_ID);
		$twitter->setPicture($this::EDIT_PICTURE);
		
		$this->userService->save($twitter);

		$twitter = $nUser->getOauthAdapter($this::ADAPTER);

		$this->assertEquals($this::EDIT_ID, $twitter->getId());
		$this->assertEquals($this::EDIT_PICTURE, $twitter->getPicture());
	}

	public function testDeleteTwitter(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$user->getOauth()->add($this->createOauthTwitter());
		
		$this->userService->save($user);

		$tw = $user->getOauthAdapter($this::ADAPTER);

		$user->getOauth()->removeElement($tw);
		$this->userService->save($user);

		$this->assertEquals(0, count($user->getOauth()));
	}
}