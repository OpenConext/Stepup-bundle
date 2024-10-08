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

namespace Surfnet\StepupBundle\Tests\Value;

use Error;
use PHPUnit\Framework\TestCase as UnitTest;
use stdClass;
use Surfnet\StepupBundle\Exception\DomainException;
use Surfnet\StepupBundle\Value\Loa;

class LoaTest extends UnitTest
{
    /**
     * @test
     * @group        value
     * @dataProvider wrongTypeLevelProvider
     */
    public function it_cannot_be_created_with_a_wrong_loa_data_type(mixed $invalidLevel): void
    {
        self::expectException(Error::class);

        new Loa($invalidLevel, 'identifier');
    }

    /**
     * @test
     * @group        value
     * @dataProvider invalidLevelProvider
     */
    public function it_cannot_be_created_with_an_invalid_loa_type(mixed $invalidLevel): void
    {
        $this->expectException(DomainException::class);

        new Loa($invalidLevel, 'identifier');
    }

    /**
     * @test
     * @group value
     */
    public function it_cannot_be_created_when_the_identifier_is_not_a_string(): void
    {
        // Using a data provider causes PHP to type cast certain values. Resulting in false test results
        // For example. False was converted to 0.
        self::expectException(Error::class);
        new Loa(Loa::LOA_1, 1);
        self::expectException(Error::class);
        new Loa(Loa::LOA_1, 1.1);
        self::expectException(Error::class);
        new Loa(Loa::LOA_1, false);
        self::expectException(Error::class);
        new Loa(Loa::LOA_1, []);
        self::expectException(Error::class);
        new Loa(Loa::LOA_1, new stdClass());
    }

    /**
     * @test
     * @group value
     */
    public function the_loa_can_be_asked_whether_or_not_it_has_a_particular_identifier(): void
    {
        $correctIdentifier = 'correct identifier';
        $otherIdentifier = 'Not the correct identifier';

        $loa = new Loa(Loa::LOA_1, $correctIdentifier);

        $this->assertTrue($loa->isIdentifiedBy($correctIdentifier));
        $this->assertFalse($loa->isIdentifiedBy($otherIdentifier));
    }

    /**
     * @test
     * @group value
     */
    public function it_correctly_compares_lower_or_equal_to_level(): void
    {
        $loa = new Loa(Loa::LOA_2, 'a');

        $this->assertTrue($loa->levelIsLowerOrEqualTo(Loa::LOA_3), 'Loa 2 <= Loa 3');
        $this->assertTrue($loa->levelIsLowerOrEqualTo(Loa::LOA_2), 'Loa 2 <= Loa 2');
        $this->assertFalse($loa->levelIsLowerOrEqualTo(Loa::LOA_1), 'Loa 2 !<= Loa 1');
        $this->assertFalse($loa->levelIsLowerOrEqualTo(Loa::LOA_SELF_VETTED), 'Loa 2 !<= Loa 1.5');
    }

    /**
     * @test
     * @group value
     */
    public function it_correctly_compares_higher_or_equal_to_level(): void
    {
        $loa = new Loa(Loa::LOA_2, 'a');

        $this->assertFalse($loa->levelIsHigherOrEqualTo(Loa::LOA_3), 'Loa 2 !>= Loa 3');
        $this->assertTrue($loa->levelIsHigherOrEqualTo(Loa::LOA_2), 'Loa 2 >= Loa 2');
        $this->assertTrue($loa->levelIsHigherOrEqualTo(Loa::LOA_SELF_VETTED), 'Loa 2 >= Loa 1.5');
        $this->assertTrue($loa->levelIsHigherOrEqualTo(Loa::LOA_1), 'Loa 2 >= Loa 1');
    }

    /**
     * @test
     * @group value
     */
    public function in_order_to_be_able_to_satisfy_a_loa_the_loa_must_have_a_level_higher_or_equal_to_the_other_level(): void
    {
        $loa1 = new Loa(Loa::LOA_1, '1');
        $loaSelfAsserted = new Loa(Loa::LOA_SELF_VETTED, 'loa_self_asserted');
        $loa2 = new Loa(Loa::LOA_2, '2');
        $loa3 = new Loa(Loa::LOA_3, '3');

        $this->assertFalse($loa2->canSatisfyLoa($loa3), 'Loa 2 cannot satisfy Loa 3');
        $this->assertTrue($loa2->canSatisfyLoa($loa2), 'Loa 2 can satisfy Loa 2');
        $this->assertTrue($loa2->canSatisfyLoa($loaSelfAsserted), 'Loa 2 can satisfy Loa 1.5');
        $this->assertTrue($loa2->canSatisfyLoa($loa1), 'Loa 2 can satisfy Loa 1');
    }

    /**
     * @test
     * @group value
     */
    public function it_can_check_whether_or_not_it_is_of_a_particuler_level(): void
    {
        $loa = new Loa(Loa::LOA_2, '2');

        $this->assertFalse($loa->isOfLevel(Loa::LOA_1), 'Loa 2 is not of level 1');
        $this->assertFalse($loa->isOfLevel(Loa::LOA_SELF_VETTED), 'Loa 2 is not of level 1.5');
        $this->assertTrue($loa->isOfLevel(Loa::LOA_2), 'Loa 2 is of level 2');
        $this->assertFalse($loa->isOfLevel(Loa::LOA_3), 'Loa 2 is not of level 3');
    }

    public function invalidLevelProvider(): array
    {
        return [
            'unknown level' => [4],
            'unknown level float' => [1.1],
        ];
    }

    public function wrongTypeLevelProvider(): array
    {
        return [
            'string' => ['a'],
            'object' => [new stdClass()],
        ];
    }
}
