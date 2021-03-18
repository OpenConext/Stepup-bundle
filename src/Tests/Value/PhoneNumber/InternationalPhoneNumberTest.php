<?php

/**
 * Copyright 2014 SURFnet bv
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Surfnet\StepupBundle\Tests\Value\PhoneNumber;

use PHPUnit\Framework\TestCase as UnitTest;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Surfnet\StepupBundle\Value\Exception\InvalidPhoneNumberFormatException;
use Surfnet\StepupBundle\Value\PhoneNumber\CountryCode;
use Surfnet\StepupBundle\Value\PhoneNumber\InternationalPhoneNumber;
use Surfnet\StepupBundle\Value\PhoneNumber\PhoneNumber;

class InternationalPhoneNumberTest extends UnitTest
{
    /**
     * @test
     * @group value
     */
    public function equality_is_based_on_country_code_and_phone_number_contents()
    {
        $base         = new InternationalPhoneNumber(new CountryCode('31'), new PhoneNumber('123'));

        $same         = new InternationalPhoneNumber(new CountryCode('31'), new PhoneNumber('123'));
        $otherCountry = new InternationalPhoneNumber(new CountryCode('32'), new PhoneNumber('123'));
        $otherNumber  = new InternationalPhoneNumber(new CountryCode('31'), new PhoneNumber('456'));
        $different    = new InternationalPhoneNumber(new CountryCode('32'), new PhoneNumber('456'));

        $this->assertTrue($base->equals($same));

        $this->assertFalse($base->equals($otherCountry), 'Not equal with different Country Code');
        $this->assertFalse($base->equals($otherNumber), 'Not equal with different Number');
        $this->assertFalse($base->equals($different), 'Not equal with different Country Code and different number');
    }

    /**
     * @test
     * @group value
     */
    public function it_can_be_cast_to_string_and_recreated_equally_from_that_string()
    {
        $phoneNumber = new InternationalPhoneNumber(new CountryCode('1808'), new PhoneNumber('0612345678'));
        $expectedString = '+1 808 (0) 612345678';

        $asString = (string) $phoneNumber;
        $this->assertEquals($expectedString, $asString);

        $phoneNumberFromString = InternationalPhoneNumber::fromStringFormat($asString);
        $this->assertTrue($phoneNumber->equals($phoneNumberFromString));
    }

    /**
     * @test
     * @group value
     */
    public function msisdn_representation_is_formatted_correctly_as_a_continuous_string_of_digits()
    {
        $phoneNumber = new InternationalPhoneNumber(new CountryCode('31'), new PhoneNumber('0612345678'));

        $this->assertEquals('31612345678', $phoneNumber->toMSISDN());
    }

    public function invalid_types()
    {
        return [
            'array'        => [array()],
            'integer'      => [1],
            'object'       => [new \stdClass()],
            'null'         => [null],
            'bool'         => [false],
            'resource'     => [fopen('php://memory', 'w')],
        ];
    }

    /**
     * @test
     * @dataProvider invalid_types
     * @group value
     *
     * @param mixed $invalidType
     */
    public function it_rejects_invalid_types($invalidType)
    {
        $this->expectException(InvalidArgumentException::class);

        InternationalPhoneNumber::fromStringFormat($invalidType);
    }

    public function invalid_phone_numbers()
    {
        return [
            'garbage before phone number' => ['garbage+31 (0) 681819571'],
            'garbage after phone number' => ['+31 (0) 681819571garbage'],
            'msisdn'       => ['31612345678'],
            'empty string' => [''],
        ];
    }

    /**
     * @test
     * @dataProvider invalid_phone_numbers
     * @group value
     *
     * @param mixed $invalidPhoneNumber
     */
    public function it_rejects_invalid_phone_numbers($invalidPhoneNumber)
    {
        $this->expectException(InvalidPhoneNumberFormatException::class);

        InternationalPhoneNumber::fromStringFormat($invalidPhoneNumber);
    }
}
