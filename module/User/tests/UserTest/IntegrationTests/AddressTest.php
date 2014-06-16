<?php
namespace IntegrationTests\Tests;

use IntegrationTests\AbstractTestCase;
use User\Entity\User\Address;
use User\Entity\User;

use User\Service\UserService;

class AddressTest extends AbstractTestCase {

	const EMAIL = 'mytest@test.com';
	const STREET = 'Talos Av. 123';
	const POST_CODE = '12345';
	const GEOLOCATION = NULL;
	const ADDRESSES_QUANTITY = 4;
	const EMAIL_WN_EMPTY_ADDRESSES = "mytest@notempty.com";
	const ACTUAL_STREET = "Carrer de los Castillejos, 373, 8a";
	const ACTUAL_POST_CODE = 12345;

	protected function alterConfig(array $config) {
		return $config;
	}

	public function setup() {
		parent::setup();

		$this->userService = new UserService($this->getServiceManager());
	}

	public function createAddress(){
		$data = array();
		$data['street'] = $this::STREET;
		$data['post_code'] = $this::POST_CODE;
		$data['geolocation'] = $this::GEOLOCATION;
		$address = new Address($data);	

		return $address;
	}

	public function testCreateAddress(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$user->getAddresses()->add($this->createAddress());

		$this->userService->save($user);
		$addresses = $user->getAddresses();

		$this->assertEquals($this::STREET, $addresses->get(0)->getStreet());
	}

	public function testCreateTwoOrMoreAddresses(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL));
		$addresses = $user->getAddresses();
		for ($i=0; $i < $this::ADDRESSES_QUANTITY; $i++) { 
			$addresses->add($this->createAddress());
		}
		
		$this->userService->save($user);
		$addresses = $user->getAddresses();

		$this->assertCount($this::ADDRESSES_QUANTITY, $addresses);
	}

	public function testListAddresses(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL_WN_EMPTY_ADDRESSES));
		$addresses = $user->getAddresses();
		$total = count($addresses);

		$this->assertCount(1, $addresses);
		$this->assertEquals($this::ACTUAL_STREET, $addresses->get(0)->getStreet());
		$this->assertEquals($this::ACTUAL_POST_CODE, $addresses->get(0)->getPostCode());
	}

	public function testModifyAddress(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL_WN_EMPTY_ADDRESSES));

		$user->getAddresses()->get(0)->setStreet($this::STREET);
		$user->getAddresses()->get(0)->setEmail($this::EMAIL);
		$user->getAddresses()->get(0)->setPostCode($this::POST_CODE);
		$user->getAddresses()->get(0)->setGeolocation($this::GEOLOCATION);
		$this->userService->save($user);

		$addresses = $user->getAddresses();

		$this->assertEquals($this::STREET, $addresses->get(0)->getStreet());
		$this->assertEquals($this::POST_CODE, $addresses->get(0)->getPostCode());
	}

	public function testDeleteAddress(){
		$user = $this->userService->findOneBy(array('email' => $this::EMAIL_WN_EMPTY_ADDRESSES));
		
		$address = $user->getAddresses()->get(0);
		$user->getAddresses()->removeElement($address);

		$this->userService->save($user);
		$addresses = $user->getAddresses();
		$this->assertCount(0, $addresses);
	}

}
