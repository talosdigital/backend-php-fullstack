<?php
namespace IntegrationTests\Tests;

use IntegrationTests\AbstractTestCase;
use User\Entity\User\Phonenumber;
use User\Entity\User;

use User\Service\UserService;

class PhoneTest extends AbstractTestCase {

	const EMAIL = 'mytest@test.com';
	const ACTUAL_PHONE = '1234567';
	const PHONE_QUANTITY = 4;
	const EMAIL_WN_EMPTY_ADDRESSES = "mytest@notempty.com";
	const PHONE = "987654";

	protected function alterConfig(array $config) {
		return $config;
	}

	public function setup() {
		parent::setup();

		$this->userService = new UserService($this->getServiceManager());
	}

	public function createPhone(){
		$data = array();
		$data['phonenumber'] = $this::PHONE;
		$phonenumber = new Phonenumber($data);	
		return $phonenumber;
	}

	public function testCreatePhone(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$user->getPhonenumbers()->add($this->createPhone());
		$this->userService->save($user);

		$phonenumbers = $user->getPhonenumbers();

		$this->assertCount(1, $phonenumbers);
		$this->assertEquals($this::PHONE, $phonenumbers->get(0)->getPhonenumber());
	}

	public function testCreateTwoOrMorePhones(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		for ($i=0; $i < $this::PHONE_QUANTITY; $i++) { 
			$user->getPhonenumbers()->add($this->createPhone());
		}
		
		$this->userService->save($user);
		$phonenumbers = $user->getPhonenumbers();

		$this->assertCount($this::PHONE_QUANTITY, $phonenumbers);
	}

	public function testListPhones(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL_WN_EMPTY_ADDRESSES));
		$phonenumbers = $user->getPhonenumbers();
		$total = count($phonenumbers);

		$this->assertCount(1, $phonenumbers);
		$this->assertEquals($this::ACTUAL_PHONE, $phonenumbers->get(0)->getPhonenumber());
	}

	public function testModifyPhone(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL_WN_EMPTY_ADDRESSES));
		$user->getPhonenumbers()->get(0)->setPhonenumber($this::PHONE);

		$this->userService->save($user);
		$phonenumbers = $user->getPhonenumbers();

		$this->assertEquals($this::PHONE, $phonenumbers->get(0)->getPhonenumber());
	}

	public function testDeletePhone(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL_WN_EMPTY_ADDRESSES));
		$phonenumber = $user->getPhonenumbers()->get(0);
		$user->getPhonenumbers()->removeElement($phonenumber);
		
		$this->userService->save($user);
		$phonenumbers  = count($user->getPhonenumbers());
		$this->assertEquals(0, $phonenumbers);
	}

}