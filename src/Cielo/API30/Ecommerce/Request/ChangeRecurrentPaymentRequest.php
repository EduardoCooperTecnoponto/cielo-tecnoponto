<?php

namespace Ciareis\Cielo\API30\Ecommerce\Request;

use Ciareis\Cielo\API30\Ecommerce\Sale;
use Ciareis\Cielo\API30\Environment;
use Ciareis\Cielo\API30\Merchant;
use Psr\Log\LoggerInterface;

/**
 * Class ChangeRecurrentPaymentRequest.
 */
class ChangeRecurrentPaymentRequest extends AbstractRequest
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
     * @throws \Cielo\API30\Ecommerce\Request\CieloRequestException
     * @throws \RuntimeException
     *
     * @return null
     */
    public function execute($sale)
    {
        $id = $sale->getMerchantOrderId();
        $creditcard = $sale->getPayment()->getCreditCard()->jsonSerialize();
        $payment = $sale->getPayment()->jsonSerialize();
        $params = [
            'Type' => $payment['type'],
            'Amount' => $payment['amount'],
            'Installments' => $payment['installments'],
            'CreditCard' => [
                'CardNumber' => $creditcard['cardNumber'],
                'Holder' => $creditcard['holder'],
                'ExpirationDate' => $creditcard['expirationDate'],
                'SecurityCode' => $creditcard['securityCode'],
                'Brand' => $creditcard['brand'],
            ],
        ];

        $url = $this->environment->getApiUrl()."1/RecurrentPayment/{$id}/Payment";

        return $this->sendRequest('PUT', $url, $params);
    }

    /**
     * @param $json
     *
     * @return Sale
     */
    protected function unserialize($json)
    {
        return $json;
    }
}
