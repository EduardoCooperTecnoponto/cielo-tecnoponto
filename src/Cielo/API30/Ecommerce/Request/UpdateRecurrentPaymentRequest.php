<?php

namespace Cielo\API30\Ecommerce\Request;

use Cielo\API30\Ecommerce\RecurrentPayment;
use Cielo\API30\Environment;
use Cielo\API30\Merchant;
use Psr\Log\LoggerInterface;

/**
 * @package Cielo\API30\Ecommerce\Request
 */
class UpdateRecurrentPaymentRequest extends AbstractRequest
{
    private $environment;
    private $logger;
    protected $kind;

    /**
    * UpdateRecurrentPaymentRequest constructor.
    *
    * @param Merchant    $merchant
    * @param Environment $environment
     * @param LoggerInterface|null $logger
    */
    public function __construct(Merchant $merchant, Environment $environment, LoggerInterface $logger = null)
    {
        parent::__construct($merchant);

        $this->environment = $environment;
        $this->content = null;
        $this->logger      = $logger;
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
        $url = $this->environment->getApiURL() . "1/RecurrentPayment/{$recurrentPaymentId}}/{$this->kind}";

        return $this->sendRequest('PUT', $url, $this->content);
    }

    /**
     * @param $json
     * @return RecurrentPayment
     */
    protected function unserialize($json)
    {
        return RecurrentPayment::fromJson($json);
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     * @return mixed|null
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this->content;
    }
}
