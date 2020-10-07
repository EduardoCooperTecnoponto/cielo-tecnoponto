<?php

namespace Cielo\API30\Ecommerce\Request;

/**
 * @package Cielo\API30\Ecommerce\Request
 */
class DeactiveRecurrencyPayment extends UpdateRecurrentPaymentRequest
{
    protected $kind = 'Deactivate';
}
