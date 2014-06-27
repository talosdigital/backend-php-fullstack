<?php
namespace SalesTest\Tests;

use SalesTest\AbstractTestCase;

use Sales\Document\Voucher;

use Sales\Service\VoucherService;

class VoucherTest extends AbstractTestCase {

 	protected function alterConfig(array $config) {
 		return $config;
 	}

 	public function setup() {
 		parent::setup();

		$this->voucherService = new VoucherService($this->getServiceManager());
	}

	/*
	 *  Create a Voucher
	 *
	 */
	public function testCreateVoucher(){
		// input data
		$data = array(
			'name' => 'Voucher Name',
			'code' => $this->generateRandomString(16),
			'discount' => '15',
			'discount_type' => Voucher::VOUCHER_DISCOUNT_TYPE_PERCENTAGE,
			'date_start' => new \DateTime(),
			'date_end' => new \DateTime(), // update the end date
			'status' => Voucher::VOUCHER_STATUS_ACTIVE
		);

		// logic
		$voucher = new Voucher($data);
		$voucher = $this->voucherService->save($voucher);

		// asserts
		$this->assertNotNull($voucher);
		$voucher = $this->voucherService->findOneBy(array("code" => $data['code']));
		$this->assertNotNull($voucher);
		$this->assertEquals($voucher->getName(), $data['name']);
		$this->assertEquals($voucher->getCode(), $data['code']);
		$this->assertEquals($voucher->getDiscount(), $data['discount']);
		$this->assertEquals($voucher->getDiscountType(), $data['discount_type']);
		$this->assertEquals($voucher->getStatus(), Voucher::VOUCHER_STATUS_ACTIVE);
		$this->assertCount(2, $this->voucherService->findAll());
	}

	public function testGenerateSeveralVouchers(){
		// asseerts
		$count = $this->voucherService->findAll()->count();



		for($i=0; $i<10;$i++){
			$data = array(
				'name' => 'Voucher Name',
				'code' => $this->generateRandomString(16),
				'discount' => '15',
				'discount_type' => Voucher::VOUCHER_DISCOUNT_TYPE_PERCENTAGE,
				'date_start' => new \DateTime(),
				'date_end' => new \DateTime(),
				'status' => Voucher::VOUCHER_STATUS_ACTIVE
			);
			$voucher = new Voucher($data);
			$this->voucherService->save($voucher);
		}

		$this->assertCount($count + 10, $this->voucherService->findAll());
	}

	public function testUseVoucher($code = "pw8bSryj2PAJIgc3"){
		$voucher = $this->voucherService->findOneBy(array("code" => $code));
		$voucher->setStatus(Voucher::VOUCHER_STATUS_EXPIRED);
		$this->voucherService->save($voucher);
		$voucher = $this->voucherService->findOneBy(array("code" => $code));
		$this->assertEquals($voucher->getStatus(), Voucher::VOUCHER_STATUS_EXPIRED);
		$this->assertNotNull($voucher);
		return $voucher;
	}

	private function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
}