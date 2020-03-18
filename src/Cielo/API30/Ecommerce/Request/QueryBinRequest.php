<?php

namespace Cielo\API30\Ecommerce\Request;

use Cielo\API30\Ecommerce\Bin;
use Cielo\API30\Ecommerce\Sale;
use Cielo\API30\Environment;
use Cielo\API30\Merchant;

/**
 * Class QuerySaleRequest
 *
 * @package Cielo\API30\Ecommerce\Request
 */
class QueryBinRequest extends AbstractRequest
{

    private $environment;

    /**
     * QuerySaleRequest constructor.
     *
     * @param Merchant    $merchant
     * @param Environment $environment
     */
    public function __construct(Merchant $merchant, Environment $environment)
    {
        parent::__construct($merchant);

        $this->environment = $environment;
    }

    /**
     * @param $bin
     *
     * @return null
     * @throws \Cielo\API30\Ecommerce\Request\CieloRequestException
     * @throws \RuntimeException
     */
    public function execute($bin)
    {
        $url = $this->environment->getApiQueryURL() . '1/cardBin/' . $bin;

        return $this->sendRequest('GET', $url);
    }

    /**
     * @param $json
     *
     * @return Bin
     */
    protected function unserialize($json)
    {
        return Bin::fromJson($json);
    }
}
