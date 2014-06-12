<?php
namespace IntegrationTests\Tests;

use IntegrationTests\AbstractTestCase;
use DateTime;

use User\Entity\User;

use User\Service\UserService;

class UserTest extends AbstractTestCase {

	const EMAIL = 'mytest@test.com';
	const NAME = 'javier test';

	const NEW_EMAIL = "testing@test.com";
	const NEW_NAME = 'javier test';

	protected function alterConfig(array $config) {
		return $config;
	}

	public function setup() {
		parent::setup();

		$this->userService = new UserService($this->getServiceManager());
	}


	public function testCreateUser() {
		$data = array();
		$data['email'] = $this::EMAIL;
		$data['name'] = $this::NAME;
		
		$user = new User($data);

		$total = count($this->userService->findAll());

		$this->userService->save($user);
		
		$this->assertEquals($data['name'], $user->getName());
		$this->assertEquals($total + 1, count($this->userService->findAll()));
	}

	public function testModifyUser(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$user->setEmail($this::NEW_EMAIL);
		$user->setName($this::NEW_NAME);

		$this->userService->save($user);
		
		$this->assertEquals($this::NEW_NAME, $user->getName());
		$this->assertEquals($this::NEW_EMAIL, $user->getEmail());
	}

	public function testDeleteUser(){
		$total = count($this->userService->findAll());
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));

		$this->userService->remove($user);

		$this->assertEquals($total-1, count($this->userService->findAll()));
		$this->assertNull($this->userService->findOneBy(array('email' => $this::EMAIL)));

	}

	public function testUpdateEmail(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$user->setEmail($this::NEW_EMAIL);

		$this->userService->save($user);

		$total = count($this->userService->findOneBy(array('email' => $this::NEW_EMAIL)));
		$this->assertEquals($total, 1);
	}

}
