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

namespace Surfnet\StepupBundle\Tests\Service;

use PHPUnit\Framework\TestCase as UnitTest;
use Surfnet\StepupBundle\Service\LoaResolutionService;
use Surfnet\StepupBundle\Value\Loa;

class LoaResolutionServiceTest extends UnitTest
{
    /**
     * @var \Surfnet\StepupBundle\Value\Loa[]
     */
    private $loas;

    public function setUp(): void
    {
        $providedLoas = $this->loaProvider();
        foreach ($providedLoas as $definition) {
            list($level, $identifier) = $definition;
            $this->loas[] = new Loa($level, $identifier);
        }
    }

    /**
     * @test
     * @group service
     * @dataProvider loaProvider
     *
     * @param int    $level
     * @param string $identifier
     */
    public function it_allows_to_get_the_correct_loa_by_identifier($level, $identifier)
    {
        $expectedLoa = new Loa($level, $identifier);
        $loaResolutionService = new LoaResolutionService($this->loas);

        $this->assertEquals($expectedLoa, $loaResolutionService->getLoa($identifier));
    }

    /**
     * @test
     * @group service
     */
    public function if_the_loa_definition_does_not_exist_null_is_returned()
    {
        $loaResolutionService = new LoaResolutionService($this->loas);

        $this->assertNull($loaResolutionService->getLoa('An unknown identifier'));
    }

    /**
     * @test
     * @group service
     * @dataProvider loaProvider
     *
     * @param int    $level
     * @param string $identifier
     */
    public function it_allows_to_get_the_correct_loa_by_the_loa_level($level, $identifier)
    {
        $expectedLoa = new Loa($level, $identifier);
        $loaResoltionService = new LoaResolutionService($this->loas);

        $this->assertEquals($expectedLoa, $loaResoltionService->getLoaByLevel($level));
    }

    /**
     * @test
     * @group service
     */
    public function if_the_loa_level_does_not_exist_null_is_returned()
    {
        $loaResolutionService = new LoaResolutionService($this->loas);

        $this->assertNull($loaResolutionService->getLoaByLevel(999));
    }

    public function loaProvider()
    {
        return [
            'Loa of Level 1' => [Loa::LOA_1, 'http://some.url.tld/authentication/loa1'],
            'Loa of Level 2' => [Loa::LOA_2, 'http://some.url.tld/authentication/loa2'],
            'Loa of Level 3' => [Loa::LOA_3, 'http://some.url.tld/authentication/loa3'],
        ];
    }
}
