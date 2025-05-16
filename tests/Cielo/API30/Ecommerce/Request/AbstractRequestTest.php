<?php
namespace tests\Cielo\API30\Ecommerce\Request;

use PHPUnit\Framework\TestCase;
use Cielo\API30\Ecommerce\Request\AbstractRequest;
use Cielo\API30\Ecommerce\Merchant;
use Cielo\API30\Ecommerce\Request\CieloRequestException;

class AbstractRequestTest extends TestCase
{
    public function getMockedRequest()
    {
        $merchant = $this->createMock(Merchant::class);
        return $this->getMockForAbstractClass(
            AbstractRequest::class,
            [$merchant]
        );
    }

    public function testReadResponseDefaultWithJsonMessage()
    {
        $request = $this->getMockedRequest();
        $statusCode = 418; // Código não tratado explicitamente
        $responseBody = json_encode([
            ["Message" => "Mensagem de erro personalizada"]
        ]);

        $this->expectException(CieloRequestException::class);
        $this->expectExceptionMessage('Status code: 418');
        $this->expectExceptionMessage('Mensagem de erro personalizada');

        $this->invokeReadResponse($request, $statusCode, $responseBody);
    }

    public function testReadResponseDefaultWithStringBody()
    {
        $request = $this->getMockedRequest();
        $statusCode = 499;
        $responseBody = 'Erro desconhecido do gateway';

        $this->expectException(CieloRequestException::class);
        $this->expectExceptionMessage('Status code: 499');
        $this->expectExceptionMessage('Erro desconhecido do gateway');

        $this->invokeReadResponse($request, $statusCode, $responseBody);
    }

    private function invokeReadResponse($request, $statusCode, $responseBody)
    {
        $reflection = new \ReflectionClass($request);
        $method = $reflection->getMethod('readResponse');
        $method->setAccessible(true);
        $method->invoke($request, $statusCode, $responseBody);
    }
} 