<?php

namespace Cielo\API30\Ecommerce\Request;

use Cielo\API30\Merchant;

/**
 * Class AbstractSaleRequest
 *
 * @package Cielo\API30\Ecommerce\Request
 */
abstract class AbstractRequest
{

    private $merchant;

    /**
     * AbstractSaleRequest constructor.
     *
     * @param Merchant $merchant
     */
    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * @param $param
     *
     * @return mixed
     */
    public abstract function execute($param);

    /**
     * @param                        $method
     * @param                        $url
     * @param \JsonSerializable|null $content
     *
     * @return mixed
     *
     * @throws \Cielo\API30\Ecommerce\Request\CieloRequestException
     * @throws \RuntimeException
     */
    protected function sendRequest($method, $url, \JsonSerializable $content = null)
    {
        $headers = [
            'Accept: application/json',
            'Accept-Encoding: gzip',
            'User-Agent: CieloEcommerce/3.0 PHP SDK',
            'MerchantId: ' . $this->merchant->getId(),
            'MerchantKey: ' . $this->merchant->getKey(),
            'RequestId: ' . uniqid()
        ];

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        if ($content !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($content));

            $headers[] = 'Content-Type: application/json';
        } else {
            $headers[] = 'Content-Length: 0';
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response   = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            throw new \RuntimeException('Curl error: ' . curl_error($curl));
        }

        curl_close($curl);

        return $this->readResponse($statusCode, $response);
    }

    /**
     * @param $statusCode
     * @param $responseBody
     *
     * @return mixed
     *
     * @throws CieloRequestException
     */
    protected function readResponse($statusCode, $responseBody)
    {
        $unserialized = null;

        switch ($statusCode) {
            case 200:
            case 201:
                $unserialized = $this->unserialize($responseBody);
                break;
            case 400:
                $exception = null;
                $response  = json_decode($responseBody);
                // Se ainda não for um array, trata como erro genérico
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($response)) {
                    $message = is_string($responseBody) ? $responseBody : 'Erro desconhecido';
                    $cieloError = new CieloError($message, '400');
                    $exception  = new CieloRequestException('Request Error :' . $message, $statusCode);
                    $exception->setCieloError($cieloError);
                    throw $exception;
                }

                // Se for objeto, transforma em array para iterar
                if (is_object($response)) {
                    $response = [$response];
                }

                foreach ($response as $error) {
                    $cieloError = new CieloError($error->Message, $error->Code);
                    $exception  = new CieloRequestException('Request Error', $statusCode, $exception);
                    $exception->setCieloError($cieloError);
                }

                throw $exception;
            case 401:
                throw new CieloRequestException('Access denied', 401, null);
            case 403:
                throw new CieloRequestException('Forbidden: você não tem permissão para acessar este recurso.', 403, null);
            case 404:
                throw new CieloRequestException('Resource not found', 404, null);
            case 409:
                throw new CieloRequestException('Conflict: recurso em conflito, verifique se já existe ou se há duplicidade.', 409, null);
            case 422:
                throw new CieloRequestException('Unprocessable Entity: erro de validação dos dados enviados.', 422, null);
            case 429:
                throw new CieloRequestException('Too Many Requests: limite de requisições excedido, tente novamente mais tarde.', 429, null);
            case 500:
                throw new CieloRequestException('Internal Server Error: erro interno no servidor da Cielo.', 500, null);
            case 502:
                throw new CieloRequestException('Bad Gateway: erro de comunicação com o gateway da Cielo.', 502, null);
            case 503:
                throw new CieloRequestException('Service Unavailable: serviço temporariamente indisponível.', 503, null);
            case 504:
                throw new CieloRequestException('Gateway Timeout: tempo de resposta excedido.', 504, null);
            default:
                $message = 'Unknown status';
                if (!empty($responseBody)) {
                    $decoded = json_decode($responseBody);
                    if (is_array($decoded) && isset($decoded[0]->Message)) {
                        $message = $decoded[0]->Message;
                    } elseif (is_object($decoded) && isset($decoded->Message)) {
                        $message = $decoded->Message;
                    } elseif (is_string($responseBody)) {
                        $message = $responseBody;
                    }
                }
                throw new CieloRequestException("Erro desconhecido. Status code: $statusCode. Mensagem: $message", $statusCode);
        }

        return $unserialized;
    }

    /**
     * @param $json
     *
     * @return mixed
     */
    protected abstract function unserialize($json);
}
