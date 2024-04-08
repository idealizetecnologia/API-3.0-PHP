<?php
namespace tests;

use Cielo\API30\Ecommerce\Payment;
use PHPUnit\Framework\TestCase;
use Faker;

class PaymentUnitTest extends TestCase
{
    public function testPopulatePixPayment()
    {
        $faker = Faker\Factory::create();
        $requestData = new \stdClass();
        $requestData->Amount = $faker->randomFloat(2, 0, 1000);
        $requestData->Type = Payment::PAYMENTTYPE_PIX;
        $requestData->Status = 12;
        $requestData->QrCodeString = $faker->sha256;
        $requestData->QrcodeBase64Image = $faker->sha256;
        $requestData->ProofOfSale = $faker->sha256;
        $requestData->Tid = $faker->sha256;
        $requestData->PaymentId = $faker->sha256;
        $requestData->ReturnMessage = $faker->text;
        $requestData->ReturnCode = '0';

        $payment = new Payment();
        $payment->populate($requestData);
        $this->assertEquals($requestData->Amount, $payment->getAmount());
        $this->assertEquals($requestData->Type, $payment->getType());
        $this->assertEquals($requestData->Status, $payment->getStatus());
        $this->assertEquals($requestData->QrCodeString, $payment->getPixQrCode());
        $this->assertEquals($requestData->QrcodeBase64Image, $payment->getPixQrCodeBase64());
        $this->assertEquals($requestData->ProofOfSale, $payment->getProofOfSale());
        $this->assertEquals($requestData->Tid, $payment->getTid());
        $this->assertEquals($requestData->PaymentId, $payment->getPaymentId());
        $this->assertEquals($requestData->ReturnMessage, $payment->getReturnMessage());
        $this->assertEquals($requestData->ReturnCode, $payment->getReturnCode());
    }

}