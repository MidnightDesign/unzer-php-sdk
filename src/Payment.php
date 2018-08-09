<?php
/**
 * Description
 *
 * @license Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 * @copyright Copyright © 2016-present heidelpay GmbH. All rights reserved.
 *
 * @link  http://dev.heidelpay.com/
 *
 * @author  Simon Gabriel <development@heidelpay.de>
 *
 * @package  heidelpay/${Package}
 */
namespace heidelpay\NmgPhpSdk;

use heidelpay\NmgPhpSdk\PaymentTypes\PaymentTypeInterface;

class Payment
{
    /** @var PaymentTypeInterface */
    private $paymentType;

    /** @var Customer $customer */
    private $customer;

    /**
     * Payment constructor.
     * @param PaymentTypeInterface $paymentType
     */
    public function __construct(PaymentTypeInterface $paymentType)
    {
        $this->paymentType = $paymentType;
    }

    //<editor-fold desc="Getters/Setters">
    /**
     * @return PaymentTypeInterface
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @param PaymentTypeInterface $paymentType
     * @return Payment
     */
    public function setPaymentType(PaymentTypeInterface $paymentType)
    {
        $this->paymentType = $paymentType;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return Payment
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }
    //</editor-fold>
}