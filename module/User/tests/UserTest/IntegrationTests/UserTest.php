<?php
namespace IntegrationTests\Tests;

use IntegrationTests\AbstractTestCase;
use DateTime;

use User\Entity\User;

use User\Service\UserService;

class UserTest extends AbstractTestCase {

	protected function alterConfig(array $config) {
		return $config;
	}

	public function setup() {
		parent::setup();

		$this->userService = new UserService($this->getServiceManager());
	}

	/*
	 * Create User
	 */
	public function testCreateArtist() {
		$data['email'] = 'mytest@test.com';
		$data['name'] = 'ignacio';
		
		$user = new User($data);

		//save
		$this->userService->save($user);
		
		$this->assertEquals($data['name'], $user->getName());
	}

}
