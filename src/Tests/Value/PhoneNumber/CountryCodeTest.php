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
use stdClass;
use Surfnet\StepupBundle\Exception\InvalidArgumentException;
use Surfnet\StepupBundle\Value\Exception\UnknownCountryCodeException;
use Surfnet\StepupBundle\Value\PhoneNumber\CountryCode;

class CountryCodeTest extends UnitTest
{
    /**
     * @test
     * @group value
     * @dataProvider invalidConstructorArgumentProvider
     *
     * @param mixed $invalidArgument
     */
    public function a_country_code_cannot_be_constructed_with_anything_but_a_string($invalidArgument)
    {
        $this->expectException(InvalidArgumentException::class);

        new CountryCode($invalidArgument);
    }

    /**
     * @test
     * @group        value
     * @dataProvider invalidStringArgumentProvider
     *
     * @param string $invalidArgument
     */
    public function a_phone_number_can_only_be_created_if_the_string_contains_digits_only($invalidArgument)
    {
        $this->expectException(InvalidArgumentException::class);

        new CountryCode($invalidArgument);
    }

    /**
     * @test
     * @group value
     */
    public function a_country_code_cannot_be_created_with_a_country_code_that_does_not_exist()
    {
        $this->expectException(UnknownCountryCodeException::class);

        new CountryCode('99999');
    }

    /**
     * @test
     * @group value
     */
    public function the_country_code_returns_the_country_code_upon_request()
    {
        $definition = '1649';
        $countryCode = new CountryCode($definition);

        $this->assertEquals($definition, $countryCode->getCountryCode());
    }

    /**
     * @test
     * @group value
     */
    public function country_codes_are_equal_when_the_given_country_code_is_equal()
    {
        $base      = new CountryCode('1787');
        $same      = new CountryCode('1787');
        $different = new CountryCode('1939');

        $this->assertTrue($base->equals($same), 'Country codes with the same definition should be equal');
        $this->assertFalse($base->equals($different), 'Country codes with a different definition should not be equal');
    }

    /**
     * @test
     * @group value
     * @dataProvider toStringProvider
     *
     * @param string $definition
     * @param string $stringRepresentation
     */
    public function to_string_renders_a_correctly_formattted_string_representation($definition, $stringRepresentation)
    {
        $countryCode = new CountryCode($definition);
        $this->assertSame($stringRepresentation, $countryCode->__toString());
    }

    public function invalidConstructorArgumentProvider()
    {
        return [
            'int'           => [0],
            'float'         => [1.1],
            'boolean false' => [false],
            'boolean true'  => [true],
            'array'         => [[]],
            'object'        => [new stdClass()]
        ];
    }

    public function toStringProvider()
    {
        return [
            '4 digits'      => ['1787', '+1 787'],
            '3 digits'      => ['691', '+691'],
            '2 digits'      => ['31', '+31'],
            '1 digit'       => ['1', '+1'],
            'Kazakhstan 76' => ['76', '+7 6'],
            'Kazakhstan 77' => ['77', '+7 7']
        ];
    }

    public function invalidStringArgumentProvider()
    {
        return [
            'with characters'     => ['3AB8'],
            'with symbols'        => ['2!2'],
            'with spaces'         => ['1 3'],
            'with leading space'  => [' 31'],
            'with trailing space' => ['31 '],
        ];
    }
}
