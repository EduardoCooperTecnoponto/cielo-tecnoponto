<?php

namespace Cielo\API30\Ecommerce\Request;

/**
 * @package Cielo\API30\Ecommerce\Request
 */
class ReactivateRecurrencyPayment extends UpdateRecurrentPaymentRequest
{
    protected $kind = 'Reactivate';
}
