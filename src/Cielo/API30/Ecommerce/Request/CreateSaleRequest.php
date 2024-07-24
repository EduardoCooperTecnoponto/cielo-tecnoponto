<?php

namespace Tecnoponto\Cielo\API30\Ecommerce\Request;

use Tecnoponto\Cielo\API30\Ecommerce\Sale;
use Tecnoponto\Cielo\API30\Environment;
use Tecnoponto\Cielo\API30\Merchant;
use Psr\Log\LoggerInterface;

/**
 * Class CreateSaleRequest.
 */
class CreateSaleRequest extends AbstractRequest
{
    private $environment;

    /**
     * CreateSaleRequest constructor.
     */
    public function __construct(Merchant $merchant, Environment $environment, LoggerInterface $logger = null)
    {
        parent::__construct($merchant, $logger);

        $this->environment = $environment;
    }

    /**
     * @param $sale
     *
     * @throws \Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException
     * @throws \RuntimeException
     *
     * @return null
     */
    public function execute($sale)
    {
        $url = $this->environment->getApiUrl().'1/sales/';

        return $this->sendRequest('POST', $url, $sale);
    }

    /**
     * @param $json
     *
     * @return Sale
     */
    protected function unserialize($json)
    {
        return Sale::fromJson($json);
    }
}
