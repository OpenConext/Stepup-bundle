<?php

declare(strict_types = 1);

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
use Surfnet\StepupBundle\Value\Exception\InvalidPhoneNumberFormatException;
use Surfnet\StepupBundle\Value\PhoneNumber\PhoneNumber;

class PhoneNumberTest extends UnitTest
{
    /**
     * @test
     * @group value
     * @dataProvider invalidConstructorArgumentProvider
     */
    public function a_phone_number_cannot_be_created_with_anything_but_a_string(mixed $invalidArgument): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PhoneNumber($invalidArgument);
    }

    /**
     * @test
     * @group value
     * @dataProvider invalidStringArgumentProvider
     */
    public function a_phone_number_can_only_be_created_if_the_string_contains_digits_only(string $invalidArgument): void
    {
        $this->expectException(InvalidPhoneNumberFormatException::class);

        new PhoneNumber($invalidArgument);
    }

    /**
     * @test
     * @group value
     */
    public function the_original_phone_number_is_returned_upon_request(): void
    {
        $original = '0612345678';

        $phoneNumber = new PhoneNumber($original);

        $this->assertEquals($original, $phoneNumber->getNumber());
    }

    /**
     * @test
     * @group value
     * @dataProvider formatAsMsisdnPartProvider
     */
    public function format_as_msisdn_part_strips_exactly_one_leading_zero_if_it_has_one(string $given, string $expectedMsisdnPart): void
    {
        $phoneNumber = new PhoneNumber($given);

        $this->assertEquals($expectedMsisdnPart, $phoneNumber->formatAsMsisdnPart());
    }

    /**
     * @test
     * @group value
     */
    public function phone_numbers_are_equal_if_the_given_numbers_match_as_msisdn_part(): void
    {
        $base                        = new PhoneNumber('0612345678');
        $same                        = new PhoneNumber('0612345678');
        $sameWithoutLeadingZero      = new PhoneNumber('612345678');
        $different                   = new PhoneNumber('0612345679');
        $differentWithoutLeadingZero = new PhoneNumber('612345679');

        $this->assertTrue($base->equals($same));
        $this->assertTrue($base->equals($sameWithoutLeadingZero));
        $this->assertFalse($base->equals($different));
        $this->assertFalse($base->equals($differentWithoutLeadingZero));
    }

    /**
     * @test
     * @group value
     * @dataProvider toStringProvider
     */
    public function as_string_the_phone_number_is_rendered_with_a_replaced_leading_zero_between_brackets(
        string $given,
        string $expected
    ): void {
        $phoneNumber = new PhoneNumber($given);

        $this->assertEquals($expected, $phoneNumber->__toString());
    }

    public function invalidConstructorArgumentProvider(): array
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

    public function invalidStringArgumentProvider(): array
    {
        return [
            'with characters'     => ['06123AB78'],
            'with symbols'        => ['06!2345^78'],
            'with spaces'         => ['061 3456 8'],
            'with leading space'  => [' 0612345678'],
            'with trailing space' => ['0612345678 '],
        ];
    }

    public function formatAsMsisdnPartProvider(): array
    {
        return [                    //given,        expected output
            'no leading zero'   => ['612345678',   '612345678'],
            'one leading zero'  => ['0612345678',  '612345678'],
            'two leading zeros' => ['00612345678', '0612345678'],
            'trailing zero'     => ['6123456780',  '6123456780']
        ];
    }

    public function toStringProvider(): array
    {
        return [                    //given,        expected output
            'no leading zero'   => ['612345678',   '(0) 612345678'],
            'one leading zero'  => ['0612345678',  '(0) 612345678'],
            'two leading zeros' => ['00612345678', '(0) 0612345678'],
            'trailing zero'     => ['6123456780',  '(0) 6123456780']
        ];
    }
}
