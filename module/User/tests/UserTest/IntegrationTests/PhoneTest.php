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

	public function testCreatePhone(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));

		$data = array();
		$data['phonenumber'] = $this::PHONE;
		$phonenumber = new Phonenumber($data);	
		$phonenumbers[0] = $phonenumber;

		$user->setPhonenumbers($phonenumbers);
		$this->userService->save($user);
		$phonenumbers = $user->getPhonenumbers();

		$this->assertCount(1, $phonenumbers);
		$this->assertEquals($data['phonenumber'], $phonenumbers[0]->getPhonenumber());
	}

	public function testCreateTwoOrMorePhones(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));

		$data = array();
		$data['phonenumber'] = $this::PHONE;
		$phonenumber = new Phonenumber($data);
		
		for ($i=0; $i < $this::PHONE_QUANTITY; $i++) { 
			$phonenumbers[$i] = $phonenumber;
		}
		
		$user->setPhonenumbers($phonenumbers);
		$this->userService->save($user);
		$phonenumbers = $user->getPhonenumbers();

		$this->assertCount($this::PHONE_QUANTITY, $phonenumbers);
	}

	public function testListPhones(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL_WN_EMPTY_ADDRESSES));
		$phonenumbers = $user->getPhonenumbers();
		$total = count($phonenumbers);

		$this->assertCount(1, $phonenumbers);
		$this->assertEquals($this::ACTUAL_PHONE, $phonenumbers[0]->getPhonenumber());
	}

	public function testModifyPhone(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL_WN_EMPTY_ADDRESSES));
		$data = array();
		$data['phonenumber'] = $this::PHONE;
		$phonenumber = new Phonenumber($data);	

		$phonenumbers[0] = $phonenumber;
		
		$user->setPhonenumbers($phonenumbers);
		$this->userService->save($user);
		$phonenumbers = $user->getPhonenumbers();

		$this->assertEquals($this::PHONE, $phonenumbers[0]->getPhonenumber());
	}

	public function testDeletePhone(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL_WN_EMPTY_ADDRESSES));
		$user->setPhonenumbers(array());
		$this->userService->save($user);
		$phonenumbers  = count($user->getPhonenumbers());
		$this->assertEquals(0, $phonenumbers);
	}

}