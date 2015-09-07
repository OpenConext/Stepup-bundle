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

use Mockery as m;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Surfnet\StepupBundle\Request\JsonConvertibleParamConverter;
use Symfony\Component\Validator\ConstraintViolationList;

class JsonConvertibleParamConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testItThrowsABadJsonRequestExceptionWhenTheParameterIsMissing()
    {
        $this->setExpectedException('Surfnet\StepupBundle\Exception\BadJsonRequestException');

        $request = $this->createJsonRequest((object) []);
        $validator = m::mock('Symfony\Component\Validator\Validator\ValidatorInterface');

        $paramConverter = new JsonConvertibleParamConverter($validator);
        $paramConverter->apply($request, new ParamConverter(['name' => 'parameter', 'class' => 'Irrelevant']));
    }

    public function testItThrowsABadJsonRequestExceptionWhenUnknownPropertiesAreSent()
    {
        $this->setExpectedException('Surfnet\StepupBundle\Exception\BadJsonRequestException');

        $validator = $this->createValidatorMockThatValidates();
        $request = $this->createJsonRequest((object) ['foo' => ['unknown' => 'prop']]);
        $configuration = new ParamConverter(['name' => 'foo', 'class' => 'Surfnet\StepupBundle\Tests\Request\Foo']);

        $paramConverter = new JsonConvertibleParamConverter($validator);
        $paramConverter->apply($request, $configuration);
    }

    public function testItThrowsABadJsonRequestExceptionWithErrorsWhenTheConvertedObjectDoesntValidate()
    {
        $this->setExpectedException('Surfnet\StepupBundle\Exception\BadJsonRequestException');

        $validator = m::mock('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->shouldReceive('validate')->once()->andReturn(
                m::mock('Symfony\Component\Validator\ConstraintViolationListInterface')
                    ->shouldReceive('count')->once()->andReturn(1)
                    ->shouldReceive('getIterator')->andReturn(new \ArrayIterator)
                    ->getMock()
            )
            ->getMock();

        $request = $this->createJsonRequest((object) ['foo' => ['bar' => '']]);
        $configuration = new ParamConverter(['name' => 'foo', 'class' => 'Surfnet\StepupBundle\Tests\Request\Foo']);

        $paramConverter = new JsonConvertibleParamConverter($validator);
        $paramConverter->apply($request, $configuration);
    }

    public function testItConvertsAParameter()
    {
        $validator = $this->createValidatorMockThatValidates();
        $paramConverter = new JsonConvertibleParamConverter($validator);

        $foo = new Foo();
        $foo->bar = 'baz';
        $foo->camelCased = 'yeah';

        $request = $this->createJsonRequest((object) ['foo' => ['bar' => 'baz', 'camel_cased' => 'yeah']]);
        $request->attributes = m::mock('Symfony\Component\HttpFoundation\ParameterBag')
            ->shouldReceive('set')->once()->with('foo', m::anyOf($foo))
            ->getMock();

        $configuration = new ParamConverter(['name' => 'foo', 'class' => 'Surfnet\StepupBundle\Tests\Request\Foo']);
        $paramConverter->apply($request, $configuration);
    }

    public function testItConvertsASnakeCasedParameter()
    {
        $validator = $this->createValidatorMockThatValidates();
        $paramConverter = new JsonConvertibleParamConverter($validator);

        $foo = new Foo();
        $foo->bar = 'baz';
        $foo->camelCased = 'yeah';

        $request = $this->createJsonRequest((object) ['foo_bar' => ['bar' => 'baz', 'camel_cased' => 'yeah']]);
        $request->attributes = m::mock('Symfony\Component\HttpFoundation\ParameterBag')
            ->shouldReceive('set')->once()->with('fooBar', m::anyOf($foo))
            ->getMock();

        $configuration = new ParamConverter(['name' => 'fooBar', 'class' => 'Surfnet\StepupBundle\Tests\Request\Foo']);
        $paramConverter->apply($request, $configuration);
    }

    public function testItConvertsDeepObjectParameter()
    {
        $validator = $this->createValidatorMockThatValidates();
        $paramConverter = new JsonConvertibleParamConverter($validator);

        $foo = new Foo();
        $foo->bar = (object) [];
        $foo->bar->serpentumInHortumEst = 'verum';

        $request = $this->createJsonRequest((object) ['foo' => ['bar' => ['serpentum_in_hortum_est' => 'verum']]]);
        $request->attributes = m::mock('Symfony\Component\HttpFoundation\ParameterBag')
            ->shouldReceive('set')->once()->with('foo', m::anyOf($foo))
            ->getMock();

        $configuration = new ParamConverter(['name' => 'foo', 'class' => 'Surfnet\StepupBundle\Tests\Request\Foo']);
        $paramConverter->apply($request, $configuration);
    }

    /**
     * @param mixed $object
     * @return \Symfony\Component\HttpFoundation\Request
     */
    private function createJsonRequest($object)
    {
        $request = m::mock('Symfony\Component\HttpFoundation\Request')
            ->shouldReceive('getContent')->andReturn(json_encode($object))
            ->getMock();

        return $request;
    }

    /**
     * @return m\MockInterface
     */
    private function createValidatorMockThatValidates()
    {
        return m::mock('Symfony\Component\Validator\Validator\ValidatorInterface')
            ->shouldReceive('validate')->andReturn(new ConstraintViolationList([]))
            ->getMock();
    }
}
