<?php

namespace Ciareis\Cielo\API30\Ecommerce\Request;

use Ciareis\Cielo\API30\Ecommerce\RecurrentPayment;
use Ciareis\Cielo\API30\Environment;
use Ciareis\Cielo\API30\Merchant;
use Psr\Log\LoggerInterface;

/**
 * Class QueryRecurrentPaymentRequest
 *
 * @package Cielo\API30\Ecommerce\Request
 */
class QueryRecurrentPaymentRequest extends AbstractRequest
{

    private $environment;

	/**
	 * QueryRecurrentPaymentRequest constructor.
	 *
	 * @param Merchant $merchant
	 * @param Environment $environment
	 * @param LoggerInterface|null $logger
	 */
    public function __construct(Merchant $merchant, Environment $environment, LoggerInterface $logger = null)
    {
        parent::__construct($merchant, $logger);

        $this->environment = $environment;
    }

    /**
     * @param $recurrentPaymentId
     *
     * @return null
     * @throws \Cielo\API30\Ecommerce\Request\CieloRequestException
     * @throws \RuntimeException
     */
    public function execute($recurrentPaymentId)
    {
        $url = $this->environment->getApiQueryURL() . '1/RecurrentPayment/' . $recurrentPaymentId;

        return $this->sendRequest('GET', $url);
    }

    /**
     * @param $json
     *
     * @return RecurrentPayment
     */
    protected function unserialize($json)
    {
        return RecurrentPayment::fromJson($json);
    }
}
