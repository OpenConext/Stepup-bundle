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

namespace Surfnet\StepupBundle\Tests\Request;

use Hamcrest\Matchers;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Surfnet\StepupBundle\Exception\BadJsonRequestException;
use Surfnet\StepupBundle\Request\JsonConvertibleParamConverter;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JsonConvertibleParamConverterTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testItThrowsABadJsonRequestExceptionWhenTheParameterIsMissing()
    {
        $this->expectException(BadJsonRequestException::class);

        $request = $this->createJsonRequest((object) []);
        $validator = m::mock(ValidatorInterface::class);

        $paramConverter = new JsonConvertibleParamConverter($validator);
        $paramConverter->apply($request, new ParamConverter(['name' => 'parameter', 'class' => 'Irrelevant']));
    }

    public function testItThrowsABadJsonRequestExceptionWhenUnknownPropertiesAreSent()
    {
        $this->expectException(BadJsonRequestException::class);

        $validator = m::mock(ValidatorInterface::class)
            ->shouldReceive('validate')->andReturn(new ConstraintViolationList([]))
            ->getMock();

        $request = $this->createJsonRequest((object) ['foo' => ['unknown' => 'prop']]);
        $configuration = new ParamConverter(['name' => 'foo', 'class' => Foo::class]);

        $paramConverter = new JsonConvertibleParamConverter($validator);
        $paramConverter->apply($request, $configuration);
    }

    public function testItThrowsABadJsonRequestExceptionWithErrorsWhenTheConvertedObjectDoesntValidate()
    {
        $this->expectException(BadJsonRequestException::class);

        $validator = m::mock(ValidatorInterface::class)
            ->shouldReceive('validate')->once()->andReturn(
                m::mock(ConstraintViolationListInterface::class)
                    ->shouldReceive('count')->once()->andReturn(1)
                    ->shouldReceive('getIterator')->andReturn(new \ArrayIterator)
                    ->getMock()
            )
            ->getMock();


        $request = $this->createJsonRequest((object) ['foo' => ['bar' => '']]);
        $configuration = new ParamConverter(['name' => 'foo', 'class' => Foo::class]);

        $paramConverter = new JsonConvertibleParamConverter($validator);
        $paramConverter->apply($request, $configuration);
    }

    public function testItConvertsAParameter()
    {
        $validator = m::mock(ValidatorInterface::class)
            ->shouldReceive('validate')->andReturn(new ConstraintViolationList([]))
            ->getMock();

        $paramConverter = new JsonConvertibleParamConverter($validator);

        $foo = new Foo();
        $foo->bar = 'baz';
        $foo->camelCased = 'yeah';

        $request = $this->createJsonRequest((object) ['foo' => ['bar' => 'baz', 'camel_cased' => 'yeah']]);
        $request->attributes = m::mock(ParameterBag::class)
            ->shouldReceive('set')->once()->with('foo', Matchers::equalTo($foo))
            ->getMock();

        $configuration = new ParamConverter(['name' => 'foo', 'class' => Foo::class]);
        $paramConverter->apply($request, $configuration);
    }

    public function testItConvertsASnakeCasedParameter()
    {
        $validator = m::mock(ValidatorInterface::class)
            ->shouldReceive('validate')->andReturn(new ConstraintViolationList([]))
            ->getMock();

        $paramConverter = new JsonConvertibleParamConverter($validator);

        $foo = new Foo();
        $foo->bar = 'baz';
        $foo->camelCased = 'yeah';

        $request = $this->createJsonRequest((object) ['foo_bar' => ['bar' => 'baz', 'camel_cased' => 'yeah']]);
        $request->attributes = m::mock(ParameterBag::class)
            ->shouldReceive('set')->once()->with('fooBar', Matchers::equalTo($foo))
            ->getMock();

        $configuration = new ParamConverter(['name' => 'fooBar', 'class' => Foo::class]);
        $paramConverter->apply($request, $configuration);
    }

    /**
     * @param mixed $object
     * @return \Request
     */
    private function createJsonRequest($object)
    {
        $request = m::mock(Request::class)
            ->shouldReceive('getContent')->andReturn(json_encode($object))
            ->getMock();

        return $request;
    }
}
