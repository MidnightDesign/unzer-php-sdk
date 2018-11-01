<?php
/**
 * This class defines integration tests to verify interface and functionality of the payment method prepayment.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * @copyright Copyright © 2016-present heidelpay GmbH. All rights reserved.
 *
 * @link  http://dev.heidelpay.com/
 *
 * @author  Simon Gabriel <development@heidelpay.com>
 *
 * @package  heidelpay/mgw_sdk/tests/integration/payment_types
 */
namespace heidelpay\MgwPhpSdk\test\integration\PaymentTypes;

use heidelpay\MgwPhpSdk\Constants\ApiResponseCodes;
use heidelpay\MgwPhpSdk\Constants\Currencies;
use heidelpay\MgwPhpSdk\Exceptions\HeidelpayApiException;
use heidelpay\MgwPhpSdk\Exceptions\HeidelpaySdkException;
use heidelpay\MgwPhpSdk\Resources\AbstractHeidelpayResource;
use heidelpay\MgwPhpSdk\Resources\PaymentTypes\Prepayment;
use heidelpay\MgwPhpSdk\Resources\TransactionTypes\Authorization;
use heidelpay\MgwPhpSdk\test\BasePaymentTest;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;

class PrepaymentTest extends BasePaymentTest
{
    /**
     * Verify Prepayment can be created and fetched.
     *
     * @return Prepayment
     *
     * @throws HeidelpayApiException
     * @throws AssertionFailedError
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws \RuntimeException
     * @throws HeidelpaySdkException
     * @test
     */
    public function prepaymentShouldBeCreatableAndFetchable(): AbstractHeidelpayResource
    {
        $prepayment = $this->heidelpay->createPaymentType(new Prepayment());
        $this->assertInstanceOf(Prepayment::class, $prepayment);
        $this->assertNotEmpty($prepayment->getId());

        $fetchedPrepayment = $this->heidelpay->fetchPaymentType($prepayment->getId());
        $this->assertInstanceOf(Prepayment::class, $fetchedPrepayment);
        $this->assertEquals($prepayment->expose(), $fetchedPrepayment->expose());

        return $fetchedPrepayment;
    }

    /**
     * Verify authorization of prepayment type.
     *
     * @test
     *
     * @depends prepaymentShouldBeCreatableAndFetchable
     *
     * @param Prepayment $prepayment
     *
     * @return Authorization
     *
     * @throws HeidelpayApiException
     * @throws ExpectationFailedException
     * @throws \RuntimeException
     * @throws HeidelpaySdkException
     */
    public function prepaymentTypeShouldBeAuthorizable(Prepayment $prepayment): Authorization
    {
        $authorization = $prepayment->authorize(100.0, Currencies::EURO, self::RETURN_URL);
        $this->assertNotNull($authorization);
        $this->assertNotNull($authorization->getId());
        $this->assertNotEmpty($authorization->getIban());
        $this->assertNotEmpty($authorization->getBic());
        $this->assertNotEmpty($authorization->getHolder());
        $this->assertNotEmpty($authorization->getDescriptor());

        return $authorization;
    }

    /**
     * Verify charging a prepayment throws an exception.
     *
     * @test
     *
     * @depends prepaymentShouldBeCreatableAndFetchable
     *
     * @param Prepayment $prepayment
     *
     * @throws HeidelpayApiException
     * @throws Exception
     * @throws \RuntimeException
     * @throws HeidelpaySdkException
     */
    public function prepaymentTypeShouldNotBeChargeable(Prepayment $prepayment)
    {
        $this->expectException(HeidelpayApiException::class);
        $this->expectExceptionCode(ApiResponseCodes::API_ERROR_TRANSACTION_CHARGE_NOT_ALLOWED);

        $prepayment->charge(100.0, Currencies::EURO, self::RETURN_URL);
    }

    /**
     * Verify shipment on a prepayment throws an exception.
     *
     * @test
     *
     * @depends prepaymentTypeShouldBeAuthorizable
     *
     * @param Authorization $authorization
     *
     * @throws HeidelpayApiException
     * @throws Exception
     * @throws \RuntimeException
     * @throws HeidelpaySdkException
     */
    public function prepaymentTypeShouldNotBeShippable(Authorization $authorization)
    {
        $this->expectException(HeidelpayApiException::class);
        $this->expectExceptionCode(ApiResponseCodes::API_ERROR_TRANSACTION_SHIP_NOT_ALLOWED);

        $this->heidelpay->ship($authorization->getPayment());
    }

    /**
     * Verify authorization of prepayment type.
     *
     * @test
     *
     * @depends prepaymentShouldBeCreatableAndFetchable
     *
     * @param Prepayment $prepayment
     *
     * @throws HeidelpayApiException
     * @throws ExpectationFailedException
     * @throws \RuntimeException
     * @throws HeidelpaySdkException
     */
    public function prepaymentAuthorizeCanBeCanceled(Prepayment $prepayment)
    {
        $authorization = $prepayment->authorize(100.0, Currencies::EURO, self::RETURN_URL);
        $cancellation = $authorization->cancel();
        $this->assertNotNull($cancellation);
        $this->assertNotNull($cancellation->getId());
    }
}