<?php

namespace Cielo\API30\Ecommerce\Request;

use Cielo\API30\Ecommerce\RecurrentPayment;
use Cielo\API30\Environment;
use Cielo\API30\Merchant;
use Psr\Log\LoggerInterface;

class UpdateRecurrentPaymentRequest extends AbstractRequest
{
    protected $kind;
    private $environment;
    private $logger;

    /**
     * UpdateRecurrentPaymentRequest constructor.
     */
    public function __construct(Merchant $merchant, Environment $environment, LoggerInterface $logger = null)
    {
        parent::__construct($merchant);

        $this->environment = $environment;
        $this->content = null;
        $this->logger = $logger;
    }

    /**
     * @param $recurrentPaymentId
     *
     * @throws \Cielo\API30\Ecommerce\Request\CieloRequestException
     * @throws \RuntimeException
     *
     * @return null
     */
    public function execute($recurrentPaymentId)
    {
        $url = $this->environment->getApiURL()."1/RecurrentPayment/{$recurrentPaymentId}/{$this->kind}";

        return $this->sendRequest('PUT', $url, $this->content);
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
     *
     * @return null|mixed
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this->content;
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
