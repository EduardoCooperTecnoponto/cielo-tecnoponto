<?php

namespace Tecnoponto\Cielo\API30\Ecommerce;

use Tecnoponto\Cielo\API30\Ecommerce\Request\ChangeRecurrentPaymentRequest;
use Tecnoponto\Cielo\API30\Ecommerce\Request\CreateSaleRequest;
use Tecnoponto\Cielo\API30\Ecommerce\Request\DeactiveRecurrencyPayment;
use Tecnoponto\Cielo\API30\Ecommerce\Request\QueryRecurrentPaymentRequest;
use Tecnoponto\Cielo\API30\Ecommerce\Request\QuerySaleRequest;
use Tecnoponto\Cielo\API30\Ecommerce\Request\ReactivateRecurrencyPayment;
use Tecnoponto\Cielo\API30\Ecommerce\Request\RecurrencyAmountRecurrencyPayment;
use Tecnoponto\Cielo\API30\Ecommerce\Request\RecurrencyDayRecurrencyPayment;
use Tecnoponto\Cielo\API30\Ecommerce\Request\TokenizeCardRequest;
use Tecnoponto\Cielo\API30\Ecommerce\Request\UpdateSaleRequest;
use Tecnoponto\Cielo\API30\Merchant;
use Psr\Log\LoggerInterface;

/**
 * The Cielo Ecommerce SDK front-end;.
 */
class CieloEcommerce
{
    private $merchant;

    private $environment;

    private $logger;

    /**
     * Create an instance of CieloEcommerce choosing the environment where the
     * requests will be send.
     *
     * @param Merchant $merchant
     *                           The merchant credentials
     * @param Environment environment
     *            The environment: {@link Environment::production()} or
     *            {@link Environment::sandbox()}
     */
    public function __construct(Merchant $merchant, Environment $environment = null, LoggerInterface $logger = null)
    {
        if (null === $environment) {
            $environment = Environment::production();
        }

        $this->merchant = $merchant;
        $this->environment = $environment;
        $this->logger = $logger;
    }

    /**
     * Send the Sale to be created and return the Sale with tid and the status
     * returned by Cielo.
     *
     * @param Sale $sale
     *                   The preconfigured Sale
     *
     * @throws \Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException if anything gets wrong
     *
     * @see <a href=
     *      "https://developercielo.github.io/Webservice-3.0/english.html#error-codes">Error
     *      Codes</a>
     */
    public function createSale(Sale $sale)
    {
        $createSaleRequest = new CreateSaleRequest($this->merchant, $this->environment, $this->logger);

        return $createSaleRequest->execute($sale);
    }

    /**
     * Query a Sale on Cielo by paymentId.
     *
     * @param string $paymentId
     *                          The paymentId to be queried
     *
     * @throws \Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException if anything gets wrong
     *
     * @see <a href=
     *      "https://developercielo.github.io/Webservice-3.0/english.html#error-codes">Error
     *      Codes</a>
     */
    public function getSale($paymentId)
    {
        $querySaleRequest = new QuerySaleRequest($this->merchant, $this->environment, $this->logger);

        return $querySaleRequest->execute($paymentId);
    }

    /**
     * Query a RecurrentPayment on Cielo by RecurrentPaymentId.
     *
     * @param string $recurrentPaymentId
     *                                   The RecurrentPaymentId to be queried
     * @see <a href=
     *      "https://developercielo.github.io/Webservice-3.0/english.html#error-codes">Error
     *      Codes</a>
     */
    public function getRecurrentPayment($recurrentPaymentId)
    {
        $queryRecurrentPaymentRequest = new queryRecurrentPaymentRequest($this->merchant, $this->environment, $this->logger);

        return $queryRecurrentPaymentRequest->execute($recurrentPaymentId);
    }

    /**
     * Cancel a Sale on Cielo by paymentId and speficying the amount.
     *
     * @param string $paymentId
     *                          The paymentId to be queried
     * @param int    $amount
     *                          Order value in cents
     *
     * @throws \Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException if anything gets wrong
     *
     * @see <a href=
     *      "https://developercielo.github.io/Webservice-3.0/english.html#error-codes">Error
     *      Codes</a>
     */
    public function cancelSale($paymentId, $amount = null)
    {
        $updateSaleRequest = new UpdateSaleRequest('void', $this->merchant, $this->environment, $this->logger);

        $updateSaleRequest->setAmount($amount);

        return $updateSaleRequest->execute($paymentId);
    }

    /**
     * Capture a Sale on Cielo by paymentId and specifying the amount and the
     * serviceTaxAmount.
     *
     * @param string $paymentId
     *                                 The paymentId to be captured
     * @param int    $amount
     *                                 Amount of the authorization to be captured
     * @param int    $serviceTaxAmount
     *                                 Amount of the authorization should be destined for the service
     *                                 charge
     *
     * @throws \Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException if anything gets wrong
     *
     * @see <a href=
     *      "https://developercielo.github.io/Webservice-3.0/english.html#error-codes">Error
     *      Codes</a>
     */
    public function captureSale($paymentId, $amount = null, $serviceTaxAmount = null)
    {
        $updateSaleRequest = new UpdateSaleRequest('capture', $this->merchant, $this->environment, $this->logger);

        $updateSaleRequest->setAmount($amount);
        $updateSaleRequest->setServiceTaxAmount($serviceTaxAmount);

        return $updateSaleRequest->execute($paymentId);
    }

    /**
     * @return CreditCard
     */
    public function tokenizeCard(CreditCard $card)
    {
        $tokenizeCardRequest = new TokenizeCardRequest($this->merchant, $this->environment, $this->logger);

        return $tokenizeCardRequest->execute($card);
    }

    /**
     * Deactivate a RecurrentPayment on Cielo.
     *
     * @param string $recurrentPaymentId
     *                                   The RecurrentPaymentId to be deactivated
     *
     * @throws \Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException if anything gets wrong
     *
     * @see <a href=
     *      "https://developercielo.github.io/Webservice-3.0/english.html#error-codes">Error
     *      Codes</a>
     */
    public function deactivateRecurrentPayment($recurrentPaymentId)
    {
        $request = new DeactiveRecurrencyPayment($this->merchant, $this->environment, $this->logger);

        return $request->execute($recurrentPaymentId);
    }

    /**
     * Reactivate a RecurrentPayment on Cielo.
     *
     * @param string $recurrentPaymentId
     *                                   The RecurrentPaymentId to be reactivated
     *
     * @throws \Tecnoponto\Cielo\API30\Ecommerce\Request\CieloRequestException if anything gets wrong
     *
     * @see <a href=
     *      "https://developercielo.github.io/Webservice-3.0/english.html#error-codes">Error
     *      Codes</a>
     */
    public function reactivateRecurrentPayment($recurrentPaymentId)
    {
        $request = new ReactivateRecurrencyPayment($this->merchant, $this->environment, $this->logger);

        return $request->execute($recurrentPaymentId);
    }

    /**
     * Change the day of a RecurrentPayment on Cielo.
     *
     * @param $recurrentPaymentId
     * @param int $recurrencyDay
     *
     * @return mixed
     */
    public function changeDayRecurrentPayment($recurrentPaymentId, $recurrencyDay)
    {
        $request = new RecurrencyDayRecurrencyPayment($this->merchant, $this->environment, $this->logger);

        $request->setContent($recurrencyDay);

        return $request->execute($recurrentPaymentId);
    }

    /**
     * Change the amount of a RecurrentPayment on Cielo.
     *
     * @param $recurrentPaymentId
     * @param int $amount
     *
     * @return mixed
     */
    public function changeAmountRecurrentPayment($recurrentPaymentId, $amount)
    {
        $request = new RecurrencyAmountRecurrencyPayment($this->merchant, $this->environment, $this->logger);

        $request->setContent($amount);

        return $request->execute($recurrentPaymentId);
    }

    /**
     * Change the amount of a RecurrentPayment on Cielo.
     *
     * @return mixed
     */
    public function updateRecurrentPayment(Sale $sale)
    {
        $request = new ChangeRecurrentPaymentRequest($this->merchant, $this->environment, $this->logger);

        return $request->execute($sale);
    }
}
